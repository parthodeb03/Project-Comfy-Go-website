<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — ComfyGo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/about.css">
</head>
<body>

<?php
$active_page = $active_page ?? 'about';
include 'navbaar.php';
?>

<div id="about_sec_1">
    <p id="about_header">Travel made with comfort!</p>
    <p id="about_text">ComfyGo is a travel agency dedicated to crafting comfortable, memorable, and stress-free journeys across Bangladesh. From the misty tea gardens of Sylhet to the bustling streets of Dhaka and the sun-kissed shores of Chittagong, we bring every destination to life with carefully curated services, trusted local guides, and handpicked accommodations — so you can focus on the experience, not the logistics.</p>
</div>

<div id="about_sec_2">
    <p id="about_header_2">Our Mission</p>
    <div id="about_subsec_1">
        <img src="images/about_image1.jpg" alt="Our Mission" id="about_img_1">
    </div>
    <p id="about_text_2">At ComfyGo, our mission is simple — to make every journey feel effortless and extraordinary. We believe travel should be a source of joy, not stress. That's why we handle every detail, from the moment you book to the moment you return home.<br><br>We work with verified local partners, certified guides, and carefully selected hotels to ensure your safety and comfort at every step. Whether you're a solo explorer, a family on holiday, or a group of adventurers, we tailor each experience to match your pace and preferences.<br><br>We are committed to responsible, sustainable tourism that uplifts local communities and preserves the natural beauty of Bangladesh for generations to come.</p>
</div>

<div id="about_sec_3">
    <p id="certified_places">Places We Certified</p>
    <div class="places-grid">
        <div id="about_sylhet">
            <img src="images/aboutSylhet_image.jpg" alt="Sylhet" id="sylhet_img">
            <p id="sylhet">Sylhet</p>
            <p id="sylhet_des">Nestled in the northeast, Sylhet enchants visitors with rolling tea estates, crystal-clear rivers, and ancient shrines. Highlights include the surreal beauty of Jaflong's stone beaches, the haunting stillness of Ratargul Swamp Forest, the spiritual calm of Shah Jalal Mazar, and the emerald rows of Lakkatura Tea Garden — a destination that feels worlds apart from the ordinary.</p>
        </div>
        <div id="about_dhaka">
            <img src="images/aboutDhaka_image.jpg" alt="Dhaka" id="dhaka_img">
            <p id="dhaka">Dhaka</p>
            <p id="dhaka_des">Bangladesh's vibrant capital pulses with history, culture, and relentless energy. From the ornate pink palace of Ahsan Manzil along the Buriganga River to the iconic silhouette of the National Parliament Building, Dhaka rewards every curious traveller. Explore the National Museum's rich collections, pay homage at the National Martyrs' Memorial, and lose yourself in the colourful chaos of Old Dhaka's lanes.</p>
        </div>
        <div id="about_ctg">
            <img src="images/aboutCtg_image.jpg" alt="Chittagong" id="ctg_img">
            <p id="ctg">Chittagong</p>
            <p id="ctg_des">Bangladesh's port city blends sea breeze, hills, and heritage into one unforgettable destination. Stroll the windswept sands of Patenga Sea Beach at sunset, wander the peaceful Foy's Lake amid forested hills, or journey into the dramatic Chittagong Hill Tracts. The Chittagong War Cemetery stands as a moving tribute to history, offering quiet reflection amid immaculate grounds.</p>
        </div>
    </div>
</div>

<div id="Hotels">
    <p id="hotels">Certified Hotels</p>
    <p id="hotel_des">ComfyGo has personally vetted and certified a selection of hotels across Bangladesh's top destinations, chosen for their comfort, cleanliness, safety standards, and proximity to key attractions. Each property is evaluated based on traveller feedback, facilities, and our own on-ground inspections.<br><br>From boutique guesthouses surrounded by Sylhet's tea gardens to well-appointed city hotels in Dhaka and seaside retreats in Chittagong, our certified accommodations are matched to every budget and travel style — so you always rest easy, wherever you go.</p>
    <img src="images/aboutHotels.jpg" alt="Certified Hotels" id="aboutHotel">
</div>

<div id="guides_sec">
    <p id="guides_title">Certified Guides</p>
    <p id="guides_des">A great destination becomes truly memorable with the right guide by your side. ComfyGo's certified guides are handpicked locals who bring each destination to life with deep cultural knowledge, storytelling, and genuine hospitality. Every guide undergoes a thorough vetting process covering language proficiency, safety training, and destination expertise.<br><br>Whether you need a knowledgeable companion for a historical walking tour through Old Dhaka, a nature expert for Ratargul's swamp trails, or a seasoned hill guide for the Chittagong Hill Tracts, our certified guides ensure your journey is safe, enriching, and full of authentic moments you won't find in any guidebook.</p>
    <img src="images/about_guides.jpg" alt="Certified Guides" id="aboutGuides">
</div>
</body>
</html>