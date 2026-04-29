<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {

        $stmt = $pdo->prepare("SELECT user_ID, user_name, password FROM Users WHERE user_email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['user_ID'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['role']      = 'tourist';
            header('Location: tourist.php');
            exit;
        }

        $stmt = $pdo->prepare("SELECT guide_NID, guide_name, password FROM Guide WHERE guide_email = ? LIMIT 1");
        $stmt->execute([$email]);
        $guide = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($guide && password_verify($password, $guide['password'])) {
            session_regenerate_id(true);
            $_SESSION['guide_nid']  = $guide['guide_NID'];
            $_SESSION['guide_name'] = $guide['guide_name'];
            $_SESSION['role']       = 'guide';
            header('Location: guide.php');
            exit;
        }

        $stmt = $pdo->prepare("SELECT manager_ID, manager_name, password FROM Manager WHERE manager_email = ? LIMIT 1");
        $stmt->execute([$email]);
        $manager = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($manager && password_verify($password, $manager['password'])) {
            session_regenerate_id(true);
            $_SESSION['manager_id']   = $manager['manager_ID'];
            $_SESSION['manager_name'] = $manager['manager_name'];
            $_SESSION['role']         = 'manager';
            header('Location: manager.php');
            exit;
        }

        $error = 'Invalid email or password. Please try again.';
    }
}
?>
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

<?php $active_page = 'login'; include 'navbaar.php'; ?>

<div id="login_page_bg">

  <div id="login_hero">
    <p id="login_tag">Welcome Back</p>
    <h1 id="login_title">Sign in to <em>ComfyGo</em></h1>
    <p id="login_sub">Access your bookings, saved trips, and account settings.</p>
  </div>

  <div id="login_wrapper">
    <div id="login_card">

      <?php if ($error): ?>
        <div id="login_error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php" id="login_form">

        <div id="field_email" class="login_field">
          <label for="email">Email Address</label>
          <div class="field_wrap">
            <i class="fa-regular fa-envelope field_icon"></i>
            <input type="email" id="email" name="email" placeholder="you@example.com"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              required autofocus autocomplete="email">
          </div>
        </div>

        <div id="field_password" class="login_field">
          <div id="pw_label_row">
            <label for="password">Password</label>
            <a href="forgot_password.php" id="forgot_link">Forgot password?</a>
          </div>
          <div class="field_wrap">
            <i class="fa-solid fa-lock field_icon"></i>
            <input type="password" id="password" name="password"
              placeholder="Enter your password" required autocomplete="current-password">
          </div>
        </div>

        <button type="submit" id="login_submit">Sign In</button>

      </form>

      <p id="signup_text">Don't have an account? <a href="signup.php" id="signup_link">Create one free</a></p>

    </div>
  </div>

</div>

</body>
</html>