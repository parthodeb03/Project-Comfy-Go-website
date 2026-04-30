<?php
session_start();
require_once 'db.php';

$error   = '';
$success = '';
$email   = '';
$step    = 1; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'verify_email') {
        $email = trim($_POST['email'] ?? '');

        if (!$email) {
            $error = 'Please enter your email address.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
        
            $stmt = $pdo->prepare("
                SELECT 'tourist' as type, user_ID as id, user_name as name, user_email as email FROM Users WHERE user_email = ?
                UNION
                SELECT 'guide' as type, guide_NID as id, guide_name as name, guide_email as email FROM Guide WHERE guide_email = ?
                UNION
                SELECT 'manager' as type, manager_ID as id, manager_name as name, manager_email as email FROM Manager WHERE manager_email = ?
                LIMIT 1
            ");
            $stmt->execute([$email, $email, $email]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['reset_user'] = $user;
                $step = 2;
                $error = '';
            } else {
                $error = 'No account found with this email address.';
            }
        }
    }


    elseif ($action === 'reset_password') {
        if (!isset($_SESSION['reset_user'])) {
            $error = 'Session expired. Please start over.';
            $step = 1;
        } else {
            $user = $_SESSION['reset_user'];
            $new_password     = trim($_POST['new_password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');

            if (!$new_password || !$confirm_password) {
                $error = 'Please fill in all password fields.';
                $step = 2;
            } elseif (strlen($new_password) < 6) {
                $error = 'Password must be at least 6 characters.';
                $step = 2;
            } elseif ($new_password !== $confirm_password) {
                $error = 'Passwords do not match.';
                $step = 2;
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                if ($user['type'] === 'tourist') {
                    $stmt = $pdo->prepare("UPDATE Users SET password = ? WHERE user_ID = ?");
                } elseif ($user['type'] === 'guide') {
                    $stmt = $pdo->prepare("UPDATE Guide SET password = ? WHERE guide_NID = ?");
                } elseif ($user['type'] === 'manager') {
                    $stmt = $pdo->prepare("UPDATE Manager SET password = ? WHERE manager_ID = ?");
                }

                $stmt->execute([$hashed, $user['id']]);

                unset($_SESSION['reset_user']);
                $success = 'Your password has been successfully reset. You can now log in with your new password.';
                $step = 1;
                $email = '';
            }
        }
    }


    elseif ($action === 'cancel') {
        unset($_SESSION['reset_user']);
        $step = 1;
        $email = '';
    }
}


if ($step === 2 && isset($_SESSION['reset_user'])) {
    $email = $_SESSION['reset_user']['email'];
}

$active_page = 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/login.css">
  <style>
    #login_success {
      background: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
      border-left: 4px solid #28a745;
    }
  </style>
</head>
<body>

<?php include 'navbaar.php'; ?>

<div id="login_page_bg">
  <div id="login_wrapper">
    <div id="login_card">

      <?php if ($success): ?>
        <div id="login_success"><?= htmlspecialchars($success) ?></div>
        <p style="text-align: center; margin: 20px 0;">
          <a href="login.php" style="color: #3a7d44; font-weight: 500;">Go to Login</a>
        </p>
      <?php else: ?>

      <h1 id="login_title"><?= $step === 1 ? 'Reset Your Password' : 'Set New Password' ?></h1>
      <p id="login_sub"><?= $step === 1 ? 'Enter your email address and we\'ll verify it exists.' : 'Enter a new password for your account.' ?></p>

      <?php if ($error): ?>
        <div id="login_error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($step === 1): ?>
      <form method="POST" action="forgot_password.php" id="login_form">
        <input type="hidden" name="action" value="verify_email">

        <div id="field_email" class="login_field">
          <label for="email">Email Address</label>
          <div class="field_wrap">
            <i class="fa-regular fa-envelope field_icon"></i>
            <input type="email" id="email" name="email"
                   placeholder="you@example.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   required autofocus autocomplete="email">
          </div>
        </div>

        <button type="submit" id="login_submit">Verify Email</button>
      </form>
      <?php else: ?>
      <form method="POST" action="forgot_password.php" id="login_form">
        <input type="hidden" name="action" value="reset_password">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

        <div id="field_password" class="login_field">
          <label for="new_password">New Password</label>
          <div class="field_wrap">
            <i class="fa-solid fa-lock field_icon"></i>
            <input type="password" id="new_password" name="new_password"
                   placeholder="Enter new password (min. 6 chars)"
                   required autocomplete="new-password">
          </div>
        </div>

        <div id="field_password" class="login_field">
          <label for="confirm_password">Confirm New Password</label>
          <div class="field_wrap">
            <i class="fa-solid fa-lock field_icon"></i>
            <input type="password" id="confirm_password" name="confirm_password"
                   placeholder="Repeat new password"
                   required autocomplete="new-password">
          </div>
        </div>

        <button type="submit" id="login_submit">Reset Password</button>
      </form>

      <p style="text-align: center; margin-top: 15px;">
        <a href="forgot_password.php?cancel=1" style="color: #6c757d; font-size: 0.9rem;">Cancel</a>
      </p>
      <?php endif; ?>

      <p id="signup_text">Remember your password? <a href="login.php" id="signup_link">Sign in</a></p>

      <?php endif; ?>

    </div>
  </div>
</div>

</body>
</html>
