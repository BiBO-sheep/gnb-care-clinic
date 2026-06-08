<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Klinik</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0B2B5E; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0B2B5E; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { w-full; border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #0B2B5E; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #E6F0FA; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>G&B Care Clinic</h1>
        <p>Laporan Keuangan Transaksi Lunas</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Pasien</th>
                <th>Poli</th>
                <th>Tgl Lunas</th>
                <th class="text-right">Jasa Dokter</th>
                <th class="text-right">Obat-obatan</th>
                <th class="text-right">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td>INV-{{ $inv->id }}</td>
                <td>{{ $inv->user->name ?? '-' }}</td>
                <td>{{ $inv->appointment->poli->nama ?? '-' }}</td>
                <td>{{ $inv->updated_at->format('d M Y') }}</td>
                <td class="text-right">Rp {{ number_format($inv->total_consultation, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($inv->total_medicines, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada transaksi lunas.</td>
            </tr>
            @endforelse
        </tbody>
        @if($invoices->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($invoices->sum('total_consultation'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($invoices->sum('total_medicines'), 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($invoices->sum('grand_total'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
