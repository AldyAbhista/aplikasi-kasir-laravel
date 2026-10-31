<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $transaction->no_nota }}</title>
    <style>
        /* CSS Khusus Printer Thermal */
        * { 
            box-sizing: border-box; 
        }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; 
            margin: 0; 
            padding: 0; 
            background-color: #f0f0f0; 
        }
        .struk { 
            width: 80mm; 
            max-width: 80mm; 
            padding: 4mm; 
            margin: 20px auto; 
            background: #fff; 
            border: 1px solid #ccc; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            overflow-wrap: anywhere; 
        }
        .header, .footer { 
            text-align: center; 
            margin-bottom: 10px; 
        }
        .divider { 
            border-bottom: 1px dashed #000; 
            margin: 5px 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        td { 
            vertical-align: top; 
            padding: 2px 0; 
        }
        .text-right { 
            text-align: right; 
        }
        
        /* Pengaturan saat diprint ke kertas */
        @media print {
            @page { 
                size: 80mm auto; 
                margin: 0; /* Menghilangkan header & footer browser */
            }
            body { 
                background-color: #fff; 
                margin: 0;
            }
            .struk { 
                width: 80mm; 
                max-width: 80mm; 
                border: none; 
                box-shadow: none; 
                margin: 0; 
                padding: 4mm; 
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php
        $st = $setting ?? \App\Models\StoreSetting::getSetting();
    @endphp
    <div class="struk">
        <div class="header">
            <h3 style="margin-bottom:5px; font-size: 14px;">{{ strtoupper($st->store_name ?? 'KASIR KAFE') }}</h3>
            <p style="font-size: 11px; margin-top: 0;">
                {{ $st->address ?? 'Jl. Contoh Alamat No. 123' }}<br>
                @if($st->phone) Telp: {{ $st->phone }} @endif
            </p>
        </div>
        
        <div class="divider"></div>
        <p style="font-size: 11px; margin: 5px 0; line-height: 1.4;">
            No. Nota : {{ $transaction->no_nota }}<br>
            Antrian  : #{{ $transaction->queue_number ?? '-' }}<br>
            Pembeli  : {{ $transaction->customer_name ?? '-' }}<br>
            Tipe     : {{ ($transaction->order_type ?? 'dine_in') === 'takeaway' ? 'TAKEAWAY' : 'DINE IN' }}<br>
            Tanggal  : {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
            Kasir    : {{ $transaction->user->name ?? 'N/A' }}
        </p>
        <div class="divider"></div>
        
        @if($transaction->notes)
        <p style="font-size: 11px; margin: 5px 0; font-weight: bold;">
            CATATAN:<br>
            {{ $transaction->notes }}
        </p>
        <div class="divider"></div>
        @endif

        <table>
            @foreach($details as $d)
            <tr>
                <td colspan="3" style="padding-bottom: 0; font-weight: bold;">{{ $d->nama_barang }}</td>
            </tr>
            @if(!empty($d->notes))
            <tr>
                <td colspan="3" style="font-size: 10px; color: #333; font-style: italic; padding: 1px 0 2px 8px;">
                    * {{ $d->notes }}
                </td>
            </tr>
            @endif
            <tr>
                <td style="width: 30%;">{{ $d->jumlah }} x</td>
                <td style="width: 35%;">{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right" style="width: 35%;">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="divider"></div>
        
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($transaction->details->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            @if(($transaction->discount_amount ?? 0) > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if(($transaction->tax_amount ?? 0) > 0)
            <tr>
                <td>Pajak</td>
                <td class="text-right">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>
                    @if($transaction->payment_method === 'cash')
                        Tunai
                    @else
                        QRIS {{ $transaction->bank ? '('.$transaction->bank.')' : '' }}
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>
        
        <div class="footer">
            <p style="font-size: 10px; margin: 0;">{!! nl2br(e($st->footer_message ?? 'Terima Kasih! Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.')) !!}</p>
        </div>
    </div>

    <script>
        // Otomatis tutup jendela/tab setelah dialog print ditutup atau selesai diprint
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>