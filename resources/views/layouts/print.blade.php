<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')

    <style>
        /* Additional print styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            body {
                font-size: 12pt;
                line-height: 1.5;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            a {
                text-decoration: none !important;
                color: #000 !important;
            }

            .table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            .badge {
                border: 1px solid #000 !important;
                color: #000 !important;
                background-color: transparent !important;
            }
        }
    </style>
</head>

<body>
    @yield('content')

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);

            // Optional: Close window after print
            window.onafterprint = function() {
                window.close();
            };
        };

        // Fallback for browsers that don't support onafterprint
        setTimeout(function() {
            window.close();
        }, 1000);
    </script>
</body>

</html>
