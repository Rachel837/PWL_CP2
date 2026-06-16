<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="/" class="d-inline-flex">
            <img src="data:image/svg+xml,%3csvg%20width='62'%20height='67'%20viewBox='0%200%2062%2067'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M30.604%2066.378L0.00805664%2048.1582V35.7825L30.604%2054.0023V66.378Z'%20fill='%23302C4D'/%3e%3cpath%20d='M61.1996%2048.1582L30.604%2066.378V54.0023L61.1996%2035.7825V48.1582Z'%20fill='%23E66239'/%3e%3cpath%20d='M30.5955%200L0%2018.2198V30.5955L30.5955%2012.3757V0Z'%20fill='%23657E92'/%3e%3cpath%20d='M61.191%2018.2198L30.5955%200V12.3757L61.191%2030.5955V18.2198Z'%20fill='%23A3B2BE'/%3e%3cpath%20d='M30.604%2048.8457L0.00805664%2030.6259V18.2498L30.604%2036.47V48.8457Z'%20fill='%23302C4D'/%3e%3cpath%20d='M61.1996%2030.6259L30.604%2048.8457V36.47L61.1996%2018.2498V30.6259Z'%20fill='%23E66239'/%3e%3c/svg%3e"
                 alt="" width="24">
            <span class="logo-text ms-2"> <img src="{{ asset('assets/images/logo.svg') }}" alt=""></span>
        </a>
    </div>
    <ul class="nav flex-column">
        @php
            $role = Session::get('user')['role'] ?? '';
        @endphp

        <!-- ================= DATA MASTER SECTION ================= -->
        @if(Session::has('user') && $role === 'administrator')
            <li class="px-4 py-2"><small class="nav-text text-uppercase font-semibold tracking-wider opacity-75">Data Master</small></li>
            <li>
                <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="ti ti-users"></i>
                    <span class="nav-text">Pengguna</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->is('ruangan*') ? 'active' : '' }}" href="{{ route('ruangan.index') }}">
                    <i class="ti ti-door"></i>
                    <span class="nav-text">Ruangan</span>
                </a>
            </li>
        @endif

        <!-- ================= PENGADAAN SECTION ================= -->
        @if(Session::has('user') && in_array($role, ['kepala laboratorium', 'ketua program studi', 'staf administrasi']))
            <li class="px-4 py-2"><small class="nav-text text-uppercase font-semibold tracking-wider opacity-75">Pengadaan Barang</small></li>
            <li>
                <a class="nav-link {{ (request()->is('draft-pengadaan') || request()->is('draft-pengadaan/*')) && !request()->is('draft-pengadaan/history') && !request()->is('draft-pengadaan/review*') && !request()->is('draft-pengadaan/*/terima') ? 'active' : '' }}"
                    href="{{ route('draft-pengadaan.index') }}">
                    <i class="ti ti-file-text"></i>
                    <span class="nav-text">Draf Pengadaan</span>
                </a>
            </li>

            @if($role === 'staf administrasi')
                <li>
                    <a class="nav-link {{ request()->is('penerimaan-barang') || request()->is('draft-pengadaan/*/terima') ? 'active' : '' }}"
                        href="{{ route('penerimaan-barang.index') }}">
                        <i class="ti ti-box"></i>
                        <span class="nav-text">Penerimaan Barang</span>
                    </a>
                </li>
            @endif

            @if($role === 'kepala laboratorium')
                <li>
                    <a class="nav-link {{ request()->is('draft-pengadaan/history') ? 'active' : '' }}"
                        href="{{ route('draft-pengadaan.history') }}">
                        <i class="ti ti-history"></i>
                        <span class="nav-text">History Pengadaan</span>
                    </a>
                </li>
            @endif

            @if($role === 'ketua program studi')
                <li>
                    <a class="nav-link {{ request()->is('draft-pengadaan/review*') ? 'active' : '' }}"
                        href="{{ route('draft-pengadaan.review.index') }}">
                        <i class="ti ti-clipboard-check"></i>
                        <span class="nav-text">Review Pengadaan</span>
                    </a>
                </li>
            @endif
        @endif

        <!-- ================= OPERASIONAL LAB SECTION ================= -->
        @if(Session::has('user') && $role === 'staf laboratorium')
            <li class="px-4 py-2"><small class="nav-text text-uppercase font-semibold tracking-wider opacity-75">Operasional Lab</small></li>
            <li>
                <a class="nav-link {{ request()->is('maintenance*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
                    <i class="ti ti-tool"></i>
                    <span class="nav-text">Log Maintenance</span>
                </a>
            </li>
        @endif

        <!-- ================= MANAJEMEN ASET SECTION (BOTTOM) ================= -->
        @if(Session::has('user') && in_array($role, ['kepala laboratorium', 'ketua program studi', 'staf administrasi', 'staf laboratorium']))
            <li class="px-4 py-2"><small class="nav-text text-uppercase font-semibold tracking-wider opacity-75">Manajemen Aset</small></li>
            <li>
                <a class="nav-link {{ request()->is('inventaris*') ? 'active' : '' }}" href="{{ route('inventaris.index') }}">
                    <i class="ti ti-archive"></i>
                    <span class="nav-text">Inventory</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->is('stok-bhp*') ? 'active' : '' }}" href="{{ route('stok-bhp.index') }}">
                    <i class="ti ti-box"></i>
                    <span class="nav-text">Stok BHP</span>
                </a>
            </li>
        @endif
    </ul>
</aside>