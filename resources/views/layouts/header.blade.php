{{-- header.blade.php --}}
<div class="header-main">

    <a id="menuSidebar" href="#" class="menu-link" onclick="toggleSidebar(event)">
        <i class="ri-menu-2-fill"></i>
    </a>

    <h3 style="position:absolute; left:50%; transform:translateX(-50%); margin:0;">SISTEM INFORMASI PIKET SMAN 1 CIBINONG</h3>

    <div class="header-right">

        {{-- User button --}}
        <div class="dropdown" id="avatarDropdown" onclick="toggleDropdown('avatarDropdown')"
             style="cursor:pointer;">
            <div style="display:flex; align-items:center; gap:10px; padding:6px 12px 6px 6px;
                        border:1px solid var(--border); border-radius:999px; background:#fff;">
                <div class="avatar-text" style="border:none;">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
                </div>
                <div style="display:flex; flex-direction:column; line-height:1.15;">
                    <span style="font-weight:600; font-size:13px; color:var(--text-main);">
                        {{ auth()->user()->nama }}
                    </span>
                    <span style="font-size:11px; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
                        <span>{{ auth()->user()->roles->pluck('nama_role')->join(', ') }}</span>
                        @php
                            $namaKelasAvatar = null;
                            $uId = auth()->user()->id;
                            if (auth()->user()->hasRole('Wali Kelas')) {
                                $k = \Illuminate\Support\Facades\DB::table('kelas')
                                    ->join('guru', 'guru.id', '=', 'kelas.wali_kelas_id')
                                    ->where('guru.user_id', $uId)
                                    ->where('kelas.status', 1)
                                    ->select('kelas.nama_kelas')->first();
                                if($k) $namaKelasAvatar = $k->nama_kelas;
                            } elseif (auth()->user()->hasRole('Ketua Kelas') || auth()->user()->hasRole('Siswa')) {
                                $k = \Illuminate\Support\Facades\DB::table('kelas')
                                    ->join('siswa', 'siswa.id', '=', 'kelas.ketua_kelas_id')
                                    ->where('siswa.user_id', $uId)
                                    ->where('kelas.status', 1)
                                    ->select('kelas.nama_kelas')->first();
                                if($k) $namaKelasAvatar = $k->nama_kelas;
                            }
                        @endphp
                        @if($namaKelasAvatar)
                            <span style="background:var(--primary-light); color:var(--primary); padding:2px 6px; border-radius:4px; font-weight:600; font-size:10px;">
                                {{ $namaKelasAvatar }}
                            </span>
                        @endif
                    </span>
                </div>
                <i class="ri-arrow-down-s-line" style="color:var(--text-muted); font-size:16px;"></i>
            </div>

            <div class="dropdown-menu" style="min-width:200px;">
                <a href="{{ route('UserManagement.edit', auth()->user()->id) }}" class="dropdown-item">
                    <i class="ri-lock-password-line"></i> Ubah Password
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