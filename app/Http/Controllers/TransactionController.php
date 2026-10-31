<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::where('stok', '>', 0)->get();
        $setting = StoreSetting::getSetting();

        return view('kasir.index', compact('products', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bayar' => 'required|integer|min:0',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|integer|exists:products,id',
            'jumlah' => 'required|array|size:'.count($request->input('produk_id', [])),
            'jumlah.*' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'order_type' => 'nullable|in:dine_in,takeaway',
            'bank' => 'nullable|string|max:100|required_if:payment_method,qris',
            'bank_custom' => 'nullable|string|max:100',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string|max:500',
            'qris_confirmed' => 'nullable|boolean',
        ]);

        $setting = StoreSetting::getSetting();
        if ($validated['payment_method'] === 'qris' && ! $setting->qris_enabled) {
            return redirect()->route('kasir.index')
                ->withInput()
                ->withErrors(['payment_method' => 'Pembayaran QRIS sedang dinonaktifkan oleh admin.']);
        }

        if ($validated['payment_method'] === 'qris' && ! $request->boolean('qris_confirmed')) {
            try {
                $total = $this->calculateGrandTotal($validated);
                $request->session()->put('pending_qris_payment', $validated);

                return redirect()->route('kasir.index')
                    ->with('success', 'Menunggu konfirmasi pembayaran QRIS.')
                    ->with('qris_pending', true)
                    ->with('transaction_total', $total);
            } catch (\Throwable $e) {
                return redirect()->route('kasir.index')
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

        try {
            $transaction = DB::transaction(function () use ($request, $validated) {
                $products = Product::whereIn('id', $validated['produk_id'])
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $total = 0;
                $requestedQuantities = [];

                foreach ($validated['produk_id'] as $key => $productId) {
                    $product = $products->get($productId);
                    $qty = $validated['jumlah'][$key];

                    if (! $product) {
                        throw new \RuntimeException('Stok produk tidak mencukupi.');
                    }

                    $requestedQuantities[$productId] = ($requestedQuantities[$productId] ?? 0) + $qty;
                    $total += $product->harga_jual * $qty;
                }

                foreach ($requestedQuantities as $productId => $requestedQuantity) {
                    if ($products->get($productId)->stok < $requestedQuantity) {
                        throw new \RuntimeException('Stok produk tidak mencukupi.');
                    }
                }

                $discountType = $validated['discount_type'] ?? null;
                $discountValue = (float) ($validated['discount_value'] ?? 0);
                if ($discountType === 'percent' && $discountValue > 100) {
                    throw new \InvalidArgumentException('Diskon persentase maksimal 100%.');
                }
                $discountAmount = $discountType === 'percent'
                    ? min($total, round($total * min($discountValue, 100) / 100))
                    : min($total, round($discountValue));
                $taxableTotal = $total - $discountAmount;
                $taxRate = 11;
                $taxAmount = (int) round($taxableTotal * $taxRate / 100);
                $grandTotal = $taxableTotal + $taxAmount;

                if ($validated['bayar'] < $grandTotal) {
                    throw new \RuntimeException('Uang pembayaran kurang.');
                }

                $bank = $validated['payment_method'] === 'qris'
                    ? ($validated['bank'] === 'Lainnya' ? ($validated['bank_custom'] ?? null) : $validated['bank'])
                    : null;

                if ($validated['payment_method'] === 'qris' && blank($bank)) {
                    throw new \RuntimeException('Bank atau e-wallet QRIS wajib dipilih.');
                }

                // 1. Hitung nomor antrian dan no nota dengan locking untuk cegah race condition
                $today = now()->toDateString();
                // Jangan gunakan lockForUpdate() pada query agregat MAX(). MySQL
                // dapat menolak locking clause pada query agregat.
                $lastTransaction = Transaction::withTrashed()
                    ->whereDate('created_at', $today)
                    ->orderByDesc('queue_number')
                    ->lockForUpdate()
                    ->first(['queue_number']);

                $queueNumber = ($lastTransaction?->queue_number ?? 0) + 1;
                $noNota = 'TRX-'.now()->format('Ymd').'-'.str_pad($queueNumber, 3, '0', STR_PAD_LEFT);

                // 2. Buat transaksi master
                $trx = Transaction::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'no_nota' => $noNota,
                    'total_harga' => $grandTotal,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'bayar' => $validated['bayar'],
                    'kembalian' => $validated['bayar'] - $grandTotal,
                    'payment_method' => $request->payment_method,
                    'order_type' => $request->order_type ?? 'dine_in',
                    'bank' => $bank,
                    'notes' => $request->notes,
                    'queue_number' => $queueNumber,
                ]);

                // 3. Validasi stok produk & kurangi stok secara atomic
                foreach ($validated['produk_id'] as $key => $productId) {
                    $qty = $validated['jumlah'][$key];
                    // Kunci baris produk saat transaksi berlangsung (pessimistic lock)
                    $product = $products->get($productId);

                    $subtotal = $product->harga_jual * $qty;
                    $itemNote = isset($validated['item_notes'][$key]) && trim($validated['item_notes'][$key]) !== ''
                        ? trim($validated['item_notes'][$key])
                        : null;

                    TransactionDetail::create([
                        'transaction_id' => $trx->id,
                        'product_id' => $product->id,
                        'jumlah' => $qty,
                        'harga_satuan' => $product->harga_jual,
                        'harga_beli' => $product->harga_beli,
                        'subtotal' => $subtotal,
                        'notes' => $itemNote,
                    ]);

                    // Atomic decrement
                    $product->decrement('stok', $qty);
                }

                // 4. Catat Audit Trail
                ActivityLog::record(
                    action: 'create_transaction',
                    description: "Transaksi {$trx->no_nota} senilai Rp ".number_format($trx->total_harga, 0, ',', '.').' ('.strtoupper($trx->payment_method).') berhasil diproses oleh '.auth()->user()->name.'.',
                    model: $trx
                );

                return $trx;
            });

            return redirect()->route('kasir.index')
                ->with('success', 'Pembayaran Berhasil! Transaksi telah disimpan.')
                ->with('transaction_id', $transaction->id)
                ->with('qris_confirmed_waiting', $transaction->payment_method === 'qris');

        } catch (\Throwable $e) {
            Log::error('Gagal memproses transaksi.', [
                'user_id' => auth()->id(),
                'product_ids' => $request->input('produk_id', []),
                'payment_method' => $request->input('payment_method'),
                'exception' => $e,
            ]);

            $message = match (true) {
                $e instanceof \RuntimeException && str_contains($e->getMessage(), 'Stok') => 'Stok produk tidak mencukupi.',
                $e instanceof \RuntimeException && str_contains($e->getMessage(), 'pembayaran') => 'Uang pembayaran masih kurang.',
                $e instanceof \InvalidArgumentException => 'Nilai diskon tidak valid.',
                $e instanceof QueryException => 'Database belum siap untuk fitur terbaru. Hubungi admin untuk menjalankan migration.',
                default => 'Transaksi gagal diproses. Periksa data pembayaran lalu coba lagi.',
            };

            return redirect()->route('kasir.index')->with('error', $message);
        }
    }

    public function confirmQris(Request $request)
    {
        $pending = $request->session()->get('pending_qris_payment');

        if (! is_array($pending)) {
            return redirect()->route('kasir.index')
                ->with('error', 'Pembayaran QRIS tidak ditemukan atau sudah dibatalkan.');
        }

        $pending['qris_confirmed'] = true;
        $request->merge($pending);

        $response = $this->store($request);
        if ($response->isRedirect() && ! $request->session()->has('error')) {
            $request->session()->forget('pending_qris_payment');
        }

        return $response;
    }

    public function cancelQris(Request $request)
    {
        $pending = $request->session()->pull('pending_qris_payment');
        $restore = null;

        if (is_array($pending)) {
            $products = Product::whereIn('id', $pending['produk_id'])->get()->keyBy('id');
            $restore = [
                'keranjang' => collect($pending['produk_id'])->map(function ($productId, $key) use ($pending, $products) {
                    $product = $products->get($productId);

                    return [
                        'id' => $product->id,
                        'nama' => $product->nama_barang,
                        'harga' => $product->harga_jual,
                        'stok' => $product->stok,
                        'qty' => $pending['jumlah'][$key],
                        'notes' => $pending['item_notes'][$key] ?? '',
                    ];
                })->values()->all(),
                'customerName' => $pending['customer_name'] ?? '',
                'notes' => $pending['notes'] ?? '',
                'orderType' => $pending['order_type'] ?? 'dine_in',
                'discountType' => $pending['discount_type'] ?? '',
                'discountValue' => $pending['discount_value'] ?? 0,
            ];
        }

        $redirect = redirect()->route('kasir.index');
        if ($restore) {
            $redirect->with('restore_qris_payment', $restore);
        }

        return $redirect;
    }

    private function calculateGrandTotal(array $validated): int
    {
        $products = Product::whereIn('id', $validated['produk_id'])->get()->keyBy('id');
        $total = 0;

        foreach ($validated['produk_id'] as $key => $productId) {
            $product = $products->get($productId);
            $qty = $validated['jumlah'][$key];

            if (! $product || $product->stok < $qty) {
                throw new \RuntimeException('Stok produk tidak mencukupi.');
            }

            $total += $product->harga_jual * $qty;
        }

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = (float) ($validated['discount_value'] ?? 0);
        if ($discountType === 'percent' && $discountValue > 100) {
            throw new \InvalidArgumentException('Diskon persentase maksimal 100%.');
        }

        $discountAmount = $discountType === 'percent'
            ? min($total, round($total * min($discountValue, 100) / 100))
            : min($total, round($discountValue));
        $taxableTotal = $total - $discountAmount;

        return (int) ($taxableTotal + round($taxableTotal * 11 / 100));
    }

    // --- CETAK STRUK ---
    public function struk($id)
    {
        $transaction = Transaction::findOrFail($id);
        $setting = StoreSetting::getSetting();

        $details = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
            ->where('transaction_id', $id)
            ->select('transaction_details.*', 'products.nama_barang')
            ->get();

        return view('kasir.struk', compact('transaction', 'details', 'setting'));
    }

    // --- RIWAYAT TRANSAKSI PENJUALAN ---
    public function history(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $filteredTransactions = Transaction::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('no_nota', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhereDate('created_at', $search);
                });
            });
        $transactions = (clone $filteredTransactions)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $total_pendapatan = $filteredTransactions->sum('total_harga');

        return view('kasir.history', compact('transactions', 'total_pendapatan'));
    }

    // --- VOID TRANSAKSI (BATALKAN TRANSAKSI & RESTORE STOK) ---
    public function destroyLaporan($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaction = Transaction::findOrFail($id);
                $details = TransactionDetail::where('transaction_id', $id)->get();

                // Kembalikan stok produk
                foreach ($details as $d) {
                    $product = Product::lockForUpdate()->find($d->product_id);
                    if ($product) {
                        $product->increment('stok', $d->jumlah);
                    }
                    $d->delete(); // Soft delete detail transaksi
                }

                // Soft delete transaksi master
                $transaction->delete();

                // Catat ke audit log
                ActivityLog::record(
                    action: 'void_transaction',
                    description: "Transaksi {$transaction->no_nota} senilai Rp ".number_format($transaction->total_harga, 0, ',', '.').' dibatalkan (void) oleh '.auth()->user()->name.'. Stok telah dikembalikan.',
                    model: $transaction
                );
            });

            return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan! Stok barang telah dikembalikan.');
        } catch (\Throwable $e) {
            Log::error('Gagal membatalkan transaksi.', ['transaction_id' => $id, 'exception' => $e]);

            return redirect()->back()->with('error', 'Gagal membatalkan transaksi. Silakan coba lagi.');
        }
    }
}
