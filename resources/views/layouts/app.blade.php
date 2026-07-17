<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-PIKET — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/remixicon/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

    @include('layouts.sidebar')

    <div class="main">

        @include('layouts.header')

        <main class="content">
            @include('Dashboard._panel_switcher')
            @yield('content')
        </main>

        @include('layouts.footer')

    </div>

    <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/datatables.min.js') }}"></script>

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

            if (window.innerWidth <= 768) {
                const isActive = sidebar.classList.toggle('mobile-active');
                if (isActive) {
                    overlay.style.display = 'block';
                } else {
                    overlay.style.display = 'none';
                }
            } else {
                const isHidden = sidebar.classList.toggle('sidebar-hidden');
                if (isHidden) {
                    main.style.marginLeft = '0';
                    header.style.left     = '0';
                } else {
                    main.style.marginLeft = 'var(--sidebar-width)';
                    header.style.left     = 'var(--sidebar-width)';
                }
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const main    = document.querySelector('.main');
            const header  = document.querySelector('.header-main');

            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-active');
                overlay.style.display = 'none';
            } else {
                sidebar.classList.add('sidebar-hidden');
                main.style.marginLeft = '0';
                header.style.left     = '0';
            }
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