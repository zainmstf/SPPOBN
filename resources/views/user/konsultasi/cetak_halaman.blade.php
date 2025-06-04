<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Konsultasi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 40px;
            color: #333;
        }

        h1,
        h2 {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 8px 12px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-print {
            padding: 8px 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button onclick="window.print()" class="btn-print">🖨️ Cetak Laporan</button>

    <h1>Laporan Konsultasi</h1>
    <p><strong>Dicetak oleh:</strong> {{ $user->nama_lengkap ?? 'Admin' }}</p>
    <p><strong>Waktu Cetak:</strong> {{ now()->format('d-m-Y H:i') }}</p>

    <div class="section">
        <h2>Data Konsultasi</h2>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="width:5%; border: 1px solid #ddd; padding: 8px; text-align: left;">No</th>
                    <th style="width:12%; border: 1px solid #ddd; padding: 8px; text-align: left;">Tanggal Konsultasi
                    </th>
                    <th style="width:12%; border: 1px solid #ddd; padding: 8px; text-align: left;">Rentang Waktu</th>
                    <th style="width:8%; border: 1px solid #ddd; padding: 8px; text-align: left;">Durasi</th>
                    <th style="width:8%; border: 1px solid #ddd; padding: 8px; text-align: left;">Jumlah Fakta</th>
                    <th style="width:55%; border: 1px solid #ddd; padding: 8px; text-align: left;">Hasil Konsultasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row[0] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row[1] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row[2] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row[3] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $row[4] }}</td>
                        <?php
                        $originalString = $row[5];
                        $processedHtml = '';
                        
                        // Regex untuk menemukan pola **Judul**: Deskripsi
                        // Ini akan mencari teks tebal (**) diikuti titik dua, lalu teks sisanya.
                        // Flag 'm' untuk multiple lines, 'U' untuk non-greedy matching
                        preg_match_all('/\*\*(.*?)\*\*:\s*(.*?)(?=\*\*|$)/s', $originalString, $matches, PREG_SET_ORDER);
                        
                        if (!empty($matches)) {
                            $processedHtml .= '<ul>';
                            foreach ($matches as $match) {
                                $title = trim($match[1]); // Judul tanpa **
                                $description = trim($match[2]); // Deskripsi
                                // Hapus newline atau spasi berlebih di akhir deskripsi
                                $description = rtrim($description, "\n ");
                        
                                // Untuk tampilan di tabel, gabungkan judul dan deskripsi
                                $processedHtml .= '<li><strong>' . $title . '</strong>: ' . $description . '</li>';
                            }
                            $processedHtml .= '</ul>';
                        } else {
                            // Jika tidak ada pola yang cocok, tampilkan string aslinya
                            $processedHtml = nl2br($originalString);
                        }
                        ?>
                        <td style="border: 1px solid #ddd; padding: 8px;">{!! $processedHtml !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <footer style="margin-top: 60px; font-size: 12px; color: #999;">
        <p>Laporan ini dicetak dari sistem pakar osteoporosis. | {{ now()->format('d-m-Y H:i') }}</p>
    </footer>

    <script>
        window.onload = function() {
            // Tambahkan media query untuk mengatur gaya cetak
            const style = document.createElement('style');
            style.setAttribute('type', 'text/css');
            style.innerHTML = `
      @media print {
        @page {
          size: landscape;
          margin: 1cm; /* Atur margin sesuai kebutuhan */
        }
        body {
          zoom: 0.75; /* Untuk skala 75% */
          -moz-transform: scale(0.75); /* Untuk Firefox */
          -moz-transform-origin: 0 0;
          transform-origin: 0 0;
        }
      }
    `;
            document.head.appendChild(style);
            window.print();
        };
    </script>
</body>

</html>
