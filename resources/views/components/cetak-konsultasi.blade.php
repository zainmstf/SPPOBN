<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Konsultasi #{{ $konsultasi->konsultasiID }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #000;
        }

        h2,
        h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table th,
        table td {
            text-align: start;
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        ol {
            padding-left: 20px;
        }

        .small-text {
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>
    <h2>SISTEM PAKAR PENANGANAN OSTEOPOROSIS</h2>
    <h3>Detail Konsultasi #{{ $konsultasi->konsultasiID }}</h3>

    <div class="section-title">Informasi Pengguna</div>
    <table>
        <tr>
            <th>Nama</th>
            <td>{{ $konsultasi->user->nama }}</td>
        </tr>
        <tr>
            <th>Username</th>
            <td>{{ $konsultasi->user->username }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $konsultasi->user->alamat }}</td>
        </tr>
        <tr>
            <th>No. Telepon</th>
            <td>{{ $konsultasi->user->no_telp }}</td>
        </tr>
        <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ Carbon\Carbon::parse($konsultasi->created_at)->isoFormat('DD MMMM YYYY HH:mm:ss') }}</td>
        </tr>
        <tr>
            <th>Tanggal Selesai</th>
            <td>{{ Carbon\Carbon::parse($konsultasi->completedAt)->isoFormat('DD MMMM YYYY HH:mm:ss') }}</td>
        </tr>
    </table>

    <div class="section-title">Riwayat Jawaban</div>
    @foreach (['risiko_osteoporosis', 'asupan_nutrisi', 'preferensi_makanan'] as $kategori)
        <h4>{{ ucwords(str_replace('_', ' ', $kategori)) }}</h4>
        <table>
            <thead>
                <tr>
                    <th style="width:10%;">Kode</th>
                    <th style="width:50%;">Pertanyaan</th>
                    <th style="width:20%;">Jawaban</th>
                    <th style="width:20%;">Dijawab pada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($konsultasi->konsultasiJawaban->filter(fn($item) => $item->pertanyaan->kategori === $kategori) as $jawaban)
                    <tr>
                        <td>{{ $jawaban->pertanyaan->kodePertanyaan }}</td>
                        <td>{!! nl2br(e($jawaban->pertanyaan->teksPertanyaan)) !!}</td>
                        <td>{{ $jawaban->formattedJawaban }}</td>
                        <td>{{ Carbon\Carbon::parse($jawaban->answeredAt)->isoFormat('DD MMMM YYYY HH:mm:ss') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="section-title">Hasil Rekomendasi</div>
    @foreach ($konsultasi->rekomendasies as $rekomendasi)
        @php $aturanData = $aturan[$rekomendasi->rekomendasiID] ?? null; @endphp
        <table>
            <tr>
                <th style="width:25%;">Jenis Rekomendasi</th>
                <td>{{ ucwords(str_replace('_', ' ', $rekomendasi->jenisRekomendasi)) }}</td>
            </tr>
            <tr>
                <th>Kode Aturan</th>
                <td>{{ $aturanData->kodeAturan ?? 'Tidak Ditemukan' }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $aturanData->deskripsi ?? 'Aturan tidak tersedia' }}</td>
            </tr>
            <tr>
                <th>Rekomendasi</th>
                <td>
                    <ol>
                        @foreach (explode("\n", $rekomendasi->teksRekomendasi) as $line)
                            @if (trim($line) !== '')
                                <li>{{ trim($line) }}</li>
                            @endif
                        @endforeach
                    </ol>
                </td>
            </tr>
        </table>
    @endforeach

    <div class="small-text">Dicetak pada: {{ now()->isoFormat('DD MMMM YYYY HH:mm') }}</div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
