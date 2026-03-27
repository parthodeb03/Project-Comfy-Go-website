<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $role = $_POST['role'] ?? 'tourist';

  if ($role === 'tourist') {
    $user_ID = trim($_POST['user_ID'] ?? '');
    $user_name = trim($_POST['user_name'] ?? '');
    $user_email = trim($_POST['user_email'] ?? '');
    $user_phone = trim($_POST['user_phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if (!$user_ID || !$user_name || !$user_email || !$user_phone || !$password || !$confirm) {
      $error = 'Please fill in all fields.';
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm) {
      $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
      $error = 'Password must be at least 6 characters.';
    } else {
      $check = $pdo->prepare("SELECT user_ID FROM Users WHERE user_ID = ? OR user_email = ? LIMIT 1");
      $check->execute([$user_ID, $user_email]);

      if ($check->fetch()) {
        $error = 'User ID or email already exists.';
      } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Users (user_ID, user_email, user_name, user_phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_ID, $user_email, $user_name, $user_phone, $hashed]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_ID;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['role'] = 'tourist';

        header('Location: tourist.php');
        exit;
      }
    }

  } elseif ($role === 'guide') {
    $guide_NID = trim($_POST['guide_NID'] ?? '');
    $guide_name = trim($_POST['guide_name'] ?? '');
    $guide_email = trim($_POST['guide_email'] ?? '');
    $guide_mobile = trim($_POST['guide_mobile'] ?? '');
    $guide_division = trim($_POST['guide_division'] ?? '');
    $guide_district = trim($_POST['guide_district'] ?? '');

    if (!$guide_NID || !$guide_name || !$guide_email || !$guide_mobile || !$guide_division || !$guide_district) {
      $error = 'Please fill in all fields.';
    } elseif (!filter_var($guide_email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Please enter a valid email address.';
    } else {
      $check = $pdo->prepare("SELECT guide_NID FROM Guide WHERE guide_NID = ? LIMIT 1");
      $check->execute([$guide_NID]);
      if ($check->fetch()) {
        $error = 'A guide with this NID already exists.';
      } else {
        $stmt = $pdo->prepare("INSERT INTO Guide (guide_NID, guide_name, guide_email, guide_mobile, guide_division, guide_district) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$guide_NID, $guide_name, $guide_email, $guide_mobile, $guide_division, $guide_district]);
        $success = 'Guide account created successfully!';
      }
    }

  } elseif ($role === 'manager') {
    $manager_ID = trim($_POST['manager_ID'] ?? '');
    $manager_name = trim($_POST['manager_name'] ?? '');
    $manager_email = trim($_POST['manager_email'] ?? '');
    $manager_mobile = trim($_POST['manager_mobile'] ?? '');
    $hotel_reg = trim($_POST['hotel_registration_number'] ?? '');

    if (!$manager_ID || !$manager_name || !$manager_email || !$manager_mobile || !$hotel_reg) {
      $error = 'Please fill in all fields.';
    } elseif (!filter_var($manager_email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Please enter a valid email address.';
    } else {
      $check = $pdo->prepare("SELECT manager_ID FROM Manager WHERE manager_ID = ? OR manager_email = ? LIMIT 1");
      $check->execute([$manager_ID, $manager_email]);
      if ($check->fetch()) {
        $error = 'Manager ID or email already exists.';
      } else {
        $hotelCheck = $pdo->prepare("SELECT hotel_registration_number FROM Hotels WHERE hotel_registration_number = ? LIMIT 1");
        $hotelCheck->execute([$hotel_reg]);
        if (!$hotelCheck->fetch()) {
          $error = 'Hotel registration number not found. Please enter a valid registered hotel.';
        } else {
          $stmt = $pdo->prepare("INSERT INTO Manager (manager_ID, manager_name, manager_email, manager_mobile, hotel_registration_number) VALUES (?, ?, ?, ?, ?)");
          try {
            $stmt->execute([$manager_ID, $manager_name, $manager_email, $manager_mobile, $hotel_reg]);
            $success = 'Manager account created successfully!';
          } catch (PDOException $e) {
            $error = 'Registration failed: invalid hotel registration number.';
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="styles/signup.css">
</head>

<body>

  <?php
  $active_page = 'signup';
  include 'navbaar.php';
  ?>

  <div id="register_page_bg">

    <div id="register_hero">
      <p id="register_tag">Join ComfyGo</p>
      <h1 id="register_title">Create your <em>account</em></h1>
      <p id="register_sub">Tell us who you are to get started.</p>
    </div>

    <div id="register_wrapper">

      <div id="role_selector">
        <p id="role_label">I am a...</p>
        <div id="role_cards">
          <label class="role_card <?= (!isset($_POST['role']) || $_POST['role'] === 'tourist') ? 'active' : '' ?>"
            id="role_tourist_card">
            <input type="radio" name="role_toggle" value="tourist" <?= (!isset($_POST['role']) || $_POST['role'] === 'tourist') ? 'checked' : '' ?> onclick="switchRole('tourist')">
            <span class="role_title">Tourist</span>
            <span class="role_desc">I want to explore Bangladesh</span>
          </label>
          <label class="role_card <?= (isset($_POST['role']) && $_POST['role'] === 'guide') ? 'active' : '' ?>"
            id="role_guide_card">
            <input type="radio" name="role_toggle" value="guide" <?= (isset($_POST['role']) && $_POST['role'] === 'guide') ? 'checked' : '' ?> onclick="switchRole('guide')">
            <span class="role_title">Guide</span>
            <span class="role_desc">I lead tours and experiences</span>
          </label>
          <label class="role_card <?= (isset($_POST['role']) && $_POST['role'] === 'manager') ? 'active' : '' ?>"
            id="role_manager_card">
            <input type="radio" name="role_toggle" value="manager" <?= (isset($_POST['role']) && $_POST['role'] === 'manager') ? 'checked' : '' ?> onclick="switchRole('manager')">
            <span class="role_title">Hotel Manager</span>
            <span class="role_desc">I manage a certified hotel</span>
          </label>
        </div>
      </div>

      <div id="register_card">

        <?php if ($error): ?>
          <div id="form_error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div id="form_success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="signup.php" id="register_form">
          <input type="hidden" name="role" id="role_input" value="<?= htmlspecialchars($_POST['role'] ?? 'tourist') ?>">

          <?php $role = $_POST['role'] ?? 'tourist'; ?>

          <div id="form_tourist" class="role_form" <?= $role !== 'tourist' ? 'style="display:none;"' : '' ?>>
            <div class="reg_field">
              <label>User ID</label>
              <input type="text" name="user_ID" placeholder="Choose a unique user ID"
                value="<?= htmlspecialchars($_POST['user_ID'] ?? '') ?>" required>
            </div>
            <div class="reg_field">
              <label>Full Name</label>
              <input type="text" name="user_name" placeholder="Your full name"
                value="<?= htmlspecialchars($_POST['user_name'] ?? '') ?>" required autocomplete="name">
            </div>
            <div class="reg_field">
              <label>Email Address</label>
              <input type="email" name="user_email" placeholder="you@example.com"
                value="<?= htmlspecialchars($_POST['user_email'] ?? '') ?>" required autocomplete="email">
            </div>
            <div class="reg_field">
              <label>Phone Number</label>
              <input type="tel" name="user_phone" placeholder="+880 1XXX XXXXXX"
                value="<?= htmlspecialchars($_POST['user_phone'] ?? '') ?>" required autocomplete="tel">
            </div>
            <div class="reg_field">
              <label>Password</label>
              <input type="password" name="password" placeholder="At least 6 characters" required
                autocomplete="new-password">
            </div>
            <div class="reg_field">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" placeholder="Repeat your password" required
                autocomplete="new-password">
            </div>
          </div>

          <div id="form_guide" class="role_form" <?= $role !== 'guide' ? 'style="display:none;"' : '' ?>>
            <div class="reg_field">
              <label>National ID (NID)</label>
              <input type="text" name="guide_NID" placeholder="Your NID number"
                value="<?= htmlspecialchars($_POST['guide_NID'] ?? '') ?>">
            </div>
            <div class="reg_field">
              <label>Full Name</label>
              <input type="text" name="guide_name" placeholder="Your full name"
                value="<?= htmlspecialchars($_POST['guide_name'] ?? '') ?>" autocomplete="name">
            </div>
            <div class="reg_field">
              <label>Email Address</label>
              <input type="email" name="guide_email" placeholder="you@example.com"
                value="<?= htmlspecialchars($_POST['guide_email'] ?? '') ?>" autocomplete="email">
            </div>
            <div class="reg_field">
              <label>Mobile Number</label>
              <input type="tel" name="guide_mobile" placeholder="+880 1XXX XXXXXX"
                value="<?= htmlspecialchars($_POST['guide_mobile'] ?? '') ?>" autocomplete="tel">
            </div>
            <div class="reg_field">
              <label>Division</label>
              <input type="text" name="guide_division" placeholder="e.g. Sylhet"
                value="<?= htmlspecialchars($_POST['guide_division'] ?? '') ?>">
            </div>
            <div class="reg_field">
              <label>District</label>
              <input type="text" name="guide_district" placeholder="e.g. Moulvibazar"
                value="<?= htmlspecialchars($_POST['guide_district'] ?? '') ?>">
            </div>
          </div>

          <div id="form_manager" class="role_form" <?= $role !== 'manager' ? 'style="display:none;"' : '' ?>>
            <div class="reg_field">
              <label>Manager ID</label>
              <input type="text" name="manager_ID" placeholder="Choose a unique manager ID"
                value="<?= htmlspecialchars($_POST['manager_ID'] ?? '') ?>">
            </div>
            <div class="reg_field">
              <label>Full Name</label>
              <input type="text" name="manager_name" placeholder="Your full name"
                value="<?= htmlspecialchars($_POST['manager_name'] ?? '') ?>" autocomplete="name">
            </div>
            <div class="reg_field">
              <label>Email Address</label>
              <input type="email" name="manager_email" placeholder="you@example.com"
                value="<?= htmlspecialchars($_POST['manager_email'] ?? '') ?>" autocomplete="email">
            </div>
            <div class="reg_field">
              <label>Mobile Number</label>
              <input type="tel" name="manager_mobile" placeholder="+880 1XXX XXXXXX"
                value="<?= htmlspecialchars($_POST['manager_mobile'] ?? '') ?>" autocomplete="tel">
            </div>
            <div class="reg_field">
              <label>Hotel Registration Number</label>
              <input type="text" name="hotel_registration_number" placeholder="Official hotel reg. number"
                value="<?= htmlspecialchars($_POST['hotel_registration_number'] ?? '') ?>">
            </div>
          </div>

          <button type="submit" id="register_submit">Create Account</button>
        </form>

        <p id="signin_text">Already have an account? <a href="login.php" id="signin_link">Sign in</a></p>
      </div>
    </div>
  </div>

  <script>
    function switchRole(val) {
      document.querySelectorAll('.role_form').forEach(f => f.style.display = 'none');
      const target = document.getElementById('form_' + val);
      if (target) target.style.display = 'flex';
      document.getElementById('role_input').value = val;
      document.querySelectorAll('.role_card').forEach(c => c.classList.remove('active'));
      document.getElementById('role_' + val + '_card').classList.add('active');
    }
  </script>

</body>

</html>