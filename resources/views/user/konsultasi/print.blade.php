<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Konsultasi #{{ $konsultasi->id }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-info,
        .riwayat-fakta,
        .hasil-konsultasi,
        .logika-inferensi {
            margin-bottom: 20px;
        }

        .user-info dt {
            font-weight: bold;
        }

        .riwayat-fakta table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .riwayat-fakta th,
        .riwayat-fakta td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .hasil-konsultasi .card {
            border: 1px solid #28a745;
            margin-top: 10px;
        }

        .hasil-konsultasi .card-header {
            background-color: #f0fff3;
            padding: 10px;
            border-bottom: 1px solid #28a745;
        }

        .hasil-konsultasi .card-body {
            padding: 15px;
        }

        .peringatan {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .logika-inferensi .accordion-item {
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }

        .logika-inferensi .accordion-header button {
            background-color: #eee;
            width: 100%;
            padding: 10px;
            border: none;
            text-align: left;
            cursor: pointer;
        }

        .logika-inferensi .accordion-body {
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Detail Konsultasi #{{ $konsultasi->id }}</h2>
            <p>Tanggal: {{ Carbon\Carbon::now()->isoFormat('DD MMMM YYYY HH:mm:ss') }}</p>
        </div>

        <div class="user-info">
            <h5>Informasi Pengguna</h5>
            <dl class="row">
                <dt class="col-sm-4">Nama</dt>
                <dd class="col-sm-8">: {{ $konsultasi->user->nama_lengkap }}</dd>
                <dt class="col-sm-4">Username</dt>
                <dd class="col-sm-8">: {{ $konsultasi->user->username }}</dd>
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">: {{ $konsultasi->user->email }}</dd>
                <dt class="col-sm-4">No. Telepon</dt>
                <dd class="col-sm-8">: {{ $konsultasi->user->no_telepon }}</dd>
                <dt class="col-sm-4">Tanggal Dibuat</dt>
                <dd class="col-sm-8">:
                    {{ Carbon\Carbon::parse($konsultasi->created_at)->isoFormat('DD MMMM YYYY HH:mm:ss') }}</dd>
                <dt class="col-sm-4">Tanggal Selesai</dt>
                <dd class="col-sm-8">:
                    {{ Carbon\Carbon::parse($konsultasi->completedAt)->isoFormat('DD MMMM YYYY HH:mm:ss') }}</dd>
            </dl>
        </div>
        <div class="inferensi-log">
            <h5>Fakta yang didapat :</h5>
            @if ($detailKonsultasi->isNotEmpty())
                <ul>
                    @foreach ($detailKonsultasi as $detail)
                        @if (strtolower($detail->jawaban) === 'ya' && $detail->fakta && $detail->fakta->deskripsi)
                            <li>{{ $detail->fakta->deskripsi }}</li>
                        @endif
                    @endforeach
                </ul>
                @if ($detailKonsultasi->filter(fn($d) => strtolower($d->jawaban) === 'ya')->isEmpty())
                    Tidak ada fakta dengan jawaban 'ya'.
                @endif
            @else
                Tidak ada fakta yang didapat.
            @endif
        </div>
        <div class="hasil-konsultasi" style="page-break-before: auto;">
            @if ($solusiAkhir)
                <div class="card">
                    <div class="card-header">
                        <h5>Hasil Konsultasi</h5>
                    </div>
                    <div class="card-body">
                        @if ($solusiAkhir->peringatan_konsultasi)
                            <p class="peringatan">Peringatan: {{ $solusiAkhir->peringatan_konsultasi }}</p>
                        @endif
                        <p>
                            {!! $solusiAkhir->deskripsi !!}
                        </p>
                        @if ($solusiAkhir->rekomendasiNutrisi->isNotEmpty())
                            <h6>Sumber Nutrisi Yang Bisa Didapat</h6>
                            <ul>
                                @foreach ($solusiAkhir->rekomendasiNutrisi as $rekomendasiNutrisi)
                                    <li>{{ $rekomendasiNutrisi->nutrisi }}
                                        @if ($rekomendasiNutrisi->sumberNutrisi->isNotEmpty())
                                            Sumber:
                                            @php
                                                $acakSumberNutrisi = $rekomendasiNutrisi->sumberNutrisi->shuffle();
                                            @endphp

                                            @foreach ($acakSumberNutrisi->take(3) as $sumber)
                                                {{ $sumber->nama_sumber }}{{ $loop->last ? '' : ', ' }}
                                            @endforeach

                                            @if ($rekomendasiNutrisi->sumberNutrisi->count() > 3)
                                                dan lainnya...
                                            @endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <small style="font-size: 12px; color: #999;">
                                <p><span class="text-danger">*</span>
                                    Untuk daftar sumber nutrisi lebih lengkap bisa di lihat di menu
                                    <a href="{{ route('edukasi.daftarMakanan') }}">daftar makanan</a>
                                </p>
                            </small>
                        @endif
                    </div>
                </div>
            @else
                <p>Hasil rekomendasi konsultasi belum tersedia.</p>
                <div class="hasil_konsultasi">
                    @php
                        $hasil = explode("\n\n", $konsultasi->hasil_konsultasi ?? '');
                    @endphp
                    @foreach ($hasil as $bagian)
                        @php
                            $baris = explode("\n", $bagian);
                        @endphp
                        @if (count($baris) > 0)
                            <div class="mb-3">
                                <strong>{{ array_shift($baris) }}</strong>
                                <ul class="mb-0">
                                    @foreach ($baris as $item)
                                        @if (trim($item) !== '')
                                            <li>{{ $item }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <footer style="margin-top: 60px; font-size: 12px; color: #999;">
        <p>Dicetak otomatis dari sistem pakar osteoporosis berbasis nutrisi. | {{ now()->format('d-m-Y H:i') }}</p>
    </footer>
    <script>
        window.onload = function() {
            window.print();
            // Setelah mencetak, kembali ke halaman detail (opsional)
            // window.onafterprint = function() {
            //     window.location.href = document.referrer;
            // }
        };
    </script>
</body>

</html>
