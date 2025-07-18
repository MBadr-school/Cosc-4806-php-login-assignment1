<?php
// Movie app doesn't require authentication for search functionality
// Users can search and rate movies anonymously
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo isset($data['page_title']) ? $data['page_title'] : 'Movie Search & Reviews'; ?></title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Custom CSS -->
        <style>
            :root {
                --primary-color: #4a90e2;
                --primary-hover: #357abd;
                --light-blue: #e3f2fd;
                --text-muted: #6c757d;
            }

            .navbar-brand {
                font-weight: bold;
                color: var(--primary-color) !important;
            }

            .navbar {
                box-shadow: 0 2px 4px rgba(0,0,0,.1);
            }

            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }

            .btn-primary:hover {
                background-color: var(--primary-hover);
                border-color: var(--primary-hover);
            }

            .text-primary {
                color: var(--primary-color) !important;
            }

            .bg-primary {
                background-color: var(--primary-color) !important;
            }

            body {
                background-color: #f8f9fa;
            }
        </style>

        <link rel="icon" href="/favicon.png">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/movie">
                <i class="bi bi-film me-2"></i>
                Movie Search & Reviews
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/movie') !== false) ? 'active' : ''; ?>" href="/movie">
                            <i class="bi bi-search me-1"></i> Search Movies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/movie/popular">
                            <i class="bi bi-fire me-1"></i> Popular
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/movie/recent">
                            <i class="bi bi-clock me-1"></i> Recent
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['auth']) && $_SESSION['auth']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/home">
                                <i class="bi bi-house me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <!-- Content goes here -->