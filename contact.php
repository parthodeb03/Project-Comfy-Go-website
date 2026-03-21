<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact-ComfyGo</title>
    <link rel="stylesheet" href="styles/contact.css">
</head>
<body>
    <?php
    $active_page = 'Contact';
    $active_page = $active_page ?? 'Contact';
    include 'navbaar.php';
    ?>
    <div id = "contact">
        <div id = contact_sec_1>
        <p id="p1">GET IN TOUCH</p>
        <p id="p2">Questions or feedback?</p> 
        <p id="p3">We'd love to hear from you!</p>
        </div>
        <div id="contact_sub_sec_1">
         <div id="contact_info">
            <p id="p4">Contact Information</p>
            <div id="contact_info_des">
                <p id="p5"><i class="fa-solid fa-map-pin"></i>Address: Sylhet, Bangladesh</p>
                <p id="p6"><i class="fa-solid fa-phone"></i>Phone: +8801234567890</p>
                <p id="p7"><i class="fa-solid fa-envelope"></i>Email: info@comfygo.com</p>
            </div>
         </div>
         <div id="SendMessage">
            <p id="p8">Send a message</p>
            <form action="contact.php" method="post">
                <input type="text" name="name" placeholder="Your Name" id="name">
                <input type="email" name="email" placeholder="Your Email" id="email">
                <input type="text" name="phone" placeholder="Your Phone" id="phone">
                <textarea name="message" placeholder="Your Message" id="message"></textarea>
                <button type="submit" id="submit">Send Message</button>
            </form>
         </div>   
        </div>
    </div>
</body>
</html>