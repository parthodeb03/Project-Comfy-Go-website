<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$error = '';
$success = '';

$user = $pdo->prepare("SELECT * FROM Users WHERE user_ID = ?");
$user->execute([$user_id]);
$user = $user->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'update_profile') {
    $new_name = trim($_POST['user_name'] ?? '');
    $new_email = trim($_POST['user_email'] ?? '');
    $new_phone = trim($_POST['user_phone'] ?? '');

    if (!$new_name || !$new_email || !$new_phone) {
      $error = 'Please fill in all fields.';
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Invalid email address.';
    } else {
      $stmt = $pdo->prepare("UPDATE Users SET user_name = ?, user_email = ?, user_phone = ? WHERE user_ID = ?");
      $stmt->execute([$new_name, $new_email, $new_phone, $user_id]);
      $_SESSION['user_name'] = $new_name;
      $user_name = $new_name;
      $success = 'Profile updated successfully.';
      $user = $pdo->prepare("SELECT * FROM Users WHERE user_ID = ?");
      $user->execute([$user_id]);
      $user = $user->fetch();
    }
  } elseif ($action === 'book_transport') {
    $transport_id = trim($_POST['transport_id'] ?? '');
    $travel_date = trim($_POST['travel_date'] ?? '');

    if (!$transport_id || !$travel_date) {
      $error = 'Please select transport and travel date.';
    } else {
      $booking_id = 'BK' . strtoupper(uniqid());
      $stmt = $pdo->prepare("INSERT INTO Booking (booking_ID, booking_Type, booking_confirmation, user_ID, booking_date) VALUES (?, 'Transport', 'Confirmed', ?, ?)");
      $stmt->execute([$booking_id, $user_id, $travel_date]);

      $fare = $pdo->prepare("SELECT transport_fare FROM Transportation WHERE transport_ID = ?");
      $fare->execute([$transport_id]);
      $fare = $fare->fetchColumn();

      $payment_id = 'PY' . strtoupper(uniqid());
      $stmt2 = $pdo->prepare("INSERT INTO Payment (payment_ID, booking_ID, price, user_ID) VALUES (?, ?, ?, ?)");
      $stmt2->execute([$payment_id, $booking_id, $fare, $user_id]);

      $success = 'Transport booked successfully! Booking ID: ' . $booking_id;
    }
  } elseif ($action === 'book_hotel') {
    $hotel_reg = trim($_POST['hotel_reg'] ?? '');
    $checkin = trim($_POST['checkin'] ?? '');

    if (!$hotel_reg || !$checkin) {
      $error = 'Please select a hotel and check-in date.';
    } else {
      $priceStmt = $pdo->prepare("SELECT hotel_price FROM Hotels WHERE hotel_registration_number = ?");
      $priceStmt->execute([$hotel_reg]);
      $hotel_price = $priceStmt->fetchColumn();

      if ($hotel_price === false) {
        $error = 'Selected hotel not found.';
      } else {
        $booking_id = 'BK' . strtoupper(uniqid());
        $stmt = $pdo->prepare("INSERT INTO Booking (booking_ID, booking_Type, booking_confirmation, user_ID, booking_date) VALUES (?, 'Hotel', 'Confirmed', ?, ?)");
        $stmt->execute([$booking_id, $user_id, $checkin]);

        $payment_id = 'PY' . strtoupper(uniqid());
        $stmt2 = $pdo->prepare("INSERT INTO Payment (payment_ID, booking_ID, price, user_ID) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$payment_id, $booking_id, $hotel_price, $user_id]);

        $success = 'Hotel booked successfully! Booking ID: ' . $booking_id;
      }
    }
  } elseif ($action === 'book_guide') {
    $guide_nid = trim($_POST['guide_nid'] ?? '');
    $guide_date = trim($_POST['guide_date'] ?? '');

    if (!$guide_nid || !$guide_date) {
      $error = 'Please select a guide and date.';
    } else {
      $rateStmt = $pdo->prepare("SELECT guide_rate FROM Guide WHERE guide_NID = ?");
      $rateStmt->execute([$guide_nid]);
      $guide_rate = $rateStmt->fetchColumn();

      if ($guide_rate === false) {
        $error = 'Selected guide not found.';
      } else {
        $booking_id = 'BK' . strtoupper(uniqid());
        $stmt = $pdo->prepare("INSERT INTO Booking (booking_ID, booking_Type, booking_confirmation, user_ID, booking_date) VALUES (?, 'Guide', 'Confirmed', ?, ?)");
        $stmt->execute([$booking_id, $user_id, $guide_date]);

        $payment_id = 'PY' . strtoupper(uniqid());
        $stmt2 = $pdo->prepare("INSERT INTO Payment (payment_ID, booking_ID, price, user_ID) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$payment_id, $booking_id, $guide_rate, $user_id]);

        $success = 'Guide booked successfully! Booking ID: ' . $booking_id;
      }
    }
  } elseif ($action === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
  }
}

$transports = $pdo->query("SELECT * FROM Transportation ORDER BY transport_route")->fetchAll();

$division_filter = $_GET['division'] ?? '';
$hotel_query = $division_filter
? $pdo->prepare("SELECT * FROM Hotels WHERE hotel_division = ?") : $pdo->prepare("SELECT * FROM Hotels");
$division_filter ? $hotel_query->execute([$division_filter]) : $hotel_query->execute();
$hotels = $hotel_query->fetchAll();

$guide_div = $_GET['guide_division'] ?? '';
$guide_query = $guide_div
  ? $pdo->prepare("SELECT * FROM Guide WHERE guide_division = ?")
  : $pdo->prepare("SELECT * FROM Guide");
$guide_div ? $guide_query->execute([$guide_div]) : $guide_query->execute();
$guides = $guide_query->fetchAll();

$bookings = $pdo->prepare("
    SELECT b.*, p.price FROM Booking b
    LEFT JOIN Payment p ON b.booking_ID = p.booking_ID
    WHERE b.user_ID = ?
    ORDER BY b.booking_date DESC
");
$bookings->execute([$user_id]);
$bookings = $bookings->fetchAll();

$transport_route_filter = $_GET['route'] ?? '';
if ($transport_route_filter) {
  $transports = array_filter($transports, fn($t) => stripos($t['transport_route'], $transport_route_filter) !== false);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="styles/tourist.css">
</head>

<body>

  <?php $active_page = 'tourist'; ?>

  <div id="tourist_page">

    <div id="tourist_topbar">
      <div id="tourist_welcome">
        <p id="tourist_welcome_tag">Tourist Dashboard</p>
        <h1 id="tourist_welcome_name">Welcome, <?= htmlspecialchars($user_name) ?></h1>
        <p id="tourist_welcome_id">ID: <?= htmlspecialchars($user_id) ?></p>
      </div>
      <form method="POST" action="tourist.php">
        <input type="hidden" name="action" value="logout">
        <button type="submit" id="logout_btn">Logout</button>
      </form>
    </div>

    <?php if ($error): ?>
      <div class="t_alert t_alert_error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="t_alert t_alert_success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div id="tourist_body">

      <div id="tourist_sidebar">
        <nav id="tourist_nav">
          <a href="#transport" class="t_nav_link">Transport</a>
          <a href="#hotels" class="t_nav_link">Hotels</a>
          <a href="#guides" class="t_nav_link">Guides</a>
          <a href="#bookings" class="t_nav_link">My Bookings</a>
          <a href="#profile" class="t_nav_link">Profile</a>
        </nav>
      </div>

      <div id="tourist_content">

        <section class="t_section" id="transport">
          <h2 class="t_section_title">Book Transport</h2>
          <p class="t_section_sub">Select your route and travel type.</p>

          <div id="route_filter">
            <a href="?route=" class="route_btn <?= !$transport_route_filter ? 'active' : '' ?>">All Routes</a>
            <a href="?route=Dhaka"
              class="route_btn <?= $transport_route_filter === 'Dhaka' ? 'active' : '' ?>">Dhaka</a>
            <a href="?route=Sylhet"
              class="route_btn <?= $transport_route_filter === 'Sylhet' ? 'active' : '' ?>">Sylhet</a>
            <a href="?route=Chittagong"
              class="route_btn <?= $transport_route_filter === 'Chittagong' ? 'active' : '' ?>">Chittagong</a>
          </div>

          <?php if (empty($transports)): ?>
            <p class="t_empty">No transport options available for this route.</p>
          <?php else: ?>
            <div class="t_table_wrap">
              <table class="t_table">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Route</th>
                    <th>Fare (৳/person)</th>
                    <th>Book</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($transports as $t): ?>
                    <tr>
                      <td><?= htmlspecialchars($t['transport_type']) ?></td>
                      <td><?= htmlspecialchars($t['transport_route']) ?></td>
                      <td>৳<?= number_format($t['transport_fare']) ?></td>
                      <td>
                        <form method="POST" action="tourist.php" class="inline_form">
                          <input type="hidden" name="action" value="book_transport">
                          <input type="hidden" name="transport_id" value="<?= htmlspecialchars($t['transport_ID']) ?>">
                          <input type="date" name="travel_date" required class="t_date_input">
                          <button type="submit" class="t_btn_sm">Book</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

        <section class="t_section" id="hotels">
          <h2 class="t_section_title">Book a Hotel</h2>
          <p class="t_section_sub">Filter by division to find certified hotels.</p>

          <div id="hotel_filter">
            <a href="?division=#hotels" class="route_btn <?= !$division_filter ? 'active' : '' ?>">All</a>
            <a href="?division=Dhaka#hotels"
              class="route_btn <?= $division_filter === 'Dhaka' ? 'active' : '' ?>">Dhaka</a>
            <a href="?division=Sylhet#hotels"
              class="route_btn <?= $division_filter === 'Sylhet' ? 'active' : '' ?>">Sylhet</a>
            <a href="?division=Chittagong#hotels"
              class="route_btn <?= $division_filter === 'Chittagong' ? 'active' : '' ?>">Chittagong</a>
          </div>

          <?php if (empty($hotels)): ?>
            <p class="t_empty">No hotels found for this division.</p>
          <?php else: ?>
            <div class="t_table_wrap">
              <table class="t_table">
                <thead>
                  <tr>
                    <th>Hotel Name</th>
                    <th>Division</th>
                    <th>District</th>
                    <th>Rating</th>
                    <th>Book</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($hotels as $h): ?>
                    <tr>
                      <td><?= htmlspecialchars($h['hotel_name']) ?></td>
                      <td><?= htmlspecialchars($h['hotel_division']) ?></td>
                      <td><?= htmlspecialchars($h['hotel_district']) ?></td>
                      <td><?= htmlspecialchars($h['hotel_rating']) ?></td>
                      <td>
                        <form method="POST" action="tourist.php" class="inline_form">
                          <input type="hidden" name="action" value="book_hotel">
                          <input type="hidden" name="hotel_reg"
                            value="<?= htmlspecialchars($h['hotel_registration_number']) ?>">
                          <input type="date" name="checkin" required class="t_date_input">
                          <button type="submit" class="t_btn_sm">Book</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

        <section class="t_section" id="guides">
          <h2 class="t_section_title">Book a Guide</h2>
          <p class="t_section_sub">Find certified local guides by division.</p>

          <div id="guide_filter">
            <a href="?guide_division=#guides" class="route_btn <?= !$guide_div ? 'active' : '' ?>">All</a>
            <a href="?guide_division=Dhaka#guides"
              class="route_btn <?= $guide_div === 'Dhaka' ? 'active' : '' ?>">Dhaka</a>
            <a href="?guide_division=Sylhet#guides"
              class="route_btn <?= $guide_div === 'Sylhet' ? 'active' : '' ?>">Sylhet</a>
            <a href="?guide_division=Chittagong#guides"
              class="route_btn <?= $guide_div === 'Chittagong' ? 'active' : '' ?>">Chittagong</a>
          </div>

          <?php if (empty($guides)): ?>
            <p class="t_empty">No guides found for this division.</p>
          <?php else: ?>
            <div class="t_table_wrap">
              <table class="t_table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Division</th>
                    <th>District</th>
                    <th>Contact</th>
                    <th>Book</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($guides as $g): ?>
                    <tr>
                      <td><?= htmlspecialchars($g['guide_name']) ?></td>
                      <td><?= htmlspecialchars($g['guide_division']) ?></td>
                      <td><?= htmlspecialchars($g['guide_district']) ?></td>
                      <td><?= htmlspecialchars($g['guide_mobile']) ?></td>
                      <td>
                        <form method="POST" action="tourist.php" class="inline_form">
                          <input type="hidden" name="action" value="book_guide">
                          <input type="hidden" name="guide_nid" value="<?= htmlspecialchars($g['guide_NID']) ?>">
                          <input type="date" name="guide_date" required class="t_date_input">
                          <button type="submit" class="t_btn_sm">Book</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

        <section class="t_section" id="bookings">
          <h2 class="t_section_title">My Bookings</h2>
          <p class="t_section_sub">Your past and upcoming bookings.</p>

          <?php if (empty($bookings)): ?>
            <p class="t_empty">You have no bookings yet.</p>
          <?php else: ?>
            <div class="t_table_wrap">
              <table class="t_table">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Price (৳)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($bookings as $b): ?>
                    <tr>
                      <td><?= htmlspecialchars($b['booking_ID']) ?></td>
                      <td><?= htmlspecialchars($b['booking_Type']) ?></td>
                      <td><?= htmlspecialchars($b['booking_date']) ?></td>
                      <td><span class="t_badge"><?= htmlspecialchars($b['booking_confirmation']) ?></span></td>
                      <td><?= $b['price'] ? number_format($b['price']) : '—' ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

        <section class="t_section" id="profile">
          <h2 class="t_section_title">Update Profile</h2>
          <p class="t_section_sub">Edit your account information.</p>

          <form method="POST" action="tourist.php" id="profile_form">
            <input type="hidden" name="action" value="update_profile">

            <div class="t_field">
              <label>Full Name</label>
              <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required>
            </div>
            <div class="t_field">
              <label>Email Address</label>
              <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']) ?>" required>
            </div>
            <div class="t_field">
              <label>Phone Number</label>
              <input type="tel" name="user_phone" value="<?= htmlspecialchars($user['user_phone']) ?>" required>
            </div>

            <button type="submit" class="t_btn_primary">Save Changes</button>
          </form>
        </section>

      </div>
    </div>
  </div>

</body>

</html>