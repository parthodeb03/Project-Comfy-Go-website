<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — ComfyGo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/about.css">
</head>
<body>

<?php
$active_page = $active_page ?? 'about';
include 'navbaar.php';
?>

<div id="about_sec_1">
    <p id="about_header">Travel made with comfort!</p>
    <p id="about_text">ComfyGo is a travel agency that specializes in providing comfortable and enjoyable travel experiences. We offer a wide range of travel services, including flights, hotels, and tours, to help you explore the world in style.</p>
</div>

<div id="about_sec_2">
    <p id="about_header_2">Our Mission</p>
    <div id="about_subsec_1">
        <img src="images/about_image1.jpg" alt="Our Mission" id="about_img_1">
    </div>
    <p id="about_text_2">Our mission is to provide our customers with the best possible travel experience. We are committed to providing comfortable and enjoyable travel experiences, and we strive to exceed our customers' expectations in every way.</p>
</div>

<div id="about_sec_3">
    <p id="certified_places">PLACES WE CERTIFIED</p>
    <div class="places-grid">
        <div id="about_sylhet">
            <img src="images/aboutSylhet_image.jpg" alt="Sylhet" id="sylhet_img">
            <p id="sylhet">Sylhet</p>
            <p id="sylhet_des">Sylhet is a city in northeastern Bangladesh, known for its lush green surroundings and rich cultural heritage. Jaflong, Ratargul Swamp Forest, Shah Jalal Mazar, Lakkatura Tea Garden are the most beautiful places to visit in Sylhet.</p>
        </div>
        <div id="about_dhaka">
            <img src="images/aboutDhaka_image.jpg" alt="Dhaka" id="dhaka_img">
            <p id="dhaka">Dhaka</p>
            <p id="dhaka_des">Dhaka is the capital city of Bangladesh, known for its vibrant culture, bustling streets, and rich history. Popular spots include the National Museum, Ahsan Manzil, National Parliament Building and National Martyrs' Memorial.</p>
        </div>
        <div id="about_ctg">
            <img src="images/aboutCtg_image.jpg" alt="Chittagong" id="ctg_img">
            <p id="ctg">Chittagong</p>
            <p id="ctg_des">Chittagong is a major port city in southeastern Bangladesh, known for its beautiful beaches and rich cultural heritage. Popular spots include Patenga Sea Beach, Foy's Lake, Chittagong Hill Tracks and Chittagong War Cemetery.</p>
        </div>
    </div>
</div>

<div id="Hotels">
    <p id="hotels">Certified Hotels</p>
    <p id="hotel_des">There are some hotels in our country selected by tourist spots and public demands which are certified by us. These hotels are very comfortable and safe for tourists.</p>
    <img src="images/aboutHotels.jpg" alt="Certified Hotels" id="aboutHotel">
</div>

<div id="guides_sec">
    <p id="guides_title">Certified Guides</p>
    <p id="guides_des">There are some guides in our country selected by tourist spots and public demands which are certified by us. These guides are very comfortable and safe for tourists.</p>
    <img src="images/about_guides.jpg" alt="Certified Guides" id="aboutGuides">
</div>
</body>
</html>