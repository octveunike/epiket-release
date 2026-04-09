{{-- header.blade.php --}}
<div class="header-main">

    <a id="menuSidebar" href="#" class="menu-link" onclick="toggleSidebar(event)">
        <i class="ri-menu-2-fill"></i>
    </a>

    <h3 style="position:absolute; left:50%; transform:translateX(-50%); margin:0;">SISTEM INFORMASI PIKET SMAN 1 CIBINONG</h3>

    <div class="header-right">

        {{-- Notifications --}}
        <div class="header-icon-btn dropdown" id="notifDropdown" onclick="toggleDropdown('notifDropdown')">
            <i class="ri-notification-3-line"></i>
            <span class="header-badge"></span>
            <div class="dropdown-menu" style="min-width:280px;">
                <div style="padding:4px 8px 10px; font-weight:600; font-size:13px; color:var(--text-main); border-bottom:1px solid var(--border); margin-bottom:6px;">
                    Notifikasi
                </div>
                <div class="dropdown-item">
                    <i class="ri-calendar-check-line" style="color:var(--primary)"></i>
                    <div>
                        <div style="font-size:13px; font-weight:500; color:var(--text-main)">Kegiatan baru ditambahkan</div>
                        <div style="font-size:11.5px; color:var(--text-muted)">2 menit yang lalu</div>
                    </div>
                </div>
                <div class="dropdown-item">
                    <i class="ri-book-line" style="color:#10b981"></i>
                    <div>
                        <div style="font-size:13px; font-weight:500; color:var(--text-main)">Pelatihan dimulai besok</div>
                        <div style="font-size:11.5px; color:var(--text-muted)">1 jam yang lalu</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <a href="#" class="header-icon-btn">
            <i class="ri-settings-3-line"></i>
        </a>

        {{-- Avatar --}}
        <div class="dropdown" id="avatarDropdown" onclick="toggleDropdown('avatarDropdown')">
            <div class="avatar-text">
                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
            </div>

            <div class="dropdown-menu" style="min-width:200px;">
                <div style="padding:8px 12px 10px; border-bottom:1px solid var(--border); margin-bottom:6px;">
                    <div style="font-weight:600; font-size:13.5px; color:var(--text-main)">
                        {{ auth()->user()->nama }}
                    </div>
                    <div style="font-size:12px; color:var(--text-muted)">
                        {{ auth()->user()->email }}
                    </div>
                    <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                        {{ auth()->user()->roles->pluck('nama_role')->join(', ') }}
                    </div>
                </div>
                <a href="#" class="dropdown-item">
                    <i class="ri-user-line"></i> Profil Saya
                </a>
                <a href="#" class="dropdown-item">
                    <i class="ri-settings-line"></i> Pengaturan
                </a>
                <hr class="dropdown-divider">
                <a href="{{ route('logout') }}" class="dropdown-item" style="color:#ef4444;"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="ri-logout-box-r-line"></i> Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>

    </div>

</div>