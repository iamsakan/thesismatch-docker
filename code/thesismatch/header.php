<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThesisMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/thesismatch/public/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/thesismatch/index.php">
            <i class="bi bi-mortarboard-fill"></i> ThesIS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMenu">
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'profesor'): ?>
                        <a class="nav-link" href="/thesismatch/topic_create.php">Objavi temo</a>
                        <a class="nav-link" href="/thesismatch/applications.php">Prijave</a>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] === 'student'): ?>
                        <a class="nav-link" href="/thesismatch/applications.php">Moje prijave</a>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a class="nav-link" href="/thesismatch/admin.php">Admin</a>
                    <?php endif; ?>
                    <a class="nav-link" href="/thesismatch/topics.php">Teme</a>
                    <a class="nav-link" href="/thesismatch/logout.php">Odjava</a>
                <?php else: ?>
                    <a class="nav-link" href="/thesismatch/topics.php">Teme</a>
                    <a class="nav-link" href="/thesismatch/login.php">Prijava</a>
                    <a class="nav-link" href="/thesismatch/register.php">Registracija</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-1">