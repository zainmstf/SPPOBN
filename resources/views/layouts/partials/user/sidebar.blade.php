<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('dashboard') }}"><img src="{{ asset('storage/img/logo/logo-dashboard.png') }}"
                            alt="Logo" srcset="" /></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <!-- Dashboard Link -->
                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <i class="bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Konsultasi Menu -->
                <li class="sidebar-title">Menu Konsultasi</li>
                <li
                    class="sidebar-item has-sub {{ request()->routeIs(['konsultasi.*', 'riwayat.*']) ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-clipboard-check-fill"></i>
                        <span>Konsultasi</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs(['konsultasi.*', 'riwayat.*']) ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ request()->routeIs('konsultasi.index', 'konsultasi.question', 'konsultasi.session-summary') ? 'active' : '' }}">
                            <a href="{{ route('konsultasi.index') }}">Mulai Konsultasi Baru</a>
                        </li>
                        <li
                            class="submenu-item {{ request()->routeIs('konsultasi.result', 'konsultasi.recent') ? 'active' : '' }}">
                            <a href="{{ route('konsultasi.recent') }}">Hasil Konsultasi Terakhir</a>
                        </li>

                        <li class="submenu-item {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                            <a href="{{ route('riwayat.index') }}">Riwayat Konsultasi</a>
                        </li>
                    </ul>
                </li>

                <!-- Edukasi Menu -->
                <li class="sidebar-title">Informasi</li>
                <li class="sidebar-item {{ request()->routeIs('about.osteoporosis') ? 'active' : '' }}">
                    <a href="{{ route('about.osteoporosis') }}" class="sidebar-link">
                        <i class="bi-info-circle-fill"></i>
                        <span>Osteoporosis</span>
                    </a>
                </li>
                <li class="sidebar-item has-sub {{ request()->routeIs('edukasi.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-bag-heart-fill"></i>
                        <span>Edukasi Nutrisi</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('edukasi.*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->routeIs('edukasi.index') ? 'active' : '' }}">
                            <a href="{{ route('edukasi.index') }}">Edukasi Nutrisi Lansia</a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('edukasi.daftarMakanan') ? 'active' : '' }}">
                            <a href="{{ route('edukasi.daftarMakanan') }}">Daftar Makanan</a>
                        </li>
                    </ul>
                </li>

                <!-- Laporan -->
                <li class="sidebar-title">Laporan</li>

                <li class="sidebar-item has-sub {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-file-earmark-person-fill"></i>
                        <span>Laporan Kesehatan</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ request()->routeIs('laporan.grafikPerkembangan') ? 'active' : '' }}">
                            <a href="{{ route('laporan.grafikPerkembangan') }}">Grafik Perkembangan</a>
                        </li>
                        <li
                            class="submenu-item {{ request()->routeIs('laporan.tampilkanRekomendasi') ? 'active' : '' }}">
                            <a href="{{ route('laporan.tampilkanRekomendasi') }}">Laporan Cetak</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile Menu -->
                <li class="sidebar-title">Pengaturan</li>
                <li class="sidebar-item has-sub {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-person-circle"></i>
                        <span>Profil</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                            <a href="{{ route('profile.show') }}">Profil Saya</a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('profile.pengaturan') ? 'active' : '' }}">
                            <a href="{{ route('profile.pengaturan') }}">Ganti Kata Sandi</a>
                        </li>
                    </ul>
                </li>

                <!-- Feedback Menu -->
                <li class="sidebar-item {{ request()->routeIs('feedback.index') ? 'active' : '' }}">
                    <a href="{{ route('feedback.index') }}" class="sidebar-link">
                        <i class="bi-chat-dots-fill"></i>
                        <span>Umpan Balik</span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="sidebar-item">
                    <a href="{{ route('logout') }}" class="sidebar-link logout-link">
                        <i class="bi-arrow-right-square-fill"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x">
            <i data-feather="x"></i>
        </button>
    </div>
</div>
