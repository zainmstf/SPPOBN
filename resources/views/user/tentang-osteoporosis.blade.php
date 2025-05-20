@extends('layouts.user')
@section('title', 'Tentang Osteoporosis | SPPOBN')
@section('title-menu', 'Tentang Osteoporosis')
@section('subtitle-menu', 'Kenali penyebab, gejala, dan cara pencegahannya')

@section('breadcumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tentang Osteoporosis</li>
@endsection
@section('content')
    <x-page-header />
    <div class="page-content">
        <div class="card h-100">
            <div class="card-content">
                <img class="card-img-top img-fluid" src="{{ asset('storage/img/osteoporosis/osteoporosis.jpg') }}"
                    alt="Ilustrasi Osteoporosis">
                <div class="card-body">
                    <h2 class="card-title">Apa Itu Osteoporosis?</h2>
                    <p class="card-text">
                        Osteoporosis adalah penyakit tulang progresif yang ditandai dengan penurunan kepadatan tulang dan
                        kerusakan mikroarsitektur tulang, yang menyebabkan peningkatan kerapuhan tulang dan kerentanan
                        terhadap patah tulang. Kondisi ini sering disebut sebagai "penyakit diam-diam" karena kehilangan
                        tulang terjadi tanpa gejala.
                    </p>

                    <h3 class="card-title">Jenis-Jenis Osteoporosis</h3>
                    <ul class="list-unstyled">
                        <li><strong>Osteoporosis Primer:</strong> Terkait dengan penuaan dan penurunan hormon, terutama pada
                            wanita pasca menopause.</li>
                        <li><strong>Osteoporosis Sekunder:</strong> Disebabkan oleh kondisi medis lain atau penggunaan
                            obat-obatan tertentu.</li>
                    </ul>

                    <h2 class="card-title">Penyebab dan Faktor Risiko</h2>
                    <p class="card-text">
                        Beberapa faktor dapat meningkatkan risiko terkena osteoporosis:
                    </p>
                    <ul class="list-unstyled">
                        <li><strong>Usia:</strong> Risiko meningkat seiring bertambahnya usia, terutama setelah 50 tahun.
                        </li>
                        <li><strong>Jenis Kelamin:</strong> Wanita lebih rentan, terutama setelah menopause karena penurunan
                            hormon estrogen.</li>
                        <li><strong>Genetika:</strong> Riwayat keluarga dengan osteoporosis meningkatkan risiko.</li>
                        <li><strong>Ukuran Tubuh:</strong> Orang dengan tubuh kecil dan kurus memiliki risiko lebih tinggi.
                        </li>
                        <li><strong>Ras:</strong> Orang kulit putih dan Asia memiliki risiko lebih tinggi.</li>
                        <li><strong>Gaya Hidup:</strong>
                            <ul>
                                <li>Kurang asupan kalsium dan vitamin D.</li>
                                <li>Kurang aktivitas fisik dan olahraga beban.</li>
                                <li>Merokok dan konsumsi alkohol berlebihan.</li>
                            </ul>
                        </li>
                        <li><strong>Kondisi Medis:</strong>
                            <ul>
                                <li>Penyakit endokrin (hipertiroidisme, hiperparatiroidisme).</li>
                                <li>Penyakit inflamasi (rheumatoid arthritis, penyakit radang usus).</li>
                                <li>Penyakit ginjal kronis.</li>
                            </ul>
                        </li>
                        <li><strong>Obat-obatan:</strong> Penggunaan jangka panjang kortikosteroid, antikonvulsan, dan obat
                            tiroid tertentu.</li>
                    </ul>

                    <h2 class="card-title">Gejala Osteoporosis</h2>
                    <p class="card-text">
                        Osteoporosis sering tidak menunjukkan gejala pada tahap awal. Namun, seiring perkembangan penyakit,
                        gejala berikut dapat muncul:
                    </p>
                    <ul class="list-unstyled">
                        <li>Nyeri punggung, disebabkan oleh patah tulang belakang.</li>
                        <li>Postur tubuh bungkuk (kifosis).</li>
                        <li>Penurunan tinggi badan seiring waktu.</li>
                        <li>Patah tulang yang terjadi dengan mudah, bahkan akibat benturan ringan.</li>
                    </ul>

                    <h2 class="card-title">Diagnosis</h2>
                    <p class="card-text">
                        Diagnosis osteoporosis biasanya dilakukan dengan tes kepadatan tulang, seperti DXA scan (dual-energy
                        X-ray absorptiometry). Tes ini mengukur kepadatan mineral tulang di tulang belakang, pinggul, atau
                        pergelangan tangan.
                    </p>

                    <h3 class="card-title">Interpretasi Hasil DXA Scan</h3>
                    <p class="card-text">
                        Hasil DXA scan dilaporkan sebagai T-score, yang membandingkan kepadatan tulang Anda dengan kepadatan
                        tulang rata-rata orang dewasa muda yang sehat.
                    </p>
                    <ul class="list-unstyled">
                        <li><strong>T-score -1.0 atau lebih tinggi:</strong> Normal.</li>
                        <li><strong>T-score antara -1.0 dan -2.5:</strong> Osteopenia (kepadatan tulang rendah).</li>
                        <li><strong>T-score -2.5 atau lebih rendah:</strong> Osteoporosis.</li>
                    </ul>

                    <h2 class="card-title">Pencegahan</h2>
                    <p class="card-text">
                        Pencegahan osteoporosis dimulai dengan gaya hidup sehat:
                    </p>
                    <ul class="list-unstyled">
                        <li><strong>Nutrisi:</strong>
                            <ul>
                                <li>Konsumsi makanan kaya kalsium (susu, keju, yogurt, sayuran hijau).</li>
                                <li>Konsumsi makanan kaya vitamin D (ikan berlemak, telur, makanan yang diperkaya).</li>
                                <li>Suplemen kalsium dan vitamin D jika diperlukan (konsultasikan dengan dokter).</li>
                            </ul>
                        </li>
                        <li><strong>Olahraga:</strong>
                            <ul>
                                <li>Latihan beban (angkat beban, berjalan, jogging).</li>
                                <li>Latihan aerobik (berenang, bersepeda).</li>
                                <li>Latihan keseimbangan (tai chi, yoga).</li>
                            </ul>
                        </li>
                        <li><strong>Gaya Hidup Sehat:</strong>
                            <ul>
                                <li>Hindari merokok.</li>
                                <li>Batasi konsumsi alkohol.</li>
                            </ul>
                        </li>
                    </ul>

                    <h2 class="card-title">Pengobatan</h2>
                    <p class="card-text">
                        Pengobatan osteoporosis bertujuan untuk memperlambat hilangnya kepadatan tulang, meningkatkan
                        pembentukan tulang, dan mencegah patah tulang:
                    </p>
                    <ul class="list-unstyled">
                        <li><strong>Obat-obatan:</strong>
                            <ul>
                                <li>Bisfosfonat (alendronat, risedronat, zoledronat).</li>
                                <li>Denosumab.</li>
                                <li>Teriparatide.</li>
                                <li>Romosozumab.</li>
                            </ul>
                        </li>
                        <li><strong>Terapi Hormon:</strong> Terapi penggantian hormon (HRT) untuk wanita pasca menopause
                            (konsultasikan dengan dokter).</li>
                    </ul>

                    <h2 class="card-title">Komplikasi Osteoporosis</h2>
                    <p class="card-text">
                        Patah tulang, terutama patah tulang belakang atau pinggul, adalah komplikasi utama dari
                        osteoporosis. Patah tulang osteoporosis dapat menyebabkan nyeri, kecacatan, dan bahkan kematian.
                    </p>

                    <h2 class="card-title">Statistik Osteoporosis</h2>
                    <p class="card-text">
                        Osteoporosis mempengaruhi sekitar 200 juta wanita di seluruh dunia. Diperkirakan 1 dari 3 wanita dan
                        1 dari 5 pria di atas usia 50 tahun akan mengalami patah tulang osteoporosis.
                    </p>

                    <h2 class="card-title">Pentingnya Kesadaran Osteoporosis</h2>
                    <p class="card-text">
                        Kesadaran akan osteoporosis sangat penting untuk pencegahan dan deteksi dini. Edukasi tentang faktor
                        risiko, gejala, dan cara pencegahan dapat membantu mengurangi dampak penyakit ini.
                    </p>

                    <p class="card-text">
                        <strong>Sumber:</strong>
                    <ul>
                        <li>National Osteoporosis Foundation: <a href="https://www.nof.org/" target="_blank">www.nof.org</a>
                        </li>
                        <li>World Health Organization: <a
                                href="https://www.who.int/news-room/fact-sheets/detail/osteoporosis"
                                target="_blank">www.who.int</a></li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
