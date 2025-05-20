<div class="card h-100">
    <img src="{{ asset('storage/img/daftar-makanan/' . $makanan['gambar']) }}" class="card-img-top" alt="{{ $makanan['nama'] }}">
    <div class="card-body">
        <h5 class="card-title">{{ $makanan['nama'] }}</h5>
        <p class="card-text">Kategori: {{ $makanan['kategori'] }}</p>
        <p class="card-text">
            @foreach ($makanan['informasi_nutrisi'] as $nutrisi => $nilai)
            {{ ucfirst(str_replace('_', ' ', $nutrisi)) }}: {{ $nilai }}<br>
            @endforeach
        </p>
        <p class="card-text">{{ $makanan['deskripsi'] }}</p>
        <p class="card-text">Porsi: {{ $makanan['porsi'] }}</p>
        <p class="card-text">Tips: {{ $makanan['tips'] }}</p>
        <p class="card-text">Manfaat: {{ $makanan['manfaat'] }}</p>
    </div>
</div>