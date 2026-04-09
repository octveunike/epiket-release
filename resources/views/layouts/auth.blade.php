<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'SMAN 1 Cibinong')</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-sman1-cibinong.jpg') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @stack('styles')
</head>
<body>

    @yield('content')

    <!-- Additional Scripts -->
    @stack('scripts')

</body>
</html>