<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Seller Panel')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #1a1a1a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
            margin-right: 1rem;
        }
        .nav-link {
            color: #adb5bd !important;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #fff !important;
        }
        .btn-outline-light {
            border-color: #adb5bd;
            color: #adb5bd;
        }
        .btn-outline-light:hover {
            background-color: #1a1a1a;
            color: #fff;
        }
        .container {
            max-width: 800px;
            
        }
    </style>
</head>
<body>
    <!-- Seller Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('seller.dashboard') }}">MyStore Seller</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('seller.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('seller.products.create') }}">Add Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('seller.products.index') }}">My Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('seller.orders') }}">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('seller.profile') }}">Profile</a></li>
                </ul>

                <form method="POST" action="{{ route('seller.logout') }}" class="d-flex align-items-center">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Toast Notification -->
    @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1000;">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastEl = document.querySelector('.toast');
            if (toastEl) {
                new bootstrap.Toast(toastEl, { delay: 3000 }).show();
            }
        });
    </script>
</body>
</html>