<?php
session_start();
require_once 'db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($message) < 10) {
        $error = 'Message must be at least 10 characters.';
    } else {
        $message_id = 'MSG' . strtoupper(uniqid());
        $stmt = $pdo->prepare("INSERT INTO ContactMessages (message_ID, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$message_id, $name, $email, $phone, $message]);
            $success = 'Thank you! Your message has been sent successfully.';
        } catch (PDOException $e) {
            $error = 'Failed to send message. Please try again later.';
        }
    }
}

$active_page = 'contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact-ComfyGo</title>
    <link rel="stylesheet" href="styles/contact.css">
</head>
<body>
    <?php include 'navbaar.php'; ?>
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
            <?php if ($error): ?>
                <div style="color: #e74c3c; padding: 10px; margin-bottom: 15px; border-radius: 4px; background: #fdf2f2;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="color: #27ae60; padding: 10px; margin-bottom: 15px; border-radius: 4px; background: #f2fdf4;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form action="contact.php" method="post">
                <input type="text" name="name" placeholder="Your Name" id="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                <input type="email" name="email" placeholder="Your Email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <input type="tel" name="phone" placeholder="Your Phone" id="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                <textarea name="message" placeholder="Your Message" id="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                <button type="submit" id="submit">Send Message</button>
            </form>
         </div>   
        </div>
    </div>
</body>
</html>
