<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Dashboard Laporan Keuangan dengan statistik
     */
    public function dashboard()
    {
        // Statistik Umum
        $total_pendapatan = Transaction::sum('total_harga');
        $total_modal = TransactionDetail::sum(DB::raw('harga_beli * jumlah'));
        $total_keuntungan = $total_pendapatan - $total_modal;
        $total_transaksi = Transaction::count();

        // Transaksi Hari Ini
        $today_revenue = Transaction::whereDate('created_at', today())->sum('total_harga');
        $today_transactions = Transaction::whereDate('created_at', today())->count();

        // Breakdown Metode Pembayaran
        $cash_revenue = Transaction::where('payment_method', 'cash')->sum('total_harga');
        $qris_revenue = Transaction::where('payment_method', 'qris')->sum('total_harga');
        $cash_count = Transaction::where('payment_method', 'cash')->count();
        $qris_count = Transaction::where('payment_method', 'qris')->count();

        // Bank breakdown untuk QRIS
        $bank_breakdown = Transaction::where('payment_method', 'qris')
            ->selectRaw('bank, COUNT(*) as count, SUM(total_harga) as revenue')
            ->groupBy('bank')
            ->get();

        // Top 5 Produk
        $top_products = TransactionDetail::selectRaw('product_id, SUM(jumlah) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product')
            ->get();

        // Grafik 12 Bulan
        $monthly_revenue = Transaction::selectRaw('MONTH(created_at) as month, SUM(total_harga) as revenue')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('laporan.dashboard', compact(
            'total_pendapatan',
            'total_modal',
            'total_keuntungan',
            'total_transaksi',
            'today_revenue',
            'today_transactions',
            'cash_revenue',
            'qris_revenue',
            'cash_count',
            'qris_count',
            'bank_breakdown',
            'top_products',
            'monthly_revenue'
        ));
    }

    /**
     * Laporan Bulanan dengan detail per transaksi + Date Range Filter
     */
    public function monthly(Request $request)
    {
        $dates = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $start_date = $dates['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $dates['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // Fallback ke month/year jika ada di query string (backward compatibility)
        if ($request->has('month') || $request->has('year')) {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
            $start_date = "$year-".str_pad($month, 2, '0', STR_PAD_LEFT).'-01';
            $end_date = now()->parse("$year-$month-01")->endOfMonth()->format('Y-m-d');
        }

        // Query transaksi dengan date range
        $transactions = Transaction::with('user', 'details.product')
            ->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik
        $monthly_revenue = $transactions->sum('total_harga');
        $monthly_modal = $transactions->flatMap->details->sum(function ($d) {
            return $d->harga_beli * $d->jumlah;
        });
        $monthly_profit = $monthly_revenue - $monthly_modal;
        $monthly_transactions = $transactions->count();

        // Breakdown metode pembayaran
        $payment_breakdown = Transaction::whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_harga) as revenue')
            ->groupBy('payment_method')
            ->get();

        return view('laporan.monthly', compact(
            'transactions',
            'monthly_revenue',
            'monthly_modal',
            'monthly_profit',
            'monthly_transactions',
            'payment_breakdown',
            'start_date',
            'end_date'
        ));
    }

    /**
     * Edit Modal Transaksi (jika salah input)
     */
    public function edit($id)
    {
        $transaction = Transaction::with('details.product')->findOrFail($id);

        return view('laporan.edit', compact('transaction'));
    }

    /**
     * Update Transaksi
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Validasi
        $request->validate([
            'bayar' => 'required|numeric|min:'.$transaction->total_harga,
        ]);

        // Hitung ulang kembalian
        $kembalian = $request->bayar - $transaction->total_harga;

        $oldBayar = $transaction->bayar;
        $oldKembalian = $transaction->kembalian;

        // Update
        $transaction->update([
            'bayar' => $request->bayar,
            'kembalian' => $kembalian,
        ]);

        ActivityLog::record(
            action: 'update_transaksi',
            description: 'Admin '.auth()->user()->name." mengubah nilai bayar transaksi {$transaction->no_nota} dari Rp ".number_format($oldBayar, 0, ',', '.').' menjadi Rp '.number_format($request->bayar, 0, ',', '.').'.',
            model: $transaction,
            oldValues: ['bayar' => $oldBayar, 'kembalian' => $oldKembalian],
            newValues: ['bayar' => $request->bayar, 'kembalian' => $kembalian]
        );

        return redirect()->route('laporan.monthly')
            ->with('success', 'Transaksi berhasil diupdate!');
    }

    /**
     * Delete/Void Transaksi
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaction = Transaction::findOrFail($id);
                $details = TransactionDetail::where('transaction_id', $id)->get();

                // Kembalikan stok produk ke etalase
                foreach ($details as $d) {
                    $product = Product::lockForUpdate()->find($d->product_id);
                    if ($product) {
                        $product->increment('stok', $d->jumlah);
                    }
                    $d->delete();
                }

                $transaction->delete();

                ActivityLog::record(
                    action: 'void_transaksi',
                    description: 'Admin '.auth()->user()->name." membatalkan transaksi {$transaction->no_nota} senilai Rp ".number_format($transaction->total_harga, 0, ',', '.').' dan stok barang telah dikembalikan.',
                    model: $transaction
                );
            });

            return redirect()->back()
                ->with('success', 'Transaksi berhasil dibatalkan! Stok telah dikembalikan.');
        } catch (\Throwable $e) {
            Log::error('Gagal membatalkan transaksi laporan.', ['transaction_id' => $id, 'exception' => $e]);

            return redirect()->back()
                ->with('error', 'Gagal membatalkan transaksi. Silakan coba lagi.');
        }
    }

    /**
     * Export ke CSV/Excel dengan StreamedResponse standar Laravel
     */
    public function export(Request $request)
    {
        $dates = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $start_date = $dates['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $dates['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // Fallback untuk backward compatibility
        if ($request->has('month') || $request->has('year')) {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
            $start_date = "$year-".str_pad($month, 2, '0', STR_PAD_LEFT).'-01';
            $end_date = now()->parse("$year-$month-01")->endOfMonth()->format('Y-m-d');
        }

        $transactions = Transaction::with('user', 'details.product')
            ->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "laporan_keuangan_{$start_date}_sampai_{$end_date}.csv";

        ActivityLog::record(
            action: 'export_laporan',
            description: 'Admin '.auth()->user()->name." mengunduh export laporan transaksi periode {$start_date} s/d {$end_date}."
        );

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel membaca karakter dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            // Baris Header
            fputcsv($handle, ['No', 'No Nota', 'Tanggal', 'Kasir', 'Pelanggan', 'Metode', 'Total Penjualan', 'Uang Bayar', 'Kembalian']);

            // Baris Data
            foreach ($transactions as $index => $t) {
                fputcsv($handle, [
                    $index + 1,
                    $t->no_nota,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->user->name ?? '-',
                    $t->customer_name ?? '-',
                    strtoupper($t->payment_method).($t->bank ? ' ('.$t->bank.')' : ''),
                    $t->total_harga,
                    $t->bayar,
                    $t->kembalian,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
