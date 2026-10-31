<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CashierSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
    }

    public function test_forgot_password_sends_notification(): void
    {
        Notification::fake();

        $user = User::firstOrCreate(
            ['email' => 'test_kasir@toko.com'],
            ['name' => 'Kasir Test', 'password' => bcrypt('secret123'), 'role' => 'kasir']
        );

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('success');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }

    public function test_cashier_transaction_creates_record_and_decrements_stock(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test@toko.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('secret123'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        $product = Product::create([
            'kode_barang' => 'TEST-'.uniqid(),
            'nama_barang' => 'Barang Uji Coba',
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'stok' => 20,
        ]);

        $response = $this->actingAs($admin)->post('/kasir/bayar', [
            // Nilai total dari browser sengaja dipalsukan; server harus menghitung ulang.
            'total_harga' => 1,
            'bayar' => 50000,
            'kembalian' => 30000,
            'produk_id' => [$product->id],
            'jumlah' => [2],
            'payment_method' => 'cash',
            'order_type' => 'dine_in',
        ]);

        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals(18, $product->stok);
        $transaction = Transaction::latest('id')->first();
        $this->assertEquals(22200, $transaction->total_harga);
        $this->assertEquals(27800, $transaction->kembalian);

        // Pastikan activity log tercatat
        $hasLog = ActivityLog::where('action', 'create_transaction')->exists();
        $this->assertTrue($hasLog);
    }

    public function test_public_registration_cannot_escalate_privileges(): void
    {
        $this->post('/register', [
            'name' => 'User Hack',
            'email' => 'hack@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'admin',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'hack@example.com']);
    }

    public function test_discount_is_applied_before_tax_and_notes_are_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $product = Product::create([
            'kode_barang' => 'DISC-'.uniqid(),
            'nama_barang' => 'Produk Diskon',
            'harga_beli' => 10000,
            'harga_jual' => 30000,
            'stok' => 5,
        ]);

        $this->actingAs($admin)->post('/kasir/bayar', [
            'bayar' => 27750,
            'produk_id' => [$product->id],
            'jumlah' => [1],
            'item_notes' => ['Tanpa sambal'],
            'customer_name' => 'Aldy',
            'notes' => 'Antar ke meja 4',
            'discount_type' => 'fixed',
            'discount_value' => 5000,
            'payment_method' => 'cash',
            'order_type' => 'dine_in',
        ])->assertSessionHas('success');

        $transaction = Transaction::latest('id')->first();
        $this->assertSame(5000.0, (float) $transaction->discount_amount);
        $this->assertSame(2750.0, (float) $transaction->tax_amount);
        $this->assertSame(27750.0, (float) $transaction->total_harga);
        $this->assertSame('Aldy', $transaction->customer_name);
        $this->assertSame('Antar ke meja 4', $transaction->notes);
        $this->assertSame('Tanpa sambal', $transaction->details()->first()->notes);
    }

    public function test_qris_requires_a_bank_or_wallet(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $product = Product::create([
            'kode_barang' => 'QRIS-'.uniqid(),
            'nama_barang' => 'Produk QRIS',
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'stok' => 5,
        ]);

        $this->actingAs($admin)->post('/kasir/bayar', [
            'bayar' => 11100,
            'produk_id' => [$product->id],
            'jumlah' => [1],
            'payment_method' => 'qris',
            'order_type' => 'takeaway',
        ])->assertSessionHasErrors('bank');
    }

    public function test_enabled_qris_transaction_shows_confirmation_and_qris_image(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $product = Product::create([
            'kode_barang' => 'QRIS-ENABLED-'.uniqid(),
            'nama_barang' => 'Produk QRIS Aktif',
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'stok' => 5,
        ]);
        StoreSetting::getSetting()->update([
            'qris_enabled' => true,
            'qris_image' => 'qris/toko.png',
        ]);

        $response = $this->actingAs($admin)->post('/kasir/bayar', [
            'bayar' => 11100,
            'produk_id' => [$product->id],
            'jumlah' => [1],
            'payment_method' => 'qris',
            'bank' => 'BCA',
            'order_type' => 'takeaway',
        ]);

        $response->assertSessionHas('qris_pending', true);
        $response->assertSessionHas('transaction_total', 11100);
        $response->assertRedirect(route('kasir.index'));
        $this->assertDatabaseCount('transactions', 0);
        $this->get(route('kasir.index'))
            ->assertSee('QRIS pembayaran')
            ->assertSee('Rp 11.100')
            ->assertSee('Konfirmasi Pembayaran QRIS')
            ->assertSee('Lanjut Transaksi (Konfirmasi dulu)');

        $this->actingAs($admin)->post(route('kasir.qris.confirm'))
            ->assertSessionHas('success')
            ->assertSessionHas('transaction_id')
            ->assertSessionHas('qris_confirmed_waiting', true);
        $this->assertDatabaseCount('transactions', 1);
        $this->get(route('kasir.index'))
            ->assertSee('Pembayaran QRIS Dikonfirmasi')
            ->assertSee('Lanjut Transaksi & Cetak Struk', false);
    }

    public function test_qris_can_be_cancelled_before_confirmation_and_changed_to_cash(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $product = Product::create([
            'kode_barang' => 'QRIS-CANCEL-'.uniqid(),
            'nama_barang' => 'Produk Batal QRIS',
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'stok' => 5,
        ]);
        StoreSetting::getSetting()->update(['qris_enabled' => true]);

        $this->actingAs($admin)->post('/kasir/bayar', [
            'bayar' => 11100,
            'produk_id' => [$product->id],
            'jumlah' => [1],
            'payment_method' => 'qris',
            'bank' => 'BCA',
        ])->assertSessionHas('qris_pending');

        $this->actingAs($admin)->post(route('kasir.qris.cancel'))
            ->assertRedirect(route('kasir.index'));
        $this->assertDatabaseCount('transactions', 0);

        $this->actingAs($admin)->post('/kasir/bayar', [
            'bayar' => 11100,
            'produk_id' => [$product->id],
            'jumlah' => [1],
            'payment_method' => 'cash',
        ])->assertSessionHas('success');
        $this->assertDatabaseCount('transactions', 1);
        $this->assertSame('cash', Transaction::latest('id')->value('payment_method'));
    }

    public function test_logout_redirects_and_prevents_back_access(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_sec@toko.com'],
            ['name' => 'Admin Security', 'password' => bcrypt('secret123'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        // Login dan akses admin
        $this->actingAs($admin);
        $response = $this->get('/admin');
        $response->assertStatus(200);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/login');

        // Pastikan setelah logout, mengakses halaman admin langsung di-redirect ke login
        $backResponse = $this->get('/admin');
        $backResponse->assertRedirect('/login');
    }

    public function test_deleted_product_code_is_reused_in_active_sequence(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $products = collect(range(1, 3))->map(fn (int $number) => Product::create([
            'kode_barang' => 'P'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
            'nama_barang' => 'Produk '.$number,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'stok' => 1,
        ]));

        $this->actingAs($admin)->delete(route('products.destroy', $products[1]->id))
            ->assertSessionHas('success');

        $this->actingAs($admin)->get(route('products.create'))
            ->assertSee('P002');

        $this->actingAs($admin)->post(route('products.store'), [
            'nama_barang' => 'Produk Baru',
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'stok' => 1,
        ])->assertSessionHas('success', 'Barang berhasil ditambahkan dengan kode: P002');

        $this->assertDatabaseHas('products', [
            'nama_barang' => 'Produk Baru',
            'kode_barang' => 'P002',
            'deleted_at' => null,
        ]);
    }

    public function test_product_can_be_edited_when_deleted_product_has_same_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $product = Product::create([
            'kode_barang' => 'P001',
            'nama_barang' => 'Produk Aktif',
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'stok' => 1,
        ]);
        Product::create([
            'kode_barang' => 'P001',
            'nama_barang' => 'Produk Yang Dihapus',
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'stok' => 1,
        ])->delete();

        $this->actingAs($admin)->put(route('products.update', $product->id), [
            'kode_barang' => 'P001',
            'nama_barang' => 'Nama Diperbarui',
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'stok' => 1,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nama_barang' => 'Nama Diperbarui',
        ]);
    }

    public function test_user_edit_displays_position_instead_of_database_id(): void
    {
        $placeholder = User::factory()->create(['role' => 'kasir', 'email_verified_at' => now()]);
        $placeholder->timestamps = false;
        $placeholder->update(['created_at' => Carbon::parse('2025-12-31 10:00:00')]);
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => Carbon::parse('2026-01-01 10:00:00'),
        ]);
        $user = User::factory()->create([
            'role' => 'kasir',
            'email_verified_at' => now(),
            'created_at' => Carbon::parse('2026-01-02 10:00:00'),
        ]);

        $this->actingAs($admin)->get(route('users.edit', $user->id))
            ->assertSee('value="#2"', false)
            ->assertDontSee('value="#'.$user->id.'"', false);
    }

    public function test_audit_logs_can_be_filtered_by_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'old_action',
            'description' => 'Aktivitas lama',
        ]);
        $oldLog = ActivityLog::latest('id')->first();
        DB::table('activity_logs')->where('id', $oldLog->id)
            ->update(['created_at' => '2026-10-29 10:00:00']);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'new_action',
            'description' => 'Aktivitas tanggal dipilih',
        ]);
        $newLog = ActivityLog::latest('id')->first();
        DB::table('activity_logs')->where('id', $newLog->id)
            ->update(['created_at' => '2026-10-30 10:00:00']);

        $this->actingAs($admin)->get(route('admin.activity-logs', ['date' => '2026-10-30']))
            ->assertSee('Aktivitas tanggal dipilih')
            ->assertDontSee('Aktivitas lama');
    }
}
