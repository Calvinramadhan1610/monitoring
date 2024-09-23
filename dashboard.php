<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSI</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
        }
        .container {
            margin-top: 50px;
        }
        .card-welcome {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            padding: 40px 20px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }
        .card-welcome h1 {
            font-size: 2.5rem;
            color: #343a40;
            margin-bottom: 20px;
        }
        .card-welcome p {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 40px;
        }
        .btn-logout {
            background-color: #dc3545;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 30px;
            color: white;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
            text-decoration: none;
        }
        /* Animasi Fade In */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Style Slider */
        .carousel-item img {
            border-radius: 15px;
            max-height: 400px;
            object-fit: cover;
        }
        /* Styling Section di Bawah Slider */
        .section-info {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 40px;
            text-align: center;
        }
        .section-info h2 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 20px;
        }
        .section-info p {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">MONITORING PEKERJAAN DSI</a>
    <div class="ml-auto d-flex align-items-center">
        <?php if (isset($_SESSION['username'])): ?>
            <!-- Tombol yang menampilkan username pengguna -->
            <a href="admin.php" class="btn btn-outline-light btn-admin mr-2">
                <?= htmlspecialchars($_SESSION['username']); ?>
            </a>
            <!-- Tombol Logout -->
            <a href="login.php" class="btn btn-outline-light  btn-admin mr-2 ">Login ke akun lain</a>
        <?php else: ?>
            <!-- Tombol Login -->
            <a href="login.php" class="btn btn-outline-light btn-login mr-2">Login</a>
        <?php endif; ?>
        <!-- Dropdown Menu -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                &#x22EE; <!-- Vertical ellipsis icon (three dots) -->
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="monitoring.php">Form Pengisian</a>
                <a class="dropdown-item" href="diagram.php">Lihat Diagram Pie</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-welcome">
                <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Kami senang melihat Anda kembali. Gunakan menu di atas untuk memulai pekerjaan Anda.</p>
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                <?php endif; ?>
            </di   v>
        </div>
    </div>

    <!-- Slider Carousel di bawah tulisan -->
    <div id="carouselExample" class="carousel slide mb-4" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/fifis.jpeg" class="d-block w-100" alt="Kantor 1">
            </div>
            <div class="carousel-item">
                <img src="assets/lanjutken.jpeg" class="d-block w-100" alt="Kantor 2">
            </div>
            <div class="carousel-item">
                <img src="assets/660fb42a-c6c3-4af7-a837-507aca23e165.jpeg" class="d-block w-100" alt="Kantor 3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Ganti bagian Google Maps API -->
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="section-info">
            <h2>Moto Perusahaan</h2>
            <p>"IT Solution For Your Business."</p>
                
            <h2>Alamat Perusahaan</h2>
            <p>Sendangmulyo, Tembalang, Semarang City, Central Java 50272</p>

            <!-- Google Maps Iframe -->
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15839.101466211989!2d110.4560433778413!3d-7.0356670387213684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708dcac8b65eb7%3A0xfd54752e410ba7b9!2sSendangmulyo%2C%20Tembalang%2C%20Semarang%20City%2C%20Central%20Java%2050272!5e0!3m2!1sen!2sid!4v1725941916692!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>


</body>
</html>