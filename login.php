<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — ComfyGo</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="styles/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php $active_page = 'login';
    $active_page = $active_page ?? 'login';
    include 'navbaar.php'; ?>
<div id="login_hero">
  <p id="login_tag">Welcome Back</p>
  <h1 id="login_title">Sign in to <em>ComfyGo</em></h1>
  <p id="login_sub">Access your bookings, saved trips, and account settings.</p>
</div>
<div id="login_wrapper">
  <div id="login_card">
    <form method="POST" action="login.php" id="login_form">
      <div id="field_email" class="login_field">
        <label for="email">Email Address</label>
        <div class="field_wrap">
          <i class="fa-regular fa-envelope field_icon"></i>
          <input type="email" id="email" name="email" placeholder="you@example.com" required autofocus autocomplete="email">
        </div>
      </div>
      <div id="field_password" class="login_field">
        <div id="pw_label_row">
          <label for="password">Password</label>
          <a href="forgot_password.php" id="forgot_link">Forgot password?</a>
        </div>
        <div class="field_wrap">
          <i class="fa-solid fa-lock field_icon"></i>
          <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
        </div>
      </div>
      <div id="remember_row">
        <label id="remember_label">
          <input type="checkbox" name="remember" id="remember_check">
          <span class="check_box"></span>
          <span id="remember_text">Remember me for 30 days</span>
        </label>
      </div>
      <button type="submit" id="login_submit">
        Sign In
      </button>
    </form>
    <p id="signup_text">Don't have an account? <a href="register.php" id="signup_link">Create one free</a></p>
  </div>
</div>
</body>
</html>