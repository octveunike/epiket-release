{{-- app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-PIKET — @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    @stack('styles')
</head>
<body>

    @include('layouts.sidebar')

    <div class="main">

        @include('layouts.header')

        <main class="content">
            @yield('content')
        </main>

        @include('layouts.footer')

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

    <script>
        /* ---- Sidebar accordion ---- */
        document.querySelectorAll('.sidebar-group-header').forEach(function(header) {
            header.addEventListener('click', function() {
                this.parentElement.classList.toggle('active');
            });
        });

        /* ---- Hamburger: toggle sidebar ---- */
        function toggleSidebar(e) {
            if (e) e.preventDefault();

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const main    = document.querySelector('.main');
            const header  = document.querySelector('.header-main');

            const isHidden = sidebar.classList.toggle('sidebar-hidden');

            if (isHidden) {
                main.style.marginLeft = '0';
                header.style.left     = '0';
                overlay.style.display = 'none';
            } else {
                main.style.marginLeft = 'var(--sidebar-width)';
                header.style.left     = 'var(--sidebar-width)';
                if (window.innerWidth <= 768) {
                    overlay.style.display = 'block';
                }
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const main    = document.querySelector('.main');
            const header  = document.querySelector('.header-main');

            sidebar.classList.add('sidebar-hidden');
            main.style.marginLeft = '0';
            header.style.left     = '0';
            overlay.style.display = 'none';
        }

        /* ---- Dropdown toggle ---- */
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('open');
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown.open').forEach(function(d) {
                if (!d.contains(e.target)) d.classList.remove('open');
            });
        });
    </script>

</body>
</html>