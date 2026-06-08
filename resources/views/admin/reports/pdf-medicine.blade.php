<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Obat Keluar</title>
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
    </style>
</head>
<body>

    <div class="header">
        <h1>G&B Care Clinic</h1>
        <p>Laporan Rekap Obat Keluar Terjual</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Keluar</th>
                <th>Nama Obat</th>
                <th>Dosis / Jumlah</th>
                <th>Pasien</th>
                <th class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicines as $index => $med)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $med->created_at->format('d M Y') }}</td>
                <td class="font-bold">{{ $med->medicine_name }}</td>
                <td>{{ $med->dosage }}</td>
                <td>{{ $med->medicalRecord->appointment->user->name ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($med->price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data obat keluar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
