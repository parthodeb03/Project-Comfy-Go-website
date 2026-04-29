<?php
session_start();
require_once 'db.php';

$active_page = 'destinations';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

$user_logged_in = false;
$user_name = '';
$user_role = '';
if (isset($_SESSION['user_id']) || isset($_SESSION['guide_nid']) || isset($_SESSION['manager_id'])) {
    $user_logged_in = true;
    if (isset($_SESSION['user_id'])) {
        $user_name = $_SESSION['user_name'];
        $user_role = 'tourist';
    } elseif (isset($_SESSION['guide_nid'])) {
        $user_name = $_SESSION['guide_name'];
        $user_role = 'guide';
    } elseif (isset($_SESSION['manager_id'])) {
        $user_name = $_SESSION['manager_name'];
        $user_role = 'manager';
    }
}

$spots_query = $pdo->query("
    SELECT city, spot_name, description, best_season, entry_fee, estimated_hours
    FROM TouristSpots
    WHERE city IN ('Dhaka', 'Sylhet', 'Chittagong')
    ORDER BY city, spot_name
");
$all_spots = $spots_query->fetchAll();

$city_spots = ['Dhaka' => [], 'Sylhet' => [], 'Chittagong' => []];
foreach ($all_spots as $spot) {
    $city = $spot['city'];
    if (isset($city_spots[$city])) {
        $city_spots[$city][] = $spot;
    }
}

$hotel_prices = [];
$stmt = $pdo->query("
    SELECT hotel_division, AVG(hotel_price) as avg_price
    FROM Hotels
    WHERE hotel_division IN ('Dhaka', 'Sylhet', 'Chittagong')
    GROUP BY hotel_division
");
foreach ($stmt->fetchAll() as $row) {
    $hotel_prices[$row['hotel_division']] = (int)$row['avg_price'];
}

$guide_rates = [];
$stmt = $pdo->query("
    SELECT guide_division, AVG(guide_rate) as avg_rate
    FROM Guide
    WHERE guide_division IN ('Dhaka', 'Sylhet', 'Chittagong')
      AND guide_rate > 0
    GROUP BY guide_division
");
foreach ($stmt->fetchAll() as $row) {
    $guide_rates[$row['guide_division']] = (int)$row['avg_rate'];
}

$transport_costs = [
    'Dhaka' => 0,
    'Sylhet' => ['Train' => 900, 'Bus' => 1300, 'Airplane' => 7000],
    'Chittagong' => ['Train' => 1100, 'Bus' => 1500, 'Airplane' => 8000]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destinations — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --forest:      #1a3322;
      --canopy:      #244a31;
      --moss:        #3a6642;
      --fern:        #4e8056;
      --sage:        #7aaa80;
      --mist:        #b8d4bc;
      --dew:         #dff0e2;
      --parchment:   #f5f0e8;
      --bark:        #6b4f3a;
      --earth:       #c8b89a;
      --cream:       #faf7f2;
      --gold:        #b8982a;
      --gold-light:  #e6d38a;
      --text-dark:   #1a2e1e;
      --text-mid:    #3a5a42;
      --text-soft:   #6b8b72;

      --serif: 'Cormorant Garamond', Georgia, serif;
      --sans:  'Jost', sans-serif;

      --radius-sm: 4px;
      --radius-md: 10px;
      --radius-lg: 18px;
      --radius-xl: 28px;

      --shadow-sm: 0 2px 12px rgba(26,51,34,0.08);
      --shadow-md: 0 6px 30px rgba(26,51,34,0.12);
      --shadow-lg: 0 16px 50px rgba(26,51,34,0.18);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--sans);
      background-color: var(--cream);
      color: var(--text-dark);
      min-height: 100vh;
    }

    /* ── HEADER ─────────────────────────────────────────── */
    #destinations_header {
      background: var(--forest);
      padding: 0 40px;
      height: 72px;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 2px solid var(--moss);
    }

    #destinations_header_container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 100%;
    }

    #destinations_logo {
      font-family: var(--serif);
      font-weight: 700;
      font-style: italic;
      font-size: 34px;
      color: var(--dew);
      text-decoration: none;
      letter-spacing: 0.5px;
      transition: color 0.2s;
    }

    #destinations_logo span { color: var(--gold-light); }
    #destinations_logo:hover { color: var(--mist); }

    .leaf-divider {
      display: inline-block;
      width: 1px;
      height: 20px;
      background: rgba(184,212,188,0.3);
      vertical-align: middle;
      margin: 0 4px;
    }

    #destinations_user_menu {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    #destinations_back_link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 16px;
      background: rgba(122,170,128,0.15);
      color: var(--mist);
      text-decoration: none;
      border-radius: var(--radius-sm);
      font-family: var(--sans);
      font-size: 0.78rem;
      font-weight: 400;
      letter-spacing: 1px;
      text-transform: uppercase;
      border: 1px solid rgba(122,170,128,0.3);
      transition: all 0.25s;
    }

    #destinations_back_link:hover {
      background: rgba(122,170,128,0.28);
      border-color: var(--sage);
      color: var(--dew);
    }

    #destinations_welcome {
      font-family: var(--serif);
      font-size: 1.05rem;
      color: var(--mist);
      font-style: italic;
    }

    #destinations_role {
      font-size: 0.75rem;
      color: var(--sage);
      font-family: var(--sans);
      letter-spacing: 0.5px;
    }

    #destinations_logout_btn {
      padding: 8px 20px;
      background: transparent;
      color: var(--dew);
      border: 1px solid rgba(184,212,188,0.4);
      border-radius: var(--radius-sm);
      font-family: var(--sans);
      font-size: 0.78rem;
      font-weight: 400;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.25s;
    }

    #destinations_logout_btn:hover {
      background: var(--moss);
      border-color: var(--moss);
    }

    #destinations_auth_links { display: flex; gap: 10px; }

    #destinations_login_link {
      padding: 8px 18px;
      border: 1px solid rgba(184,212,188,0.35);
      border-radius: var(--radius-sm);
      color: var(--mist);
      text-decoration: none;
      font-family: var(--sans);
      font-size: 0.78rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: all 0.25s;
    }

    #destinations_login_link:hover {
      background: rgba(122,170,128,0.15);
      border-color: var(--sage);
    }

    #destinations_signup_link {
      padding: 8px 20px;
      background: var(--moss);
      color: var(--dew);
      border-radius: var(--radius-sm);
      text-decoration: none;
      font-family: var(--sans);
      font-size: 0.78rem;
      font-weight: 500;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: background 0.25s;
      border: 1px solid transparent;
    }

    #destinations_signup_link:hover { background: var(--fern); }

    /* ── PAGE WRAPPER ───────────────────────────────────── */
    #destinations_page {
      min-height: 100vh;
      background: var(--cream);
    }

    /* ── HERO ───────────────────────────────────────────── */
    #destinations_hero {
      position: relative;
      padding: 100px 20px 80px;
      text-align: center;
      background: var(--forest);
      overflow: hidden;
    }

    /* Botanical SVG pattern overlay */
    #destinations_hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        radial-gradient(ellipse 60% 50% at 10% 20%, rgba(74,128,86,0.25) 0%, transparent 70%),
        radial-gradient(ellipse 40% 60% at 90% 80%, rgba(36,74,49,0.4) 0%, transparent 60%);
      pointer-events: none;
    }

    .hero-ornament {
      display: block;
      width: 60px;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      margin: 0 auto 28px;
    }

    #destinations_title {
      font-family: var(--serif);
      font-size: clamp(2.8rem, 5vw, 4.2rem);
      font-weight: 600;
      color: var(--dew);
      margin-bottom: 6px;
      letter-spacing: 0.5px;
      line-height: 1.1;
    }

    #destinations_title em {
      font-style: italic;
      color: var(--gold-light);
    }

    #destinations_eyebrow {
      font-family: var(--sans);
      font-size: 0.75rem;
      font-weight: 400;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 18px;
    }

    #destinations_subtitle {
      font-family: var(--serif);
      font-size: 1.2rem;
      font-style: italic;
      color: var(--mist);
      max-width: 560px;
      margin: 20px auto 0;
      line-height: 1.8;
    }

    /* ── CITY GRID ──────────────────────────────────────── */
    #city_grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
      gap: 28px;
      max-width: 1180px;
      margin: 0 auto;
      padding: 70px 24px 90px;
    }

    .city_card {
      background: #fff;
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-md);
      transition: transform 0.35s ease, box-shadow 0.35s ease;
      cursor: pointer;
      border: 1px solid rgba(78,128,86,0.12);
      position: relative;
    }

    .city_card:hover {
      transform: translateY(-10px);
      box-shadow: var(--shadow-lg);
    }

    .city_card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--moss), var(--sage));
      opacity: 0;
      transition: opacity 0.3s;
    }

    .city_card:hover::after { opacity: 1; }

    .city_card_img {
      height: 210px;
      background-size: cover;
      background-position: center;
      position: relative;
    }

    .city_card_img::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 100px;
      background: linear-gradient(to top, rgba(26,51,34,0.85), transparent);
    }

    .city_card_name {
      position: absolute;
      bottom: 18px;
      left: 22px;
      color: #fff;
      font-family: var(--serif);
      font-size: 2rem;
      font-weight: 700;
      z-index: 1;
      text-shadow: 1px 2px 6px rgba(0,0,0,0.4);
    }

    .city_card_season_badge {
      position: absolute;
      top: 16px;
      right: 16px;
      padding: 4px 12px;
      background: rgba(255,255,255,0.18);
      border: 1px solid rgba(255,255,255,0.4);
      border-radius: 20px;
      font-family: var(--sans);
      font-size: 0.7rem;
      font-weight: 400;
      letter-spacing: 0.5px;
      color: #fff;
      backdrop-filter: blur(6px);
      z-index: 1;
    }

    .city_card_body { padding: 26px 24px 28px; }

    .city_card_stats {
      display: flex;
      justify-content: space-between;
      margin-bottom: 18px;
      padding-bottom: 18px;
      border-bottom: 1px solid rgba(78,128,86,0.12);
    }

    .city_card_stat {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3px;
    }

    .city_card_stat strong {
      font-family: var(--serif);
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--canopy);
    }

    .city_card_stat span {
      font-size: 0.72rem;
      font-weight: 400;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: var(--text-soft);
    }

    .city_card_desc {
      font-family: var(--serif);
      font-size: 1.02rem;
      font-style: italic;
      color: var(--text-mid);
      line-height: 1.75;
      margin-bottom: 22px;
    }

    .city_card_btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: var(--forest);
      color: var(--dew);
      text-decoration: none;
      border-radius: var(--radius-sm);
      font-family: var(--sans);
      font-size: 0.78rem;
      font-weight: 400;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      width: 100%;
      justify-content: center;
      transition: background 0.25s;
    }

    .city_card_btn:hover { background: var(--moss); }

    .city_card_btn::after {
      content: '→';
      font-size: 0.9rem;
      transition: transform 0.2s;
    }

    .city_card:hover .city_card_btn::after { transform: translateX(4px); }

    /* ── CITY DETAIL ────────────────────────────────────── */
    #city_detail { display: none; padding-bottom: 80px; }
    #city_detail.active { display: block; }

    #city_header {
      position: relative;
      height: 380px;
      background-size: cover;
      background-position: center;
      margin-bottom: 0;
    }

    #city_header::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(
        to bottom,
        rgba(26,51,34,0.35) 0%,
        rgba(26,51,34,0.92) 100%
      );
    }

    #city_header_content {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 0 48px 48px;
      color: #fff;
      z-index: 1;
    }

    .city_header_eyebrow {
      font-family: var(--sans);
      font-size: 0.7rem;
      font-weight: 400;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 10px;
    }

    #city_header_title {
      font-family: var(--serif);
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 10px;
      text-shadow: 0 2px 12px rgba(0,0,0,0.3);
      line-height: 1;
    }

    #city_header_subtitle {
      font-family: var(--serif);
      font-size: 1.2rem;
      font-style: italic;
      opacity: 0.9;
      margin-bottom: 16px;
      color: var(--mist);
    }

    #city_badge {
      display: inline-block;
      padding: 6px 18px;
      background: rgba(184,212,188,0.18);
      border: 1px solid rgba(184,212,188,0.4);
      border-radius: 20px;
      font-family: var(--sans);
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      backdrop-filter: blur(8px);
      color: var(--mist);
    }

    /* ── CONTENT AREA ───────────────────────────────────── */
    #city_content {
      max-width: 1180px;
      margin: 0 auto;
      padding: 48px 24px 0;
    }

    #back_to_cities {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 22px;
      background: transparent;
      color: var(--moss);
      text-decoration: none;
      border-radius: var(--radius-sm);
      font-family: var(--sans);
      font-size: 0.78rem;
      font-weight: 400;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 36px;
      border: 1px solid rgba(58,102,66,0.3);
      transition: all 0.25s;
    }

    #back_to_cities:hover {
      background: var(--forest);
      color: var(--dew);
      border-color: var(--forest);
    }

    #back_to_cities::before { content: '←'; font-size: 1rem; }

    /* ── INTRO BLOCK ────────────────────────────────────── */
    #city_intro {
      background: var(--forest);
      border-radius: var(--radius-lg);
      padding: 38px 42px;
      margin-bottom: 36px;
      position: relative;
      overflow: hidden;
    }

    #city_intro::before {
      content: '"';
      position: absolute;
      top: -10px;
      left: 30px;
      font-family: var(--serif);
      font-size: 140px;
      color: rgba(122,170,128,0.12);
      line-height: 1;
      pointer-events: none;
    }

    #city_intro p {
      font-family: var(--serif);
      font-size: 1.2rem;
      font-style: italic;
      line-height: 1.9;
      color: var(--mist);
      position: relative;
      z-index: 1;
    }

    /* ── CALCULATOR ─────────────────────────────────────── */
    #calculator_section {
      background: #fff;
      padding: 40px 42px;
      border-radius: var(--radius-lg);
      margin-bottom: 50px;
      box-shadow: var(--shadow-sm);
      border: 1px solid rgba(78,128,86,0.1);
    }

    .section_eyebrow {
      font-family: var(--sans);
      font-size: 0.7rem;
      font-weight: 400;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 8px;
    }

    #calculator_title {
      font-family: var(--serif);
      font-size: 2.1rem;
      font-weight: 600;
      color: var(--forest);
      margin-bottom: 32px;
    }

    .calc_row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .calc_field { display: flex; flex-direction: column; }

    .calc_field label {
      font-family: var(--sans);
      font-size: 0.72rem;
      font-weight: 400;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--text-soft);
      margin-bottom: 8px;
    }

    .calc_field select,
    .calc_field input {
      padding: 12px 14px;
      border: 1px solid rgba(78,128,86,0.25);
      border-radius: var(--radius-sm);
      font-family: var(--sans);
      font-size: 0.95rem;
      color: var(--text-dark);
      background: var(--parchment);
      transition: border-color 0.2s, box-shadow 0.2s;
      appearance: none;
      -webkit-appearance: none;
    }

    .calc_field select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%233a6642' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      padding-right: 36px;
    }

    .calc_field select:focus,
    .calc_field input:focus {
      outline: none;
      border-color: var(--moss);
      box-shadow: 0 0 0 3px rgba(58,102,66,0.1);
    }

    #calc_btn {
      width: 100%;
      padding: 15px;
      background: var(--forest);
      color: var(--dew);
      border: none;
      border-radius: var(--radius-sm);
      font-family: var(--sans);
      font-size: 0.82rem;
      font-weight: 400;
      letter-spacing: 2px;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.25s;
      margin-top: 6px;
    }

    #calc_btn:hover { background: var(--canopy); }

    #cost_breakdown {
      display: none;
      margin-top: 28px;
      padding: 24px 28px;
      background: var(--parchment);
      border-radius: var(--radius-md);
      border-left: 3px solid var(--moss);
    }

    #cost_breakdown.show { display: block; }

    .cost_item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 11px 0;
      border-bottom: 1px solid rgba(78,128,86,0.1);
      font-family: var(--sans);
      font-size: 0.9rem;
      color: var(--text-mid);
    }

    .cost_item:last-child { border-bottom: none; }
    .cost_amount { font-weight: 500; color: var(--canopy); }

    .cost_total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 0 4px;
      border-top: 1.5px solid var(--moss);
      margin-top: 8px;
    }

    .cost_total span:first-child {
      font-family: var(--serif);
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--forest);
    }

    .cost_total .cost_amount {
      font-family: var(--serif);
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--forest);
    }

    /* ── SPOTS SECTION ──────────────────────────────────── */
    .spots_section_header {
      margin-bottom: 28px;
    }

    #spots_title {
      font-family: var(--serif);
      font-size: 2.2rem;
      font-weight: 600;
      color: var(--forest);
      line-height: 1.2;
    }

    #spots_grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
      gap: 24px;
    }

    .spot_card {
      background: #fff;
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: 1px solid rgba(78,128,86,0.1);
    }

    .spot_card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-md);
    }

    .spot_img {
      height: 175px;
      background: var(--canopy);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .spot_img_icon {
      font-size: 3.5rem;
      position: relative;
      z-index: 1;
    }

    .spot_img::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 30% 40%, rgba(122,170,128,0.3), transparent 60%);
    }

    .spot_body { padding: 24px; }

    .spot_name {
      font-family: var(--serif);
      font-size: 1.45rem;
      font-weight: 600;
      color: var(--forest);
      margin-bottom: 10px;
    }

    .spot_meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 14px;
    }

    .spot_tag {
      padding: 4px 12px;
      background: var(--dew);
      color: var(--moss);
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 400;
      letter-spacing: 0.5px;
      font-family: var(--sans);
      border: 1px solid rgba(78,128,86,0.18);
    }

    .spot_time {
      padding: 4px 12px;
      background: var(--parchment);
      color: var(--bark);
      border-radius: 20px;
      font-size: 0.72rem;
      font-family: var(--sans);
      border: 1px solid rgba(107,79,58,0.15);
    }

    .spot_desc {
      font-family: var(--serif);
      font-size: 0.97rem;
      font-style: italic;
      color: var(--text-mid);
      line-height: 1.7;
      margin-bottom: 16px;
    }

    .spot_fee {
      font-family: var(--sans);
      font-size: 0.82rem;
      font-weight: 500;
      letter-spacing: 0.5px;
      color: var(--forest);
      padding-top: 14px;
      border-top: 1px solid rgba(78,128,86,0.1);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .spot_fee::before {
      content: '';
      display: inline-block;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--moss);
    }

    .spot_fee span { color: var(--text-soft); font-weight: 400; }

    /* ── SELECTED SPOT PREVIEW ──────────────────────────── */
    #selected_spots_list {
      margin-bottom: 20px;
    }

    .spot_preview {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 14px 18px;
      background: var(--dew);
      border-radius: var(--radius-md);
      border: 1px solid rgba(78,128,86,0.2);
    }

    .spot_preview_icon { font-size: 1.8rem; line-height: 1; }

    .spot_preview_name {
      font-family: var(--serif);
      font-size: 1.05rem;
      font-weight: 600;
      color: var(--forest);
    }

    .spot_preview_meta {
      font-size: 0.8rem;
      color: var(--text-soft);
      font-family: var(--sans);
      margin-top: 2px;
    }

    /* ── EMPTY STATE ────────────────────────────────────── */
    .empty_city {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-soft);
    }

    .empty_city h3 {
      font-family: var(--serif);
      font-size: 2rem;
      margin-bottom: 10px;
      color: var(--forest);
    }

    /* ── SECTION DIVIDER ────────────────────────────────── */
    .ornament-line {
      display: flex;
      align-items: center;
      gap: 16px;
      margin: 0 0 28px;
    }

    .ornament-line::before,
    .ornament-line::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(78,128,86,0.2);
    }

    .ornament-line span {
      font-family: var(--serif);
      font-size: 1rem;
      color: var(--sage);
    }

    /* ── RESPONSIVE ─────────────────────────────────────── */
    @media (max-width: 991px) {
      #destinations_header { padding: 0 20px; height: 65px; }
      #destinations_welcome, #destinations_role { display: none; }
      #city_header_content { padding: 0 24px 36px; }
      #city_header_title { font-size: 2.8rem; }
      #calculator_section, #city_intro { padding: 28px; }
      .calc_row { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 767px) {
      #destinations_header { padding: 0 16px; height: 60px; }
      #destinations_logo { font-size: 28px; }
      #destinations_hero { padding: 70px 16px 60px; }
      #destinations_title { font-size: 2.4rem; }
      #city_header { height: 300px; }
      #city_header_title { font-size: 2.2rem; }
      #city_header_content { padding: 0 20px 30px; }
      #calculator_section, #city_intro { padding: 22px; }
      .calc_row { grid-template-columns: 1fr; }
      #spots_grid { grid-template-columns: 1fr; }
      #city_intro::before { font-size: 100px; }
    }

    @media (max-width: 575px) {
      #destinations_header { padding: 0 12px; height: 55px; }
      #destinations_logo { font-size: 24px; }
      #destinations_back_link, #destinations_logout_btn,
      #destinations_login_link, #destinations_signup_link {
        padding: 5px 10px;
        font-size: 0.68rem;
      }
      #destinations_hero { padding: 55px 14px 50px; }
      #destinations_title { font-size: 2rem; }
      #destinations_subtitle { font-size: 1rem; }
      #city_header { height: 260px; }
      #city_header_title { font-size: 1.9rem; }
      #city_header_content { padding: 0 16px 26px; }
      .city_card_img { height: 170px; }
      #calculator_section, #city_intro { padding: 18px; }
      #calculator_title { font-size: 1.7rem; }
      .cost_total .cost_amount { font-size: 1.25rem; }
      .city_card:hover { transform: none; }
      .spot_card:hover { transform: none; }
    }

    @media (min-width: 1200px) {
      #city_grid { grid-template-columns: repeat(3, 1fr); }
      #spots_grid { grid-template-columns: repeat(3, 1fr); }
      .calc_row { grid-template-columns: repeat(4, 1fr); }
    }
  </style>
</head>
<body>

<!-- Header -->
<header id="destinations_header">
  <div id="destinations_header_container">
    <a href="index.php" id="destinations_logo">Comfy<span>Go</span></a>

    <?php if ($user_logged_in): ?>
      <div id="destinations_user_menu">
        <a href="<?= $user_role === 'tourist' ? 'tourist.php' : ($user_role === 'guide' ? 'guide.php' : 'manager.php') ?>" id="destinations_back_link">
          <i class="fa-solid fa-arrow-left" style="font-size:0.7rem;"></i> Dashboard
        </a>
        <span id="destinations_welcome">Welcome, <?= htmlspecialchars($user_name) ?></span>
        <span id="destinations_role">(<?= ucfirst(htmlspecialchars($user_role)) ?>)</span>
        <form method="POST" action="destinations.php" style="display:inline;">
          <input type="hidden" name="action" value="logout">
          <button type="submit" id="destinations_logout_btn">Logout</button>
        </form>
      </div>
    <?php else: ?>
      <div id="destinations_auth_links">
        <a href="login.php" id="destinations_login_link">Login</a>
        <a href="signup.php" id="destinations_signup_link">Sign Up</a>
      </div>
    <?php endif; ?>
  </div>
</header>

<div id="destinations_page">

  <!-- ═══ CITY SELECTION ═══ -->
  <div id="city_selection" class="active">

    <div id="destinations_hero">
      <p id="destinations_eyebrow">Discover Bangladesh</p>
      <span class="hero-ornament"></span>
      <h1 id="destinations_title">Journey Into <em>Nature</em></h1>
      <p id="destinations_subtitle">Three extraordinary destinations where ancient heritage meets breathtaking landscapes — awaiting your discovery.</p>
    </div>

    <div id="city_grid">

      <!-- Dhaka Card -->
      <div class="city_card" onclick="showCityDetail('Dhaka')">
        <div class="city_card_img" style="background-image: url('images/aboutDhaka_image.jpg');">
          <span class="city_card_season_badge">Oct – Mar</span>
          <span class="city_card_name">Dhaka</span>
        </div>
        <div class="city_card_body">
          <div class="city_card_stats">
            <div class="city_card_stat"><strong>8+</strong><span>Attractions</span></div>
            <div class="city_card_stat"><strong>3–5</strong><span>Days</span></div>
            <div class="city_card_stat"><strong>Oct–Mar</strong><span>Best Season</span></div>
          </div>
          <p class="city_card_desc">The bustling capital blending Mughal grandeur with modern energy — where centuries of history live side by side with a vibrant cityscape.</p>
          <span class="city_card_btn">Explore Dhaka</span>
        </div>
      </div>

      <!-- Sylhet Card -->
      <div class="city_card" onclick="showCityDetail('Sylhet')">
        <div class="city_card_img" style="background-image: url('images/aboutSylhet_image.jpg');">
          <span class="city_card_season_badge">Sep – Mar</span>
          <span class="city_card_name">Sylhet</span>
        </div>
        <div class="city_card_body">
          <div class="city_card_stats">
            <div class="city_card_stat"><strong>8+</strong><span>Attractions</span></div>
            <div class="city_card_stat"><strong>4–6</strong><span>Days</span></div>
            <div class="city_card_stat"><strong>Sep–Mar</strong><span>Best Season</span></div>
          </div>
          <p class="city_card_desc">Emerald tea gardens, mystic hills, and sacred shrines — Sylhet is nature at its most serene, where the land itself breathes tranquillity.</p>
          <span class="city_card_btn">Explore Sylhet</span>
        </div>
      </div>

      <!-- Chittagong Card -->
      <div class="city_card" onclick="showCityDetail('Chittagong')">
        <div class="city_card_img" style="background-image: url('images/aboutCtg_image.jpg');">
          <span class="city_card_season_badge">Nov – Feb</span>
          <span class="city_card_name">Chittagong</span>
        </div>
        <div class="city_card_body">
          <div class="city_card_stats">
            <div class="city_card_stat"><strong>8+</strong><span>Attractions</span></div>
            <div class="city_card_stat"><strong>3–5</strong><span>Days</span></div>
            <div class="city_card_stat"><strong>Nov–Feb</strong><span>Best Season</span></div>
          </div>
          <p class="city_card_desc">Where rolling hills cascade into the Bay of Bengal — a coastal treasure of pristine beaches, lush hill tracts, and a storied maritime soul.</p>
          <span class="city_card_btn">Explore Chittagong</span>
        </div>
      </div>

    </div>
  </div>

  <!-- ═══ CITY DETAIL ═══ -->
  <div id="city_detail">

    <div id="city_header">
      <div id="city_header_content">
        <p class="city_header_eyebrow">ComfyGo Destinations</p>
        <h1 id="city_header_title">Dhaka</h1>
        <p id="city_header_subtitle">The heart of Bangladesh</p>
        <span id="city_badge">Best time: October – March</span>
      </div>
    </div>

    <div id="city_content">

      <a href="#" onclick="showCitySelection(); return false;" id="back_to_cities">Back to Destinations</a>

      <!-- Intro -->
      <div id="city_intro">
        <p id="city_welcome_text"></p>
      </div>

      <!-- Calculator -->
      <div id="calculator_section">
        <p class="section_eyebrow">Plan Your Visit</p>
        <h2 id="calculator_title">Trip Cost Estimator</h2>

        <form id="cost_form" onsubmit="calculateCost(event)">
          <div class="calc_row">
            <div class="calc_field" id="transport_field">
              <label>Transport Mode</label>
              <select id="transport_select" onchange="updateTransportCost()">
                <option value="" disabled selected>Select below</option>
              </select>
            </div>
            <div class="calc_field">
              <label>Nights at Hotel</label>
              <input type="number" id="nights_input" min="1" max="14" value="2" onchange="calculateCost()">
            </div>
            <div class="calc_field">
              <label>Guide Days</label>
              <input type="number" id="guide_days_input" min="1" max="14" value="2" onchange="calculateCost()">
            </div>
            <div class="calc_field">
              <label>Spot to Visit</label>
              <select id="spots_count_input" onchange="updateSelectedSpots()"></select>
            </div>
          </div>

          <div id="selected_spots_list"></div>

          <button type="submit" id="calc_btn">Calculate Total Cost</button>

          <div id="cost_breakdown">
            <div class="cost_item">
              <span>Transport (round trip)</span>
              <span class="cost_amount" id="transport_cost_display">৳0</span>
            </div>
            <div class="cost_item">
              <span>Hotel — ৳<span id="hotel_rate">0</span> × <span id="hotel_nights_display">0</span> nights</span>
              <span class="cost_amount" id="hotel_cost_display">৳0</span>
            </div>
            <div class="cost_item">
              <span>Guide — ৳<span id="guide_rate">0</span> × <span id="guide_days_display">0</span> days</span>
              <span class="cost_amount" id="guide_cost_display">৳0</span>
            </div>
            <div class="cost_item">
              <span>Entry Fees (<span id="spots_count_display">0</span> spots)</span>
              <span class="cost_amount" id="entry_cost_display">৳0</span>
            </div>
            <div class="cost_total">
              <span>Total Estimated Cost</span>
              <span class="cost_amount" id="total_cost_display">৳0</span>
            </div>
          </div>
        </form>
      </div>

      <!-- Spots -->
      <div class="spots_section_header">
        <p class="section_eyebrow">Curated Attractions</p>
        <h2 id="spots_title">Must-Visit Places</h2>
      </div>
      <div class="ornament-line"><span>✦</span></div>
      <div id="spots_grid"></div>

    </div>
  </div>

</div>

<script>
const cityData = {
  Dhaka: {
    spots: <?= json_encode($city_spots['Dhaka']) ?>,
    hotelPrice: <?= $hotel_prices['Dhaka'] ?? 15000 ?>,
    guideRate: <?= $guide_rates['Dhaka'] ?? 2500 ?>,
    transportModes: { 'Train': 0, 'Bus': 0, 'Airplane': 0 },
    welcome: "Bangladesh's vibrant capital is a fascinating blend of ancient history and modern energy. From Mughal architecture to megacity buzz, Dhaka offers endless discovery at every corner."
  },
  Sylhet: {
    spots: <?= json_encode($city_spots['Sylhet']) ?>,
    hotelPrice: <?= $hotel_prices['Sylhet'] ?? 8000 ?>,
    guideRate: <?= $guide_rates['Sylhet'] ?? 2000 ?>,
    transportModes: {
      'Train': <?= $transport_costs['Sylhet']['Train'] ?: 900 ?>,
      'Bus': <?= $transport_costs['Sylhet']['Bus'] ?: 1300 ?>,
      'Airplane': <?= $transport_costs['Sylhet']['Airplane'] ?: 7000 ?>
    },
    welcome: "Nestled among emerald tea gardens and mystical hills, Sylhet is a haven for nature lovers and spiritual seekers. The serene landscapes and sacred shrines create an unforgettable experience."
  },
  Chittagong: {
    spots: <?= json_encode($city_spots['Chittagong']) ?>,
    hotelPrice: <?= $hotel_prices['Chittagong'] ?? 12000 ?>,
    guideRate: <?= $guide_rates['Chittagong'] ?? 2500 ?>,
    transportModes: {
      'Train': <?= $transport_costs['Chittagong']['Train'] ?: 1100 ?>,
      'Bus': <?= $transport_costs['Chittagong']['Bus'] ?: 1500 ?>,
      'Airplane': <?= $transport_costs['Chittagong']['Airplane'] ?: 8000 ?>
    },
    welcome: "Where the mountains meet the sea, Chittagong offers pristine beaches, lush hills, and a historic port legacy. Explore the untamed beauty of the Bay of Bengal's coastal gem."
  }
};

const spotIcons = ['🏛️','🏯','🏞️','🏖️','⛰️','🕌','🌳','🏝️','🗿','🌿'];

function showCityDetail(city) {
  const data = cityData[city];
  if (!data) return;

  const transportSelect = document.getElementById('transport_select');
  const transportField = document.getElementById('transport_field');
  const transportLabel = transportField.querySelector('label');

  transportSelect.innerHTML = '';
  const existingNote = document.getElementById('dhaka_note');
  if (existingNote) existingNote.remove();

  if (city === 'Dhaka') {
    transportLabel.textContent = 'Transport';
    transportSelect.disabled = true;
    const opt = document.createElement('option');
    opt.value = 'None';
    opt.textContent = 'Already in Dhaka';
    transportSelect.appendChild(opt);
    const note = document.createElement('p');
    note.id = 'dhaka_note';
    note.style.cssText = 'font-size:0.78rem;color:var(--text-soft);margin-top:6px;font-family:var(--sans);';
    note.textContent = 'Add local transport (~৳200–500/day) if needed.';
    transportField.appendChild(note);
  } else {
    transportSelect.disabled = false;
    transportLabel.textContent = 'Transport (from Dhaka)';
    Object.entries(data.transportModes).forEach(([mode, cost]) => {
      const opt = document.createElement('option');
      opt.value = mode;
      opt.textContent = `${mode} — ৳${cost.toLocaleString()} round trip`;
      transportSelect.appendChild(opt);
    });
  }

  document.getElementById('city_header_title').textContent = city;
  document.getElementById('city_header_subtitle').textContent = getCitySubtitle(city);
  document.getElementById('city_badge').textContent = getBestSeasonText(city);

  const imgName = city === 'Chittagong' ? 'Ctg' : city;
  document.getElementById('city_header').style.backgroundImage = `url('images/about${imgName}_image.jpg')`;

  document.getElementById('city_welcome_text').textContent = data.welcome;

  // Spots dropdown
  const spotsSelect = document.getElementById('spots_count_input');
  spotsSelect.innerHTML = '';
  data.spots.forEach((spot, i) => {
    const opt = document.createElement('option');
    opt.value = i;
    opt.textContent = `${spot.spot_name} — ৳${spot.entry_fee}`;
    spotsSelect.appendChild(opt);
  });

  // Render spot cards
  renderSpotCards(city);
  updateSelectedSpots();

  document.getElementById('nights_input').value = 2;
  document.getElementById('guide_days_input').value = 2;
  if (city !== 'Dhaka') transportSelect.value = 'Train';
  calculateCost();

  document.getElementById('city_selection').classList.remove('active');
  document.getElementById('city_detail').classList.add('active');
  window.scrollTo(0, 0);
}

function renderSpotCards(city) {
  const data = cityData[city];
  const grid = document.getElementById('spots_grid');
  grid.innerHTML = '';

  if (!data.spots.length) {
    grid.innerHTML = '<div class="empty_city"><h3>Coming Soon</h3><p>Attractions for this city will be listed shortly.</p></div>';
    return;
  }

  data.spots.forEach((spot, i) => {
    const icon = spotIcons[i % spotIcons.length];
    const card = document.createElement('div');
    card.className = 'spot_card';
    card.innerHTML = `
      <div class="spot_img">
        <span class="spot_img_icon">${icon}</span>
      </div>
      <div class="spot_body">
        <h3 class="spot_name">${spot.spot_name}</h3>
        <div class="spot_meta">
          <span class="spot_tag">${spot.best_season}</span>
          ${spot.estimated_hours ? `<span class="spot_time">${spot.estimated_hours} hrs</span>` : ''}
        </div>
        <p class="spot_desc">${spot.description || 'A wonderful destination worth visiting.'}</p>
        <p class="spot_fee">Entry Fee <span>৳${Number(spot.entry_fee).toLocaleString()}</span></p>
      </div>
    `;
    grid.appendChild(card);
  });
}

function showCitySelection() {
  document.getElementById('city_detail').classList.remove('active');
  document.getElementById('city_selection').classList.add('active');
  window.scrollTo(0, 0);
}

function getCitySubtitle(city) {
  return {
    'Dhaka': 'The bustling capital of Bangladesh',
    'Sylhet': 'Land of tea gardens and mystic hills',
    'Chittagong': "Bay of Bengal's coastal paradise"
  }[city] || 'Discover the beauty';
}

function getBestSeasonText(city) {
  return {
    'Dhaka': 'Best time: October – March',
    'Sylhet': 'Best time: September – March',
    'Chittagong': 'Best time: November – February'
  }[city] || 'Year-round destination';
}

function updateSelectedSpots() {
  const spotsSelect = document.getElementById('spots_count_input');
  const idx = parseInt(spotsSelect.value) || 0;
  const data = getCurrentCityData();
  const spotsList = document.getElementById('selected_spots_list');

  if (!data || !data.spots[idx]) {
    spotsList.innerHTML = '';
    return;
  }

  const spot = data.spots[idx];
  const icon = spotIcons[idx % spotIcons.length];
  spotsList.innerHTML = `
    <div class="spot_preview">
      <span class="spot_preview_icon">${icon}</span>
      <div>
        <div class="spot_preview_name">${spot.spot_name}</div>
        <div class="spot_preview_meta">Entry: ৳${Number(spot.entry_fee).toLocaleString()} · Best season: ${spot.best_season}</div>
      </div>
    </div>
  `;
  calculateCost();
}

function getCurrentCityData() {
  if (document.getElementById('city_selection').classList.contains('active')) return null;
  const title = document.getElementById('city_header_title').textContent.trim();
  return cityData[title] || null;
}

function calculateCost(e) {
  if (e) e.preventDefault();
  const data = getCurrentCityData();
  if (!data) return;

  const transportMode = document.getElementById('transport_select').value;
  const nights = parseInt(document.getElementById('nights_input').value) || 1;
  const guideDays = parseInt(document.getElementById('guide_days_input').value) || 1;
  const spotsIdx = parseInt(document.getElementById('spots_count_input').value) || 0;

  const transportCost = transportMode === 'None' ? 0 : (data.transportModes[transportMode] || 0);
  const hotelCost = data.hotelPrice * nights;
  const guideCost = data.guideRate * guideDays;

  let totalEntry = 0;
  for (let i = 0; i <= spotsIdx; i++) {
    if (data.spots[i]) totalEntry += Number(data.spots[i].entry_fee);
  }

  const total = transportCost + hotelCost + guideCost + totalEntry;

  document.getElementById('transport_cost_display').textContent = transportMode === 'None' ? '৳0' : `৳${transportCost.toLocaleString()}`;
  document.getElementById('hotel_rate').textContent = data.hotelPrice.toLocaleString();
  document.getElementById('hotel_nights_display').textContent = nights;
  document.getElementById('hotel_cost_display').textContent = `৳${hotelCost.toLocaleString()}`;
  document.getElementById('guide_rate').textContent = data.guideRate.toLocaleString();
  document.getElementById('guide_days_display').textContent = guideDays;
  document.getElementById('guide_cost_display').textContent = `৳${guideCost.toLocaleString()}`;
  document.getElementById('entry_cost_display').textContent = `৳${totalEntry.toLocaleString()}`;
  document.getElementById('spots_count_display').textContent = spotsIdx + 1;
  document.getElementById('total_cost_display').textContent = `৳${total.toLocaleString()}`;
  document.getElementById('cost_breakdown').classList.add('show');
}

function updateTransportCost() { calculateCost(); }

document.addEventListener('DOMContentLoaded', updateSelectedSpots);
</script>

</body>
</html>