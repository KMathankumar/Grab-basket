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

        /* Reduce navbar height by ~10% */
        .navbar {
            background-color: #1a1a1a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-height: 54px;
            padding-top: 0;
            padding-bottom: 0;
        }

        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
            font-size: 1.1rem;
            margin-left: -70px; /* Brand shifted left */
        }

        .nav-link {
            color: #adb5bd !important;
            font-size: 0.95rem;
            padding: 0.5rem 0.8rem;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        /* Logout Button - Fixed red, no hover effect, far right */
        .logout-button {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
            margin-left:100%; /* Pushes form to the right */
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            transition: none !important;
        }

        .logout-button:hover,
        .logout-button:focus {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
            transform: none;
            box-shadow: none;
        }

        /* Compact main content padding */
        .container-fluid {
            padding-top: 1.2rem;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    </style>
</head>
<body>
    <!-- Seller Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('seller.dashboard') }}">Grab basket</a>

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

                <!-- Logout Button - Aligned to far right -->
                <form method="POST" action="{{ route('seller.logout') }}" class="d-flex">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm logout-button">
                        Logout
                    </button>
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
    <div class="container-fluid">
        @yield('content')
    </div>

    <!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')    <script>
        // Auto-show toast notifications
        document.addEventListener('DOMContentLoaded', function () {
            const toastEl = document.querySelector('.toast');
            if (toastEl) {
                new bootstrap.Toast(toastEl, { delay: 3000 }).show();
            }
        });
    </script>
</body>
</html>