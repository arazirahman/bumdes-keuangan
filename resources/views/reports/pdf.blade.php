<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .title { font-size: 16px; font-weight: bold; margin-bottom: 6px; }
    .muted { color: #555; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    .right { text-align: right; }
  </style>
</head>
<body>
  <div class="title">Laporan Keuangan SIMKEU BUMDes</div>
  <div class="muted">Periode: {{ $report['month'] }} ({{ $report['start'] }} s.d {{ $report['end'] }})</div>

  <table>
    <tr><th colspan="2">A. Laporan Laba Rugi</th></tr>
    <tr><td>Pendapatan</td><td class="right">Rp {{ number_format($report['pendapatan'],0,',','.') }}</td></tr>
    <tr><td>Biaya</td><td class="right">Rp {{ number_format($report['biaya'],0,',','.') }}</td></tr>
    <tr><td><b>Laba Bersih</b></td><td class="right"><b>Rp {{ number_format($report['laba_bersih'],0,',','.') }}</b></td></tr>
  </table>

  <table>
    <tr><th colspan="2">B. Laporan Arus Kas</th></tr>
    <tr><td>Uang Masuk</td><td class="right">Rp {{ number_format($report['uang_masuk'],0,',','.') }}</td></tr>
    <tr><td>Uang Keluar</td><td class="right">Rp {{ number_format($report['uang_keluar'],0,',','.') }}</td></tr>
    <tr><td><b>Saldo Akhir</b></td><td class="right"><b>Rp {{ number_format($report['saldo_akhir'],0,',','.') }}</b></td></tr>
  </table>

  <table>
    <tr><th colspan="2">C. Neraca Sederhana</th></tr>
    <tr><td>Kas</td><td class="right">Rp {{ number_format($report['kas'],0,',','.') }}</td></tr>
    <tr><td>Aset Lain</td><td class="right">Rp {{ number_format($report['aset_lain'],0,',','.') }}</td></tr>
    <tr><td><b>Total Aset</b></td><td class="right"><b>Rp {{ number_format($report['total_aset'],0,',','.') }}</b></td></tr>
    <tr><td>Modal</td><td class="right">Rp {{ number_format($report['modal'],0,',','.') }}</td></tr>
  </table>

  <div class="muted" style="font-size: 10px;">
    Catatan: Modal dihitung otomatis dari pemasukan kategori yang mengandung kata “Modal”. Aset lain = 0 (belum ada modul aset).
  </div>
</body>
</html>
