<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['manager_id']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}

$manager_id   = $_SESSION['manager_id'];
$manager_name = $_SESSION['manager_name'];

$error   = '';
$success = '';

$stmt = $pdo->prepare("
    SELECT m.manager_ID, m.manager_name, m.manager_email, m.manager_mobile,
           m.hotel_registration_number,
           h.hotel_name, h.hotel_division, h.hotel_district,
           h.hotel_location, h.hotel_rating, h.hotel_price
    FROM   Manager m
    LEFT JOIN Hotels h ON m.hotel_registration_number = h.hotel_registration_number
    WHERE  m.manager_ID = ?
");
$stmt->execute([$manager_id]);
$manager = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $new_name   = trim($_POST['manager_name']   ?? '');
        $new_email  = trim($_POST['manager_email']  ?? '');
        $new_mobile = trim($_POST['manager_mobile'] ?? '');

        if (!$new_name || !$new_email || !$new_mobile) {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            /* Check email isn't taken by another manager */
            $ck = $pdo->prepare("SELECT manager_ID FROM Manager WHERE manager_email = ? AND manager_ID != ? LIMIT 1");
            $ck->execute([$new_email, $manager_id]);
            if ($ck->fetch()) {
                $error = 'That email is already in use by another manager.';
            } else {
                $upd = $pdo->prepare("UPDATE Manager SET manager_name=?, manager_email=?, manager_mobile=? WHERE manager_ID=?");
                $upd->execute([$new_name, $new_email, $new_mobile, $manager_id]);
                $_SESSION['manager_name'] = $new_name;
                $manager_name = $new_name;
                $success = 'Profile updated successfully.';
            }
        }
    } elseif ($action === 'update_hotel') {
        $new_hotel_name     = trim($_POST['hotel_name']     ?? '');
        $new_hotel_division = trim($_POST['hotel_division'] ?? '');
        $new_hotel_district = trim($_POST['hotel_district'] ?? '');
        $new_hotel_location = trim($_POST['hotel_location'] ?? '');
        $new_hotel_rating   = trim($_POST['hotel_rating']   ?? '');
        $new_hotel_price    = (int)($_POST['hotel_price']   ?? 0);

        if (!$new_hotel_name || !$new_hotel_division || !$new_hotel_district || !$new_hotel_location) {
            $error = 'Please fill in all hotel fields.';
        } else {
            $upd = $pdo->prepare("UPDATE Hotels SET hotel_name=?, hotel_division=?, hotel_district=?, hotel_location=?, hotel_rating=?, hotel_price=? WHERE hotel_registration_number=?");
            $upd->execute([$new_hotel_name, $new_hotel_division, $new_hotel_district, $new_hotel_location, $new_hotel_rating, $new_hotel_price, $manager['hotel_registration_number']]);
            $success = 'Hotel details updated successfully.';
        }

    } elseif ($action === 'logout') {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT m.manager_ID, m.manager_name, m.manager_email, m.manager_mobile,
               m.hotel_registration_number,
               h.hotel_name, h.hotel_division, h.hotel_district,
               h.hotel_location, h.hotel_rating, h.hotel_price
        FROM   Manager m
        LEFT JOIN Hotels h ON m.hotel_registration_number = h.hotel_registration_number
        WHERE  m.manager_ID = ?
    ");
    $stmt->execute([$manager_id]);
    $manager = $stmt->fetch();
    $manager_name = $manager['manager_name'];
}

$bq = $pdo->prepare("
    SELECT b.booking_ID,
           b.booking_date,
           b.booking_confirmation,
           u.user_name,
           u.user_phone
    FROM   Booking b
    JOIN   Users   u ON b.user_ID = u.user_ID
    WHERE  b.booking_Type = 'Hotel'
      AND  b.hotel_registration_number = ?
    ORDER  BY b.booking_date DESC
");
$bq->execute([$manager['hotel_registration_number']]);
$hotel_bookings = $bq->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/manager.css">
</head>
<body>

<?php $active_page = ''; include 'navbaar.php'; ?>

<div id="manager_page">
  <div id="manager_topbar">
    <div id="manager_welcome">
      <p id="manager_welcome_tag">Hotel Manager Dashboard</p>
      <h1 id="manager_welcome_name">Welcome, <?= htmlspecialchars($manager_name) ?></h1>
      <p id="manager_welcome_id">Manager ID: <?= htmlspecialchars($manager_id) ?></p>
    </div>
    <form method="POST" action="manager.php">
      <input type="hidden" name="action" value="logout">
      <button type="submit" id="manager_logout_btn">Logout</button>
    </form>
  </div>

  <?php if ($error): ?>
    <div class="m_alert m_alert_error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="m_alert m_alert_success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div id="manager_body">

    <div id="manager_sidebar">
      <nav id="manager_nav">
        <a href="#overview"     class="m_nav_link">Overview</a>
        <a href="#bookings"     class="m_nav_link">Hotel Bookings</a>
        <a href="#hotel_edit"   class="m_nav_link">Hotel Details</a>
        <a href="#profile_edit" class="m_nav_link">My Profile</a>
      </nav>
    </div>

    <div id="manager_content">
      <section class="m_section" id="overview">
        <h2 class="m_section_title">Overview</h2>
        <p class="m_section_sub">Your hotel and account information at a glance.</p>

        <h3 class="m_sub_heading">Manager Info</h3>
        <div class="m_info_grid">
          <div class="m_info_card">
            <p class="m_info_label">Full Name</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['manager_name']) ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Email</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['manager_email']) ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Mobile</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['manager_mobile']) ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Hotel Reg. No.</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['hotel_registration_number']) ?></p>
          </div>
        </div>

        <h3 class="m_sub_heading" style="margin-top:2rem;">Hotel Info</h3>
        <div class="m_info_grid">
          <div class="m_info_card">
            <p class="m_info_label">Hotel Name</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['hotel_name'] ?? '—') ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Division</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['hotel_division'] ?? '—') ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">District</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['hotel_district'] ?? '—') ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Location</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['hotel_location'] ?? '—') ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Rating</p>
            <p class="m_info_value"><?= htmlspecialchars($manager['hotel_rating'] ?? '—') ?></p>
          </div>
          <div class="m_info_card">
            <p class="m_info_label">Price per Night (৳)</p>
            <p class="m_info_value">৳<?= number_format($manager['hotel_price'] ?? 0) ?></p>
          </div>
        </div>
      </section>

      <section class="m_section" id="bookings">
        <h2 class="m_section_title">Hotel Bookings</h2>
        <p class="m_section_sub">All tourist bookings for your hotel.</p>

        <?php if (empty($hotel_bookings)): ?>
          <p class="m_empty">No bookings yet. Tourists will be able to book once your hotel details are filled in.</p>
        <?php else: ?>
          <div class="m_table_wrap">
            <table class="m_table">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Tourist Name</th>
                  <th>Tourist Phone</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($hotel_bookings as $b): ?>
                <tr>
                  <td><?= htmlspecialchars($b['booking_ID']) ?></td>
                  <td><?= htmlspecialchars($b['user_name']) ?></td>
                  <td><?= htmlspecialchars($b['user_phone']) ?></td>
                  <td><?= htmlspecialchars($b['booking_date']) ?></td>
                  <td><span class="m_badge"><?= htmlspecialchars($b['booking_confirmation']) ?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <section class="m_section" id="hotel_edit">
        <h2 class="m_section_title">Update Hotel Details</h2>
        <p class="m_section_sub">Keep your hotel information accurate so tourists can find and book it.</p>

        <form method="POST" action="manager.php">
          <input type="hidden" name="action" value="update_hotel">

          <div class="m_field">
            <label>Hotel Name</label>
            <input type="text" name="hotel_name"
              value="<?= htmlspecialchars($manager['hotel_name'] ?? '') ?>" required>
          </div>
          <div class="m_field">
            <label>Division</label>
            <input type="text" name="hotel_division"
              value="<?= htmlspecialchars($manager['hotel_division'] ?? '') ?>" required>
          </div>
          <div class="m_field">
            <label>District</label>
            <input type="text" name="hotel_district"
              value="<?= htmlspecialchars($manager['hotel_district'] ?? '') ?>" required>
          </div>
          <div class="m_field">
            <label>Full Address / Location</label>
            <input type="text" name="hotel_location"
              value="<?= htmlspecialchars($manager['hotel_location'] ?? '') ?>" required>
          </div>
          <div class="m_field">
            <label>Star Rating</label>
            <select name="hotel_rating">
              <?php foreach (['1 Star','2 Star','3 Star','4 Star','5 Star'] as $r): ?>
                <option value="<?= $r ?>" <?= ($manager['hotel_rating'] ?? '') === $r ? 'selected' : '' ?>>
                  <?= $r ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="m_field">
            <label>Price per Night (৳)</label>
            <input type="number" name="hotel_price" min="0"
              value="<?= htmlspecialchars($manager['hotel_price'] ?? 0) ?>" required>
          </div>

          <button type="submit" class="m_btn_primary">Save Hotel Details</button>
        </form>
      </section>

      <section class="m_section" id="profile_edit">
        <h2 class="m_section_title">Update My Profile</h2>
        <p class="m_section_sub">Update your personal contact information.</p>

        <form method="POST" action="manager.php">
          <input type="hidden" name="action" value="update_profile">

          <div class="m_field">
            <label>Full Name</label>
            <input type="text" name="manager_name"
              value="<?= htmlspecialchars($manager['manager_name']) ?>" required>
          </div>
          <div class="m_field">
            <label>Email Address</label>
            <input type="email" name="manager_email"
              value="<?= htmlspecialchars($manager['manager_email']) ?>" required>
          </div>
          <div class="m_field">
            <label>Mobile Number</label>
            <input type="tel" name="manager_mobile"
              value="<?= htmlspecialchars($manager['manager_mobile']) ?>" required>
          </div>

          <button type="submit" class="m_btn_primary">Save Profile</button>
        </form>
      </section>

    </div>
  </div>
</div>

</body>
</html>