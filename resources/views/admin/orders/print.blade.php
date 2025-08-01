<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .receipt {
            width: 300px; /* Lebar umum 80mm */
            margin: 0 auto;
            padding: 15px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .details {
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        .details p {
            margin: 2px 0;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
        }
        .details p strong {
            flex-basis: 45%;
            text-align: left;
        }
        .details p span {
            flex-basis: 55%;
            text-align: right;
        }
        .items {
            margin-top: 15px;
        }
        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            padding: 5px 0;
            vertical-align: top;
        }
        thead th {
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        tbody tr:not(:last-child) td {
            padding-bottom: 8px;
        }
        .item-col { text-align: left; width: 50%; }
        .qty-col { text-align: center; width: 15%; }
        .price-col { text-align: right; width: 35%; }
        .notes {
            font-size: 10px;
            font-style: italic;
            color: #555;
            padding-top: 2px;
            padding-left: 10px;
            text-align: left;
        }
        .total-item-row td,
        .total-row td {
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        /* Print specific styles */
        @media print {
            /* Aturan ini sangat penting untuk kertas resi kecil */
            @page {
                size: 80mm auto; /* Lebar 80mm, tinggi otomatis */
                margin: 0;
            }
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            .receipt {
                width: 100%;
                margin: 0;
                padding: 5mm; /* Sesuaikan padding cetak jika perlu */
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Warung Tekko Ramenten10 Lelabuan</h1>
            <p>Jalan S. Parman No. 2, Benua Melayu Darat, Pontianak</p>
            <p>Telp: 0811-826-008</p>
            <p>===============================</p>
            <p>STRUK PESANAN</p>
        </div>

        <div class="details">
            <p><strong>No. Pesanan:</strong> <span>#{{ $order->order_number }}</span></p>
            <p><strong>Meja:</strong> <span>{{ $order->table_number }}</span></p>
            <p><strong>Pelanggan:</strong> <span>{{ $order->customer_name }}</span></p>
            <p><strong>Waktu:</strong> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th class="item-col">Item</th>
                        <th class="qty-col">Qty</th>
                        <th class="price-col">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td class="item-col">{{ $item->product->name }}</td>
                        <td class="qty-col">{{ $item->quantity }}</td>
                        <td class="price-col">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @if($item->notes)
                    <tr>
                        <td colspan="3" class="notes">Catatan: {{ $item->notes }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-item-row">
                        <td colspan="2" class="item-col">Total Item</td>
                        <td class="qty-col">{{ $order->orderItems->sum('quantity') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2" class="item-col">TOTAL</td>
                        <td class="price-col">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            <p>TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
            <p>Dicetak: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cetak Lagi</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>
</body>
</html>