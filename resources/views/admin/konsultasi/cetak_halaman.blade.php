<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konsultasi Selesai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .info-cetak {
            margin: 20px 0;
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .hasil-konsultasi {
            max-width: 200px;
            word-wrap: break-word;
            font-size: 11px;
        }

        .durasi {
            font-family: monospace;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .header {
                margin-bottom: 20px;
            }

            table {
                font-size: 11px;
            }

            th,
            td {
                padding: 6px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Konsultasi Selesai</h1>
        <p>Sistem Pakar Penanganan Osteoporosis Berbasis Nutrisi (SPPOBN)</p>
    </div>

    <div class="info-cetak">
        <p><strong>Tanggal Cetak:</strong> {{ date('d F Y, H:i:s') }}</p>
        <p><strong>Total Data:</strong> {{ count($processedData) }} konsultasi</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">ID Konsultasi</th>
                <th style="width: 15%;">Username</th>
                <th style="width: 18%;">Tanggal Konsultasi</th>
                <th style="width: 15%;">Rentang Waktu</th>
                <th style="width: 10%;">Durasi</th>
                <th style="width: 27%;">Hasil Konsultasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($processedData as $index => $data)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $data['konsultasi_id'] }}</td>
                    <td>{{ $data['nama_pengguna'] }}</td>
                    <td>{{ $data['tanggal_konsultasi'] }}</td>
                    <td class="text-center">{{ $data['rentang_waktu'] }}</td>
                    <td class="text-center durasi">{{ $data['durasi'] }}</td>
                    <td class="hasil-konsultasi">
                        <?php
                        $hasilKonsultasi = $data['hasil_konsultasi'] ?: 'Tidak ada hasil konsultasi';
                        $processedHtml = '';
                        
                        // Cek apakah ada hasil konsultasi dan bukan string default 'Tidak ada hasil konsultasi'
                        if ($hasilKonsultasi !== 'Tidak ada hasil konsultasi' && !empty($hasilKonsultasi)) {
                            // Pecah string berdasarkan koma dan spasi setelahnya, lalu trim setiap item
                            $items = array_map('trim', explode(',', $hasilKonsultasi));
                        
                            // Buat daftar UL jika ada item
                            if (!empty($items)) {
                                $processedHtml .= '<ul>';
                                foreach ($items as $item) {
                                    // Pastikan item tidak kosong setelah di-trim
                                    if (!empty($item)) {
                                        $processedHtml .= '<li>' . htmlspecialchars($item) . '</li>';
                                    }
                                }
                                $processedHtml .= '</ul>';
                            } else {
                                // Jika setelah dipecah tidak ada item yang valid (misal: string hanya spasi atau koma)
                                $processedHtml = 'Tidak ada hasil konsultasi yang valid';
                            }
                        } else {
                            // Jika hasil konsultasi memang kosong atau string default
                            $processedHtml = 'Tidak ada hasil konsultasi';
                        }
                        ?>
                        {!! $processedHtml !!}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #666;">
                        Tidak ada data konsultasi yang dapat ditampilkan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d F Y, H:i:s') }} WIB</p>
        <p>Administrator SPPOBN</p>
    </div>

    <script>
        // Auto print when page loads (for print action)
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
