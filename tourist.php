<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
  header('Location: login.php');
  exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$error   = '';
$success = '';

$userStmt = $pdo->prepare("SELECT * FROM Users WHERE user_ID = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'update_profile') {
    $new_name  = trim($_POST['user_name']  ?? '');
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
      $r = $pdo->prepare("SELECT * FROM Users WHERE user_ID = ?");
      $r->execute([$user_id]);
      $user = $r->fetch();
    }

  } elseif ($action === 'book_transport') {
    $transport_id = trim($_POST['transport_id'] ?? '');
    $travel_date  = trim($_POST['travel_date']  ?? '');
    if (!$transport_id || !$travel_date) {
      $error = 'Please select transport and travel date.';
    } else {
       $booking_id = 'BK' . strtoupper(uniqid());
       $stmt = $pdo->prepare("INSERT INTO Booking (booking_ID, booking_Type, booking_confirmation, user_ID, booking_date, transport_ID) VALUES (?, 'Transport', 'Pending', ?, ?, ?)");
       $stmt->execute([$booking_id, $user_id, $travel_date, $transport_id]);
      $fare = $pdo->prepare("SELECT transport_fare FROM Transportation WHERE transport_ID = ?");
      $fare->execute([$transport_id]);
      $fare = $fare->fetchColumn();
      $payment_id = 'PY' . strtoupper(uniqid());
      $stmt2 = $pdo->prepare("INSERT INTO Payment (payment_ID, booking_ID, price, user_ID, payment_date) VALUES (?, ?, ?, ?, CURDATE())");
      $stmt2->execute([$payment_id, $booking_id, $fare, $user_id]);
      $success = 'Transport booked! Booking ID: ' . $booking_id;
    }

  } elseif ($action === 'book_hotel') {
    $hotel_reg = trim($_POST['hotel_reg'] ?? '');
    $checkin   = trim($_POST['checkin']   ?? '');
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
         $stmt = $pdo->prepare("INSERT INTO Booking (booking_ID, booking_Type, booking_confirmation, user_ID, booking_date, hotel_registration_number) VALUES (?, 'Hotel', 'Pending', ?, ?, ?)");
         $stmt->execute([$booking_id, $user_id, $checkin, $hotel_reg]);
        $payment_id = 'PY' . strtoupper(uniqid());
        $stmt2 = $pdo->prepare("INSERT INTO Payment (payment_ID, booking_ID, price, user_ID, payment_date) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt2->execute([$payment_id, $booking_id, $hotel_price, $user_id]);
        $success = 'Hotel booked! Booking ID: ' . $booking_id;
      }
    }

  } elseif ($action === 'book_guide') {
    $guide_nid  = trim($_POST['guide_nid']  ?? '');
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
        $stmt = $pdo->prepare("INSERT INTO Booking (booking_ID, booking_Type, booking_confirmation, user_ID, booking_date, guide_NID) VALUES (?, 'Guide', 'Pending', ?, ?, ?)");
        $stmt->execute([$booking_id, $user_id, $guide_date, $guide_nid]);
        $payment_id = 'PY' . strtoupper(uniqid());
        $stmt2 = $pdo->prepare("INSERT INTO Payment (payment_ID, booking_ID, price, user_ID, payment_date) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt2->execute([$payment_id, $booking_id, $guide_rate, $user_id]);
        $success = 'Guide booked! Booking ID: ' . $booking_id;
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
  ? $pdo->prepare("SELECT * FROM Hotels WHERE hotel_division = ?")
  : $pdo->prepare("SELECT * FROM Hotels");
$division_filter ? $hotel_query->execute([$division_filter]) : $hotel_query->execute();
$hotels = $hotel_query->fetchAll();

$guide_div = $_GET['guide_division'] ?? '';
$guide_query = $guide_div
  ? $pdo->prepare("SELECT * FROM Guide WHERE guide_division = ?")
  : $pdo->prepare("SELECT * FROM Guide");
$guide_div ? $guide_query->execute([$guide_div]) : $guide_query->execute();
$guides = $guide_query->fetchAll();

$bookingsStmt = $pdo->prepare("
  SELECT b.*, p.price FROM Booking b
  LEFT JOIN Payment p ON b.booking_ID = p.booking_ID
  WHERE b.user_ID = ?
  ORDER BY b.booking_date DESC
");
$bookingsStmt->execute([$user_id]);
$bookings = $bookingsStmt->fetchAll();

$transport_route_filter = $_GET['route'] ?? '';
if ($transport_route_filter) {
  $transports = array_filter($transports, fn($t) => stripos($t['transport_route'], $transport_route_filter) !== false);
}

function transport_icon(string $type): string {
  $t = strtolower($type);
  if (str_contains($t, 'bus'))    return '🚌';
  if (str_contains($t, 'train'))  return '🚆';
  if (str_contains($t, 'launch') || str_contains($t, 'boat')) return '⛴️';
  if (str_contains($t, 'air') || str_contains($t, 'flight'))  return '✈️';
  return '🚗';
}
function star_rating(float $r): string {
  $full = floor($r); $half = ($r - $full) >= 0.5 ? 1 : 0; $empty = 5 - $full - $half;
  return str_repeat('★', $full) . ($half ? '½' : '') . str_repeat('☆', $empty);
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
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:        #f7f4ef;
  --surface:   #ffffff;
  --surface2:  #f0ece4;
  --border:    #e0d9ce;
  --green:     #2d5a3d;
  --green-mid: #3d7a54;
  --green-lt:  #edf4ef;
  --amber:     #c8832a;
  --amber-lt:  #fdf3e3;
  --text:      #1a1a18;
  --muted:     #6b6860;
  --danger:    #b83c3c;
  --danger-lt: #fdf0f0;
  --success:   #2d5a3d;
  --success-lt:#edf4ef;
  --radius:    14px;
  --radius-sm: 8px;
  --shadow:    0 2px 12px rgba(45,90,61,.08), 0 1px 3px rgba(0,0,0,.06);
  --shadow-md: 0 8px 32px rgba(45,90,61,.12), 0 2px 8px rgba(0,0,0,.08);
}

html { scroll-behavior: smooth; }

body {
  background: var(--bg);
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  font-size: 15px;
  line-height: 1.6;
  min-height: 100vh;
}

#topbar {
  background: var(--green);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  height: 72px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 16px rgba(0,0,0,.18);
}
#topbar-brand {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px;
  font-weight: 600;
  letter-spacing: .02em;
  color: #fff;
}
#topbar-brand span { color: #a8d5b5; font-style: italic; }
#topbar-right { display: flex; align-items: center; gap: 16px; }
#topbar-user { font-size: 13px; opacity: .8; }
#topbar-user strong { display: block; font-size: 15px; opacity: 1; color: #fff; }
#logout-btn {
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.3);
  color: #fff;
  padding: 8px 18px;
  border-radius: 50px;
  cursor: pointer;
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 500;
  transition: background .2s;
}
#logout-btn:hover { background: rgba(255,255,255,.25); }

#layout { display: flex; min-height: calc(100vh - 72px); }
#sidebar {
  width: 220px;
  flex-shrink: 0;
  background: var(--surface);
  border-right: 1px solid var(--border);
  padding: 28px 0;
  position: sticky;
  top: 72px;
  height: calc(100vh - 72px);
  overflow-y: auto;
}
.sidebar-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--muted);
  padding: 0 20px 10px;
}
.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 20px;
  color: var(--muted);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  border-left: 3px solid transparent;
  transition: all .18s;
}
.nav-link:hover {
  color: var(--green);
  background: var(--green-lt);
  border-left-color: var(--green-mid);
}
.nav-icon { font-size: 16px; width: 20px; text-align: center; }
.nav-divider { height: 1px; background: var(--border); margin: 12px 20px; }

#main { flex: 1; padding: 36px 32px; max-width: 1100px; }

.alert {
  display: flex; align-items: center; gap: 10px;
  padding: 14px 18px;
  border-radius: var(--radius-sm);
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 500;
  animation: slideDown .3s ease;
}
@keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:none; } }
.alert-error   { background: var(--danger-lt);  color: var(--danger);  border: 1px solid #f0c4c4; }
.alert-success { background: var(--success-lt); color: var(--success); border: 1px solid #b8d9c4; }

.section { margin-bottom: 56px; }
.section-header { margin-bottom: 22px; }
.section-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 30px;
  font-weight: 600;
  color: var(--text);
  line-height: 1.2;
}
.section-sub { color: var(--muted); font-size: 14px; margin-top: 4px; }

.filter-bar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
.filter-pill {
  padding: 6px 16px;
  border-radius: 50px;
  border: 1.5px solid var(--border);
  background: var(--surface);
  color: var(--muted);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  transition: all .18s;
}
.filter-pill:hover { border-color: var(--green-mid); color: var(--green); }
.filter-pill.active { background: var(--green); border-color: var(--green); color: #fff; }

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 18px;
}


.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 22px;
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  gap: 14px;
  transition: box-shadow .2s, transform .2s;
  animation: cardIn .35s ease both;
}
.card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
@keyframes cardIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:none; } }

.card:nth-child(1) { animation-delay: .04s; }
.card:nth-child(2) { animation-delay: .08s; }
.card:nth-child(3) { animation-delay: .12s; }
.card:nth-child(4) { animation-delay: .16s; }
.card:nth-child(5) { animation-delay: .20s; }
.card:nth-child(6) { animation-delay: .24s; }

.card-icon {
  width: 44px; height: 44px;
  background: var(--green-lt);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
}
.card-name {
  font-weight: 600;
  font-size: 16px;
  color: var(--text);
  line-height: 1.3;
}
.card-meta {
  display: flex; flex-wrap: wrap; gap: 6px;
  font-size: 12px;
}
.tag {
  background: var(--surface2);
  color: var(--muted);
  padding: 3px 10px;
  border-radius: 50px;
  border: 1px solid var(--border);
  font-weight: 500;
}
.tag.green { background: var(--green-lt); color: var(--green); border-color: #b8d9c4; }
.tag.amber { background: var(--amber-lt); color: var(--amber); border-color: #f0d8a8; }

.card-price {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px;
  font-weight: 600;
  color: var(--green);
}
.card-price small { font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--muted); font-weight: 400; }

.card-rating { color: var(--amber); font-size: 14px; letter-spacing: .04em; }

.card-divider { height: 1px; background: var(--border); }

.card-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

.date-input {
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 8px 12px;
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  background: var(--bg);
  outline: none;
  flex: 1;
  min-width: 130px;
  transition: border-color .18s;
}
.date-input:focus { border-color: var(--green-mid); }

.btn-book {
  background: var(--green);
  color: #fff;
  border: none;
  padding: 9px 18px;
  border-radius: var(--radius-sm);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  letter-spacing: .02em;
  transition: background .18s, transform .12s;
  white-space: nowrap;
}
.btn-book:hover { background: var(--green-mid); transform: scale(1.02); }
.btn-book:active { transform: scale(.98); }

.btn-primary {
  background: var(--green);
  color: #fff;
  border: none;
  padding: 12px 28px;
  border-radius: var(--radius-sm);
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background .18s;
}
.btn-primary:hover { background: var(--green-mid); }

.booking-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px 22px;
  box-shadow: var(--shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  flex-wrap: wrap;
  animation: cardIn .3s ease both;
}
.booking-card:nth-child(odd) { border-left: 4px solid var(--green); }
.booking-card:nth-child(even) { border-left: 4px solid var(--amber); }
.booking-id { font-size: 12px; color: var(--muted); font-weight: 500; letter-spacing: .04em; }
.booking-type {
  font-weight: 600;
  font-size: 16px;
}
.booking-date { font-size: 13px; color: var(--muted); }
.booking-price {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px;
  font-weight: 600;
  color: var(--green);
}
.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .06em;
  text-transform: uppercase;
}
.badge-confirmed { background: var(--green-lt);  color: var(--green); }
.badge-pending   { background: var(--amber-lt);  color: var(--amber); }
.badge-other     { background: var(--surface2);  color: var(--muted); }

.profile-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 32px;
  max-width: 520px;
  box-shadow: var(--shadow-md);
}
.field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
.field label { font-size: 12px; font-weight: 600; color: var(--muted); letter-spacing: .06em; text-transform: uppercase; }
.field input {
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 11px 14px;
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  background: var(--bg);
  outline: none;
  transition: border-color .18s, box-shadow .18s;
}
.field input:focus {
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(45,90,61,.1);
}

.empty {
  text-align: center;
  padding: 48px 20px;
  color: var(--muted);
}
.empty-icon { font-size: 40px; margin-bottom: 12px; }
.empty p { font-size: 14px; }

.dest-promo {
  background: linear-gradient(135deg, var(--green) 0%, #3d7a54 100%);
  border-radius: var(--radius);
  padding: 32px;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}
.dest-promo h3 { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 600; }
.dest-promo p { opacity: .8; font-size: 14px; margin-top: 6px; }
.btn-outline-white {
  border: 2px solid rgba(255,255,255,.7);
  color: #fff;
  background: transparent;
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  white-space: nowrap;
  transition: background .2s;
}
.btn-outline-white:hover { background: rgba(255,255,255,.15); }

::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
</style>
</head>
<body>


<header id="topbar">
  <div id="topbar-brand">Comfy<span>Go</span></div>
  <div id="topbar-right">
    <div id="topbar-user">
      <strong><?= htmlspecialchars($user_name) ?></strong>
      <?= htmlspecialchars($user_id) ?>
    </div>
    <form method="POST" action="tourist.php">
      <input type="hidden" name="action" value="logout">
      <button type="submit" id="logout-btn">Logout</button>
    </form>
  </div>
</header>

<div id="layout">


  <aside id="sidebar">
    <div class="sidebar-label">Navigate</div>
    <a href="#transport" class="nav-link"><span class="nav-icon">🚌</span> Transport</a>
    <a href="#hotels"    class="nav-link"><span class="nav-icon">🏨</span> Hotels</a>
    <a href="#guides"    class="nav-link"><span class="nav-icon">🧭</span> Guides</a>
    <div class="nav-divider"></div>
    <a href="#bookings"  class="nav-link"><span class="nav-icon">📋</span> My Bookings</a>
    <a href="destinations.php" class="nav-link"><span class="nav-icon">🗺️</span> Destinations</a>
    <div class="nav-divider"></div>
    <a href="#profile"   class="nav-link"><span class="nav-icon">👤</span> Profile</a>
  </aside>

  <main id="main">
    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <section class="section" id="transport">
      <div class="section-header">
        <h2 class="section-title">Book Transport</h2>
        <p class="section-sub">Select your route and preferred travel type.</p>
      </div>

      <div class="filter-bar">
        <a href="?route="           class="filter-pill <?= !$transport_route_filter ? 'active' : '' ?>">All Routes</a>
        <a href="?route=Dhaka"      class="filter-pill <?= $transport_route_filter === 'Dhaka'      ? 'active' : '' ?>">Dhaka</a>
        <a href="?route=Sylhet"     class="filter-pill <?= $transport_route_filter === 'Sylhet'     ? 'active' : '' ?>">Sylhet</a>
        <a href="?route=Chittagong" class="filter-pill <?= $transport_route_filter === 'Chittagong' ? 'active' : '' ?>">Chittagong</a>
      </div>

      <?php if (empty($transports)): ?>
        <div class="empty"><div class="empty-icon">🚌</div><p>No transport options for this route.</p></div>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($transports as $t): ?>
            <div class="card">
              <div style="display:flex;align-items:center;gap:12px;">
                <div class="card-icon"><?= transport_icon($t['transport_type']) ?></div>
                <div>
                  <div class="card-name"><?= htmlspecialchars($t['transport_type']) ?></div>
                  <div style="font-size:13px;color:var(--muted);"><?= htmlspecialchars($t['transport_route']) ?></div>
                </div>
              </div>
              <div class="card-meta">
                <span class="tag green"><?= htmlspecialchars($t['transport_route']) ?></span>
              </div>
              <div class="card-price">৳<?= number_format($t['transport_fare']) ?> <small>/ person</small></div>
              <div class="card-divider"></div>
              <form method="POST" action="tourist.php" class="card-form">
                <input type="hidden" name="action" value="book_transport">
                <input type="hidden" name="transport_id" value="<?= htmlspecialchars($t['transport_ID']) ?>">
                <input type="date" name="travel_date" required class="date-input">
                <button type="submit" class="btn-book">Book</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="section" id="hotels">
      <div class="section-header">
        <h2 class="section-title">Book a Hotel</h2>
        <p class="section-sub">Filter by division to find certified stays.</p>
      </div>

      <div class="filter-bar">
        <a href="?division=#hotels"           class="filter-pill <?= !$division_filter ? 'active' : '' ?>">All</a>
        <a href="?division=Dhaka#hotels"      class="filter-pill <?= $division_filter === 'Dhaka'      ? 'active' : '' ?>">Dhaka</a>
        <a href="?division=Sylhet#hotels"     class="filter-pill <?= $division_filter === 'Sylhet'     ? 'active' : '' ?>">Sylhet</a>
        <a href="?division=Chittagong#hotels" class="filter-pill <?= $division_filter === 'Chittagong' ? 'active' : '' ?>">Chittagong</a>
      </div>

      <?php if (empty($hotels)): ?>
        <div class="empty"><div class="empty-icon">🏨</div><p>No hotels found for this division.</p></div>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($hotels as $h): ?>
            <div class="card">
              <div style="display:flex;align-items:center;gap:12px;">
                <div class="card-icon">🏨</div>
                <div>
                  <div class="card-name"><?= htmlspecialchars($h['hotel_name']) ?></div>
                  <div style="font-size:13px;color:var(--muted);"><?= htmlspecialchars($h['hotel_district']) ?></div>
                </div>
              </div>
              <div class="card-meta">
                <span class="tag green"><?= htmlspecialchars($h['hotel_division']) ?></span>
                <span class="tag"><?= htmlspecialchars($h['hotel_district']) ?></span>
              </div>
              <div class="card-rating" title="Rating: <?= htmlspecialchars($h['hotel_rating']) ?>">
                <?= star_rating((float)$h['hotel_rating']) ?>
                <span style="font-size:12px;color:var(--muted);margin-left:4px;"><?= htmlspecialchars($h['hotel_rating']) ?></span>
              </div>
              <div class="card-divider"></div>
              <form method="POST" action="tourist.php" class="card-form">
                <input type="hidden" name="action" value="book_hotel">
                <input type="hidden" name="hotel_reg" value="<?= htmlspecialchars($h['hotel_registration_number']) ?>">
                <input type="date" name="checkin" required class="date-input">
                <button type="submit" class="btn-book">Book</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="section" id="guides">
      <div class="section-header">
        <h2 class="section-title">Book a Guide</h2>
        <p class="section-sub">Certified local guides available by division.</p>
      </div>

      <div class="filter-bar">
        <a href="?guide_division=#guides"           class="filter-pill <?= !$guide_div ? 'active' : '' ?>">All</a>
        <a href="?guide_division=Dhaka#guides"      class="filter-pill <?= $guide_div === 'Dhaka'      ? 'active' : '' ?>">Dhaka</a>
        <a href="?guide_division=Sylhet#guides"     class="filter-pill <?= $guide_div === 'Sylhet'     ? 'active' : '' ?>">Sylhet</a>
        <a href="?guide_division=Chittagong#guides" class="filter-pill <?= $guide_div === 'Chittagong' ? 'active' : '' ?>">Chittagong</a>
      </div>

      <?php if (empty($guides)): ?>
        <div class="empty"><div class="empty-icon">🧭</div><p>No guides found for this division.</p></div>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($guides as $g): ?>
            <div class="card">
              <div style="display:flex;align-items:center;gap:12px;">
                <div class="card-icon" style="background:var(--amber-lt);font-size:20px;">🧭</div>
                <div>
                  <div class="card-name"><?= htmlspecialchars($g['guide_name']) ?></div>
                  <div style="font-size:13px;color:var(--muted);"><?= htmlspecialchars($g['guide_district']) ?></div>
                </div>
              </div>
              <div class="card-meta">
                <span class="tag green"><?= htmlspecialchars($g['guide_division']) ?></span>
                <span class="tag amber">Certified</span>
              </div>
              <div style="font-size:13px;color:var(--muted);">
                📞 <?= htmlspecialchars($g['guide_mobile']) ?>
              </div>
              <div class="card-divider"></div>
              <form method="POST" action="tourist.php" class="card-form">
                <input type="hidden" name="action" value="book_guide">
                <input type="hidden" name="guide_nid" value="<?= htmlspecialchars($g['guide_NID']) ?>">
                <input type="date" name="guide_date" required class="date-input">
                <button type="submit" class="btn-book">Book</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="section" id="bookings">
      <div class="section-header">
        <h2 class="section-title">My Bookings</h2>
        <p class="section-sub">Your past and upcoming travel bookings.</p>
      </div>

      <?php if (empty($bookings)): ?>
        <div class="empty"><div class="empty-icon">📋</div><p>You have no bookings yet. Start planning your trip!</p></div>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php foreach ($bookings as $b):
            $conf = strtolower($b['booking_confirmation'] ?? '');
            $badge_class = $conf === 'confirmed' ? 'badge-confirmed' : ($conf === 'pending' ? 'badge-pending' : 'badge-other');
            $type_icon = match(strtolower($b['booking_Type'])) {
              'transport' => '🚌', 'hotel' => '🏨', 'guide' => '🧭', default => '📄'
            };
          ?>
            <div class="booking-card">
              <div style="display:flex;align-items:center;gap:14px;">
                <div class="card-icon" style="flex-shrink:0;"><?= $type_icon ?></div>
                <div>
                  <div class="booking-id"><?= htmlspecialchars($b['booking_ID']) ?></div>
                  <div class="booking-type"><?= htmlspecialchars($b['booking_Type']) ?></div>
                  <div class="booking-date">📅 <?= htmlspecialchars($b['booking_date']) ?></div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($b['booking_confirmation']) ?></span>
                <div class="booking-price">
                  <?= $b['price'] ? '৳' . number_format($b['price']) : '<span style="color:var(--muted);font-size:16px;">—</span>' ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="section" id="destinations">
      <div class="dest-promo">
        <div>
          <h3>Explore Bangladesh</h3>
          <p>Curated destinations, trip cost estimator, and travel guides across the country.</p>
        </div>
        <a href="destinations.php" class="btn-outline-white">Browse Destinations →</a>
      </div>
    </section>

    <section class="section" id="profile">
      <div class="section-header">
        <h2 class="section-title">Update Profile</h2>
        <p class="section-sub">Edit your account information.</p>
      </div>
      <div class="profile-card">
        <form method="POST" action="tourist.php">
          <input type="hidden" name="action" value="update_profile">
          <div class="field">
            <label>Full Name</label>
            <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required placeholder="Your full name">
          </div>
          <div class="field">
            <label>Email Address</label>
            <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']) ?>" required placeholder="you@example.com">
          </div>
          <div class="field">
            <label>Phone Number</label>
            <input type="tel" name="user_phone" value="<?= htmlspecialchars($user['user_phone']) ?>" required placeholder="+880 ...">
          </div>
          <button type="submit" class="btn-primary">Save Changes</button>
        </form>
      </div>
    </section>

  </main>
</div>
</body>
</html>