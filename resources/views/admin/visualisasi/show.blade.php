@extends('layouts.admin')

@section('title', 'Alur Pertanyaan | SPPOBN')
@section('title-menu', 'Visualisasi Alur Pertanyaan')
@section('subtitle-menu', 'Menampilkan Alur Pertanyaan')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Alur Pertanyaan</li>
@endsection

@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="mb-0">Alur Pertanyaan: {{ ucwords(str_replace('_', ' ', $kategori))}}</h4>
                        <a href="{{ route('admin.basisPengetahuan.visualisasi.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Diagram berikut menunjukkan alur pertanyaan berdasarkan
                            jawaban
                            Ya/Tidak
                        </div>

                        <div class="mermaid">
                            {!! $mermaidScript !!}
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-outline-primary" onclick="downloadDiagram()">
                                <i class="bi bi-download"></i> Unduh Diagram
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Daftar Pertanyaan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pertanyaan</th>
                                    <th>Ya →</th>
                                    <th>Tidak →</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $q)
                                    <tr>
                                        <td>{{ $q->kode }}</td>
                                        <td>{{ $q->pertanyaan }}</td>
                                        <td>{{ $q->next_if_yes ?? 'Sesi Selesai' }}</td>
                                        <td>{{ $q->next_if_no ?? 'Sesi Selesai' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts-bottom')
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true,
                curve: 'basis'
            }
        });

        function downloadDiagram() {
            const svg = document.querySelector('.mermaid svg');
            if (!svg) return;

            const serializer = new XMLSerializer();
            const svgStr = serializer.serializeToString(svg);

            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const img = new Image();

            img.onload = function () {
                canvas.width = svg.width.baseVal.value;
                canvas.height = svg.height.baseVal.value;
                ctx.drawImage(img, 0, 0);

                const a = document.createElement("a");
                a.download = "flowchart-{{ $kategori }}.png";
                a.href = canvas.toDataURL("image/png");
                a.click();
            };

            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgStr)));
        }
    </script>
@endpush
@push('css-top')
    <style>
        .mermaid {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            overflow: auto;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        .mermaid .node rect {
            fill: #f8f9fa;
            stroke: #0d6efd;
            stroke-width: 2px;
            rx: 5;
            ry: 5;
        }

        .mermaid .edgePath path {
            stroke: #6c757d;
            stroke-width: 2px;
            fill: none;
        }

        .mermaid .edgeLabel {
            background-color: #e9ecef;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
@endpush