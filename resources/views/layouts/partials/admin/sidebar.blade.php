<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('storage/img/logo/logo-dashboard.png') }}"
                            alt="Logo" srcset="" /></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                        <i class="bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-title">Manajemen Data</li>

                <li class="sidebar-item has-sub {{ request()->routeIs('admin.basisPengetahuan.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-database-fill"></i>
                        <span>Basis Pengetahuan</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.basisPengetahuan.*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ request()->routeIs('admin.basisPengetahuan.aturan.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.basisPengetahuan.aturan.index') }}">Aturan</a>
                        </li>
                        <li
                            class="submenu-item {{ request()->routeIs('admin.basisPengetahuan.fakta.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.basisPengetahuan.fakta.index') }}">Fakta</a>
                        </li>
                        <li
                            class="submenu-item {{ request()->routeIs('admin.basisPengetahuan.solusi.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.basisPengetahuan.solusi.index') }}">Solusi</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub {{ request()->routeIs('admin.konten-edukasi.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-bag-heart-fill"></i>
                        <span>Konten Edukasi</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.konten-edukasi.*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ in_array(request()->route()->getName(), ['admin.konten-edukasi.index', 'admin.konten-edukasi.edit']) ? 'active' : '' }}">
                            <a href="{{ route('admin.konten-edukasi.index') }}">Daftar Materi</a>
                        </li>
                        <li
                            class="submenu-item {{ request()->routeIs('admin.konten-edukasi.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.konten-edukasi.create') }}">Tambah Materi Nutrisi</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.rekomendasi-nutrisi.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.rekomendasi-nutrisi.index') }}" class="sidebar-link">
                        <i class="bi-heart-pulse-fill"></i>
                        <span>Rekomendasi Nutrisi</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.sumber-nutrisi.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sumber-nutrisi.index') }}" class="sidebar-link">
                        <i class="bi-circle-square"></i>
                        <span>Sumber Nutrisi</span>
                    </a>
                </li>

                <li class="sidebar-title">Manajemen Pengguna & Konsultasi</li>

                <li class="sidebar-item has-sub {{ request()->routeIs('admin.konsultasi.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-clipboard2-pulse-fill"></i>
                        <span>Konsultasi</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.konsultasi.*') ? 'active' : '' }}">
                        <li
                            class="submenu-item {{ request()->routeIs('admin.konsultasi.riwayat', 'admin.konsultasi.show') ? 'active' : '' }}">
                            <a href="{{ route('admin.konsultasi.riwayat') }}">Riwayat Konsultasi</a>
                        </li>
                        <li
                            class="submenu-item {{ request()->routeIs('admin.konsultasi.statistik') ? 'active' : '' }}">
                            <a href="{{ route('admin.konsultasi.statistik') }}">Statistik Konsultasi</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                        <i class="bi-people-fill"></i>
                        <span>Pengguna</span>
                    </a>
                </li>

                <li class="sidebar-title">Laporan & Pengaturan</li>

                <li class="sidebar-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                    <a href="{{ route('admin.laporan') }}" class="sidebar-link">
                        <i class="bi-file-earmark-bar-graph-fill"></i>
                        <span>Laporan Konsultasi</span>
                    </a>
                </li>

                <li class="sidebar-item has-sub {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-chat-dots-fill"></i>
                        <span>Umpan Balik</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.feedback.index') }}">Daftar Umpan Balik</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.riwayatAktivitas') ? 'active' : '' }}">
                    <a href="{{ route('admin.riwayatAktivitas') }}" class="sidebar-link">
                        <i class="bi-activity"></i>
                        <span>Riwayat Aktivitas</span>
                    </a>
                </li>

                <li class="sidebar-title">Pengaturan</li>

                <li class="sidebar-item has-sub {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi-gear-fill"></i>
                        <span>Pengaturan Akun</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}">
                            <a href=" {{ route('admin.profile.index') }}">Profil Saya</a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('admin.profile.pengaturan') ? 'active' : '' }}">
                            <a href="{{ route('admin.profile.pengaturan') }}">Ganti Password</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                        <a href="{{route('logout.admin')}}" class="sidebar-link logout-link">
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
