<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['guide_nid']) || $_SESSION['role'] !== 'guide') {
    header('Location: login.php');
    exit;
}

$guide_nid  = $_SESSION['guide_nid'];
$guide_name = $_SESSION['guide_name'];
$error   = '';
$success = '';

$stmt = $pdo->prepare("SELECT guide_NID, guide_name, guide_email, guide_mobile, guide_division, guide_district, guide_rate FROM Guide WHERE guide_NID = ?");
$stmt->execute([$guide_nid]);
$guide = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $new_name     = trim($_POST['guide_name']     ?? '');
        $new_email    = trim($_POST['guide_email']    ?? '');
        $new_mobile   = trim($_POST['guide_mobile']   ?? '');
        $new_division = trim($_POST['guide_division'] ?? '');
        $new_district = trim($_POST['guide_district'] ?? '');
        $new_rate     = (int)($_POST['guide_rate']    ?? 0);

        if (!$new_name || !$new_email || !$new_mobile || !$new_division || !$new_district) {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            $upd = $pdo->prepare("UPDATE Guide SET guide_name=?, guide_email=?, guide_mobile=?, guide_division=?, guide_district=?, guide_rate=? WHERE guide_NID=?");
            $upd->execute([$new_name, $new_email, $new_mobile, $new_division, $new_district, $new_rate, $guide_nid]);
            $_SESSION['guide_name'] = $new_name;
            $guide_name = $new_name;
            $success = 'Profile updated successfully.';
            $stmt = $pdo->prepare("SELECT guide_NID, guide_name, guide_email, guide_mobile, guide_division, guide_district, guide_rate FROM Guide WHERE guide_NID = ?");
            $stmt->execute([$guide_nid]);
            $guide = $stmt->fetch();
        }

    } elseif ($action === 'approve_booking') {
        $booking_id = trim($_POST['booking_id'] ?? '');
        if ($booking_id) {
            $stmt = $pdo->prepare("UPDATE Booking SET booking_confirmation = 'Confirmed' WHERE booking_ID = ? AND guide_NID = ?");
            $stmt->execute([$booking_id, $guide_nid]);
            $success = 'Booking has been approved and confirmed.';
        } else {
            $error = 'Invalid booking ID.';
        }

    } elseif ($action === 'reject_booking') {
        $booking_id = trim($_POST['booking_id'] ?? '');
        if ($booking_id) {
            $stmt = $pdo->prepare("UPDATE Booking SET booking_confirmation = 'Rejected' WHERE booking_ID = ? AND guide_NID = ?");
            $stmt->execute([$booking_id, $guide_nid]);
            $success = 'Booking has been rejected.';
        } else {
            $error = 'Invalid booking ID.';
        }

    } elseif ($action === 'logout') {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

$pending_stmt = $pdo->prepare("
    SELECT b.booking_ID, b.booking_date, b.booking_confirmation,
           u.user_name, u.user_phone, u.user_email
    FROM   Booking b
    JOIN   Users u ON b.user_ID = u.user_ID
    WHERE  b.booking_Type = 'Guide' AND b.guide_NID = ? AND b.booking_confirmation = 'Pending'
    ORDER  BY b.booking_date ASC
");
$pending_stmt->execute([$guide_nid]);
$pending_bookings = $pending_stmt->fetchAll();

$history_stmt = $pdo->prepare("
    SELECT b.booking_ID, b.booking_date, b.booking_confirmation,
           u.user_name, u.user_phone
    FROM   Booking b
    JOIN   Users u ON b.user_ID = u.user_ID
    WHERE  b.booking_Type = 'Guide' AND b.guide_NID = ? AND b.booking_confirmation != 'Pending'
    ORDER  BY b.booking_date DESC
");
$history_stmt->execute([$guide_nid]);
$bookings = $history_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guide Dashboard — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/guide.css">
</head>
<body>

<?php $active_page = ''; include 'navbaar.php'; ?>

<div id="guide_page">

  <div id="guide_topbar">
    <div id="guide_welcome">
      <p id="guide_welcome_tag">Guide Dashboard</p>
      <h1 id="guide_welcome_name">Welcome, <?= htmlspecialchars($guide_name) ?></h1>
      <p id="guide_welcome_id">NID: <?= htmlspecialchars($guide_nid) ?></p>
    </div>
    <form method="POST" action="guide.php">
      <input type="hidden" name="action" value="logout">
      <button type="submit" id="guide_logout_btn">Logout</button>
    </form>
  </div>

  <?php if ($error): ?>
    <div class="g_alert g_alert_error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="g_alert g_alert_success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div id="guide_body">

    <div id="guide_sidebar">
      <nav id="guide_nav">
        <a href="#overview" class="g_nav_link">Overview</a>
        <a href="#bookings" class="g_nav_link">My Bookings</a>
        <a href="#profile"  class="g_nav_link">Profile</a>
      </nav>
    </div>

    <div id="guide_content">

      <section class="g_section" id="overview">
        <h2 class="g_section_title">Overview</h2>
        <p class="g_section_sub">Your current profile information at a glance.</p>
        <div id="guide_info_grid">
          <div class="g_info_card">
            <p class="g_info_label">Full Name</p>
            <p class="g_info_value"><?= htmlspecialchars($guide['guide_name']) ?></p>
          </div>
          <div class="g_info_card">
            <p class="g_info_label">Email</p>
            <p class="g_info_value"><?= htmlspecialchars($guide['guide_email']) ?></p>
          </div>
          <div class="g_info_card">
            <p class="g_info_label">Mobile</p>
            <p class="g_info_value"><?= htmlspecialchars($guide['guide_mobile']) ?></p>
          </div>
          <div class="g_info_card">
            <p class="g_info_label">Division</p>
            <p class="g_info_value"><?= htmlspecialchars($guide['guide_division']) ?></p>
          </div>
          <div class="g_info_card">
            <p class="g_info_label">District</p>
            <p class="g_info_value"><?= htmlspecialchars($guide['guide_district']) ?></p>
          </div>
          <div class="g_info_card">
            <p class="g_info_label">Rate per Day (৳)</p>
            <p class="g_info_value">৳<?= number_format($guide['guide_rate']) ?></p>
          </div>
        </div>
      </section>

      <section class="g_section" id="bookings">
        <h2 class="g_section_title">My Bookings</h2>
        <p class="g_section_sub">Tourists who have booked you as their guide.</p>

        <?php if (empty($pending_bookings) && empty($bookings)): ?>
          <p class="g_empty">No bookings yet. Your profile will appear to tourists once you set your rate.</p>
        <?php else: ?>

          <?php if (!empty($pending_bookings)): ?>
            <p class="g_section_label">Pending Approvals</p>
            <div class="g_booking_cards">
              <?php foreach ($pending_bookings as $b): ?>
              <div class="g_booking_card">
                <div class="g_booking_card_header">
                  <span class="g_booking_id">#<?= htmlspecialchars($b['booking_ID']) ?></span>
                  <span class="g_badge" style="background:#fff7ed;color:#c2410c;">Pending</span>
                </div>
                <div class="g_booking_card_body">
                  <div class="g_booking_row">
                    <span class="label">Tourist</span>
                    <?= htmlspecialchars($b['user_name']) ?>
                  </div>
                  <div class="g_booking_row">
                    <span class="label">Phone</span>
                    <?= htmlspecialchars($b['user_phone']) ?>
                  </div>
                  <div class="g_booking_row">
                    <span class="label">Date</span>
                    <?= htmlspecialchars($b['booking_date']) ?>
                  </div>
                </div>
                <div class="g_booking_card_actions">
                  <form method="POST" action="guide.php" style="flex:1;">
                    <input type="hidden" name="action" value="approve_booking">
                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($b['booking_ID']) ?>">
                    <button type="submit" class="g_btn_approve" style="width:100%;">Approve</button>
                  </form>
                  <form method="POST" action="guide.php" style="flex:1;">
                    <input type="hidden" name="action" value="reject_booking">
                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($b['booking_ID']) ?>">
                    <button type="submit" class="g_btn_reject" style="width:100%;" onclick="return confirm('Reject this booking?')">Reject</button>
                  </form>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($bookings)): ?>
            <p class="g_section_label" style="margin-top:28px;">Booking History</p>
            <div class="g_booking_cards">
              <?php foreach ($bookings as $b): ?>
              <div class="g_booking_card">
                <div class="g_booking_card_header">
                  <span class="g_booking_id">#<?= htmlspecialchars($b['booking_ID']) ?></span>
                  <span class="g_badge <?= $b['booking_confirmation'] === 'Confirmed' ? 'g_badge_confirmed' : 'g_badge_rejected' ?>">
                    <?= htmlspecialchars($b['booking_confirmation']) ?>
                  </span>
                </div>
                <div class="g_booking_card_body">
                  <div class="g_booking_row">
                    <span class="label">Tourist</span>
                    <?= htmlspecialchars($b['user_name']) ?>
                  </div>
                  <div class="g_booking_row">
                    <span class="label">Phone</span>
                    <?= htmlspecialchars($b['user_phone']) ?>
                  </div>
                  <div class="g_booking_row">
                    <span class="label">Date</span>
                    <?= htmlspecialchars($b['booking_date']) ?>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        <?php endif; ?>
      </section>

      <section class="g_section" id="profile">
        <h2 class="g_section_title">Update Profile</h2>
        <p class="g_section_sub">Keep your information up to date so tourists can find you.</p>

        <form method="POST" action="guide.php" id="guide_profile_form">
          <input type="hidden" name="action" value="update_profile">
          <div class="g_profile_grid">
            <div class="g_field">
              <label>Full Name</label>
              <input type="text" name="guide_name" value="<?= htmlspecialchars($guide['guide_name']) ?>" required>
            </div>
            <div class="g_field">
              <label>Email Address</label>
              <input type="email" name="guide_email" value="<?= htmlspecialchars($guide['guide_email']) ?>" required>
            </div>
            <div class="g_field">
              <label>Mobile Number</label>
              <input type="tel" name="guide_mobile" value="<?= htmlspecialchars($guide['guide_mobile']) ?>" required>
            </div>
            <div class="g_field">
              <label>Division</label>
              <input type="text" name="guide_division" value="<?= htmlspecialchars($guide['guide_division']) ?>" required>
            </div>
            <div class="g_field">
              <label>District</label>
              <input type="text" name="guide_district" value="<?= htmlspecialchars($guide['guide_district']) ?>" required>
            </div>
            <div class="g_field">
              <label>Rate per Day (৳)</label>
              <input type="number" name="guide_rate" value="<?= htmlspecialchars($guide['guide_rate']) ?>" min="0" required>
            </div>
          </div>
          <button type="submit" class="g_btn_primary">Save Changes</button>
        </form>
      </section>

    </div>
  </div>
</div>

</body>
</html>