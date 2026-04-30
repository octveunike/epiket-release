{{-- sidebar.blade.php --}}

<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <a href="{{ route('admin.index') }}" class="sidebar-logo">
            <div class="logo-icon"><i class="ri-shield-star-fill"></i></div>
            E-PIKET
        </a>
        <a href="#" class="sidebar-close-btn" onclick="closeSidebar()" style="color:rgba(255,255,255,.6); font-size:22px; margin-left:auto; display:none;">
            <i class="ri-close-line"></i>
        </a>
    </div>

    <div class="sidebar-body">

        {{-- Dashboard --}}
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        {{-- ======== APPS & PAGES ======== --}}
        <div class="sidebar-group active">
            <div class="sidebar-group-header sidebar-group-label">
                <span>APPS &amp; PAGES</span>
                <i class="ri-arrow-down-s-line dropdown-icon"></i>
            </div>
            <ul class="sidebar-menu">

                @if(auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('Kelas.index') }}" class="{{ request()->routeIs('Kelas.*') ? 'active' : '' }}">
                        <i class="ri-door-open-line"></i><span>Kelas</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('PeriodeAkademik.index') }}" class="{{ request()->routeIs('PeriodeAkademik.*') ? 'active' : '' }}">
                        <i class="ri-calendar-2-line"></i><span>Periode Akademik</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole(['Ketua Kelas','Admin','Petugas Piket']))
                <li>
                    <a href="{{ route('Absensi.index') }}" class="{{ request()->routeIs('Absensi.index') || request()->routeIs('Absensi.create') || request()->routeIs('Absensi.show') || request()->routeIs('Absensi.isiAbsensi') ? 'active' : '' }}">
                        <i class="ri-calendar-check-line"></i><span>Data Absensi</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole(['Wali Kelas','Petugas Piket']))
                <li>
                    <a href="{{ route('Absensi.walikelas.index') }}" class="{{ request()->routeIs('Absensi.walikelas.*') ? 'active' : '' }}">
                        <i class="ri-checkbox-circle-line"></i><span>Validasi Absensi</span>
                    </a>
                </li>
                @endif

                {{-- Laporan: hanya aktif di route Laporan.* --}}
                @if(auth()->user()->hasRole(['Wali Kelas','Admin','Petugas Piket','Ketua Kelas']))
                <li>
                    <a href="{{ route('Laporan.index') }}" class="{{ request()->routeIs('Laporan.*') ? 'active' : '' }}">
                        <i class="ri-bar-chart-2-line"></i><span>Buat Laporan</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole(['Admin','Petugas Piket']))
                <li>
                    <a href="{{ route('Keterlambatan.index') }}" class="{{ request()->routeIs('Keterlambatan.*') ? 'active' : '' }}">
                        <i class="ri-time-line"></i><span>Keterlambatan</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole(['Admin','Petugas Piket','Ketua Kelas']))
                <li>
                    <a href="{{ route('Dispensasi.index') }}" class="{{ request()->routeIs('Dispensasi.*') ? 'active' : '' }}">
                        <i class="ri-file-text-line"></i><span>Dispensasi</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('Organisasi.index') }}" class="{{ request()->routeIs('Organisasi.*') ? 'active' : '' }}">
                        <i class="ri-team-line"></i><span>Organisasi</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole(['Admin','Petugas Piket']))
                <li>
                    <a href="{{ route('DaftarTamu.index') }}" class="{{ request()->routeIs('DaftarTamu.*') ? 'active' : '' }}">
                        <i class="ri-calendar-2-line"></i><span>Daftar Tamu</span>
                    </a>
                </li>
                @endif

            </ul>
        </div>

        @if(auth()->user()->hasRole('Admin'))
        <div class="sidebar-group active">
            <div class="sidebar-group-header sidebar-group-label">
                <span>ADMIN</span>
                <i class="ri-arrow-down-s-line dropdown-icon"></i>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('Guru.index') }}" class="{{ request()->routeIs('Guru.*') ? 'active' : '' }}">
                        <i class="ri-user-2-line"></i><span>Data Guru</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('Siswa.index') }}" class="{{ request()->routeIs('Siswa.*') ? 'active' : '' }}">
                        <i class="ri-graduation-cap-line"></i><span>Data Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('Staff.index') }}" class="{{ request()->routeIs('Staff.*') ? 'active' : '' }}">
                        <i class="ri-user-settings-line"></i><span>Data Staf</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif

        @if(auth()->user()->hasRole('Admin'))
        <div class="sidebar-group active">
            <div class="sidebar-group-header sidebar-group-label">
                <span>USER MANAGEMENT</span>
                <i class="ri-arrow-down-s-line dropdown-icon"></i>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('UserManagement.index') }}" class="{{ request()->routeIs('UserManagement.*') ? 'active' : '' }}">
                        <i class="ri-user-3-line"></i><span>Data User Role</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif

    </div>
</div>

<div id="sidebarOverlay" onclick="closeSidebar()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1050;"></div>