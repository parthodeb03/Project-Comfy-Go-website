<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ComfyGo — Travel &amp; Stay in Bangladesh</title>
  <link rel="stylesheet" href="styles/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<?php
$active_page = 'home';
$active_page = $active_page ?? 'home';
?>
<nav id="navbaar">
<div id = "nav_sec_1">
    <a href = "index.php" id="comfygo_logo">ComfyGo</a>
</div>
<div id = "nav_sec_2">
    <a href="about.php" id="nav_about">About</a>
    <a href="contact.php" id="nav_contact">Contact</a>
</div>
<div id = "nav_sec_3">
    <a href="login.php" id="nav_login">Login</a>
    <a href="signup.php" id="nav_signup">Signup</a>
</div>
<button id="nav_toggle" aria-label="Toggle menu">
  <i class="fa-solid fa-bars"></i>
</button>
</nav>