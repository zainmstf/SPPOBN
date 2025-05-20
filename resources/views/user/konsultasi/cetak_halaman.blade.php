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
        <table>
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:10%;">Tanggal Konsultasi</th>
                    <th style="width:10%;">Rentang Waktu</th>
                    <th style="width:10%;">Durasi</th>
                    <th style="width:5%;">Jumlah Fakta</th>
                    <th style="width:25%;">Hasil Konsultasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row[0] }}</td>
                        <td>{{ $row[1] }}</td>
                        <td>{{ $row[2] }}</td>
                        <td>{{ $row[3] }}</td>
                        <td>{{ $row[4] }}</td>
                        <?php
                        $originalString = $row[5];
                        $lines = explode("\n", $originalString);
                        $results = [];
                        foreach ($lines as $line) {
                            if (strpos($line, '- ') !== false) {
                                $results[] = trim(substr($line, strpos($line, '- ') + 2));
                            }
                        }
                        $processedString = implode(', ', $results);
                        ?>

                        <td>{{ $processedString }}</td>
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
