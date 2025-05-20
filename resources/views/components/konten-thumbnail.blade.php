@if ($konten->jenis == 'video')
    <div class="video-thumbnail pointer" data-bs-toggle="modal" data-bs-target="#edukasiModal"
        data-konten-id="{{ $konten->id }}">
        @php
            $videoId = '';
            if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $konten->path, $matches)) {
                $videoId = $matches[1];
            }
        @endphp
        <img src="{{ $videoId ? 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg' : asset('assets/images/placeholder-video.jpg') }}"
            class="card-img-top" alt="{{ $konten->judul }}">
    </div>
@else
    <div class="artikel-thumbnail pointer" data-bs-toggle="modal" data-bs-target="#edukasiModal"
        data-konten-id="{{ $konten->id }}">
        @if ($konten->thumbnail || $konten->path)
            @php
                $imageSrc = $konten->thumbnail
                    ? asset('storage/' . $konten->thumbnail)
                    : asset('storage/' . $konten->path);
            @endphp
            <img src="{{ $imageSrc }}" class="card-img-top" alt="{{ $konten->judul }}">
        @else
            <div class="card-body">
                <i class="bi bi-file-text fs-1 text-primary"></i>
            </div>
        @endif
    </div>
@endif
