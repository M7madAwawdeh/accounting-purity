<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('settings.app_name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('storage/' . config('settings.logo')) }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- RTL Support -->
    @if (App::getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-v4-rtl@4.6.0-2/dist/css/bootstrap-rtl.min.css">
    @endif
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #212529;
            --sidebar-link-color: #adb5bd;
            --sidebar-link-hover-color: #fff;
            --sidebar-link-hover-bg: #343a40;
            --header-height: 56px;
        }
        body {
            background-color: #f8f9fa;
        }
        .header {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: var(--header-height);
            background-color: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }
        .menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #343a40;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            right: calc(-1 * var(--sidebar-width)); /* Start hidden */
            background-color: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            transition: right 0.3s ease;
            z-index: 1040;
        }
        .sidebar.active {
            right: 0;
        }
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #495057;
            text-align: center;
        }
        .sidebar-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .sidebar-menu { /* This is the <ul> */
            flex-grow: 1;
            overflow-y: auto; /* Vertical scroll */
            list-style: none;
            padding: 1rem 0;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar .nav-item-header {
            padding: 0.75rem 1.5rem 0.25rem;
            font-size: 0.8rem;
            font-weight: bold;
            color: #6c757d;
            text-transform: uppercase;
        }
        .sidebar .nav-item {
            padding: 0.1rem 1rem;
        }
        .sidebar .nav-link {
            color: var(--sidebar-link-color);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            border-radius: .375rem;
            white-space: nowrap;
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 25px;
            text-align: center;
            margin-left: 15px; /* In RTL, this becomes margin-right, creating space */
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: var(--sidebar-link-hover-color);
            background-color: var(--sidebar-link-hover-bg);
        }
        .sidebar-footer {
            padding: 0.5rem 0;
            border-top: 1px solid #495057;
        }
        .main-content {
            padding-top: var(--header-height); /* Space for fixed header */
            width: 100%;
        }
        .page-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
        .table-responsive-md {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 1035;
        }
        .sidebar-overlay.active {
            display: block;
        }

    </style>
</head>
<body dir="{{ App::getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div id="app">
        @auth
        <header class="header">
            <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
        </header>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        @endauth
        
        @include('layouts.sidebar')

        <div class="main-content">
            <main class="py-4">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @auth
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebar-overlay').classList.toggle('active');
        });
        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
            this.classList.remove('active');
        });
    </script>
    @endauth

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    if (confirm('{{ __("main.delete_confirmation") }}')) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
