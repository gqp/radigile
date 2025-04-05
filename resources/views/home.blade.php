<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            text-align: center;
        }
        .coming-soon {
            margin-top: 10%;
            font-size: 2.5rem;
            font-weight: bold;
            color: #343a40;
        }
        .nav-link {
            color: #343a40 !important;
        }
    </style>
</head>
<body>
<!-- Navigation -->
@include('partials.nav')


<!-- Coming Soon Section -->
<div class="coming-soon">
    <p>Our website is coming soon! Stay tuned.</p>
    <button class="btn btn-primary" onclick="alert('Thank you for your interest!')">Notify Me</button>
</div>

<!-- About Section -->
<div id="about" class="container mt-5">
    <h3>About Us</h3>
    <p class="text-muted">We are working hard to launch our new website. Stay connected and check back soon!</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
