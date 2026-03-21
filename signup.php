<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — ComfyGo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles/signup.css">
</head>

<body>

    <?php $active_page = 'signup';
    include 'navbaar.php'; ?>

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

                    <label class="role_card" id="role_tourist_card">
                        <input type="radio" name="role" value="tourist" id="role_tourist" checked>
                        <span class="role_icon">🧳</span>
                        <span class="role_title">Tourist</span>
                        <span class="role_desc">I want to explore Bangladesh</span>
                    </label>

                    <label class="role_card" id="role_guide_card">
                        <input type="radio" name="role" value="guide" id="role_guide">
                        <span class="role_icon">🗺️</span>
                        <span class="role_title">Guide</span>
                        <span class="role_desc">I lead tours and experiences</span>
                    </label>

                    <label class="role_card" id="role_manager_card">
                        <input type="radio" name="role" value="manager" id="role_manager">
                        <span class="role_icon">🏨</span>
                        <span class="role_title">Hotel Manager</span>
                        <span class="role_desc">I manage a certified hotel</span>
                    </label>

                </div>
            </div>

            <?php
            $role = $_POST['role'] ?? 'tourist';
            $error = '';
            $success = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $role = $_POST['role'] ?? 'tourist';

                if ($role === 'tourist') {
                    $user_ID = trim($_POST['user_ID'] ?? '');
                    $user_email = trim($_POST['user_email'] ?? '');
                    $user_name = trim($_POST['user_name'] ?? '');
                    $user_phone = trim($_POST['user_phone'] ?? '');
                    $password = $_POST['password'] ?? '';
    
            
                } elseif ($role === 'guide') {
                    $guide_NID = trim($_POST['guide_NID'] ?? '');
                    $guide_name = trim($_POST['guide_name'] ?? '');
                    $guide_email = trim($_POST['guide_email'] ?? '');
                    $guide_mobile = trim($_POST['guide_mobile'] ?? '');
                    $guide_division = trim($_POST['guide_division'] ?? '');
                    $guide_district = trim($_POST['guide_district'] ?? '');

                } elseif ($role === 'manager') {
                    $manager_ID = trim($_POST['manager_ID'] ?? '');
                    $manager_name = trim($_POST['manager_name'] ?? '');
                    $manager_email = trim($_POST['manager_email'] ?? '');
                    $manager_mobile = trim($_POST['manager_mobile'] ?? '');
                    $hotel_registration_number = trim($_POST['hotel_registration_number'] ?? '');

                }
            }
            ?>

            <div id="register_card">

                <?php if ($error): ?>
                    <div id="form_error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div id="form_success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="register.php" id="register_form">

                    <input type="hidden" name="role" id="role_input" value="tourist">

                    <div id="form_tourist" class="role_form">

                        <div class="reg_field">
                            <label for="user_ID">User ID</label>
                            <input type="text" id="user_ID" name="user_ID" placeholder="Choose a unique user ID"
                                required>
                        </div>

                        <div class="reg_field">
                            <label for="user_name">Full Name</label>
                            <input type="text" id="user_name" name="user_name" placeholder="Your full name" required
                                autocomplete="name">
                        </div>

                        <div class="reg_field">
                            <label for="user_email">Email Address</label>
                            <input type="email" id="user_email" name="user_email" placeholder="you@example.com" required
                                autocomplete="email">
                        </div>

                        <div class="reg_field">
                            <label for="user_phone">Phone Number</label>
                            <input type="tel" id="user_phone" name="user_phone" placeholder="+880 1XXX XXXXXX" required
                                autocomplete="tel">
                        </div>

                        <div class="reg_field">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Create a password"
                                required autocomplete="new-password">
                        </div>

                    </div>

                    <div id="form_guide" class="role_form" style="display:none;">

                        <div class="reg_field">
                            <label for="guide_NID">National ID (NID)</label>
                            <input type="text" id="guide_NID" name="guide_NID" placeholder="Your NID number">
                        </div>

                        <div class="reg_field">
                            <label for="guide_name">Full Name</label>
                            <input type="text" id="guide_name" name="guide_name" placeholder="Your full name"
                                autocomplete="name">
                        </div>

                        <div class="reg_field">
                            <label for="guide_email">Email Address</label>
                            <input type="email" id="guide_email" name="guide_email" placeholder="you@example.com"
                                autocomplete="email">
                        </div>

                        <div class="reg_field">
                            <label for="guide_mobile">Mobile Number</label>
                            <input type="tel" id="guide_mobile" name="guide_mobile" placeholder="+880 1XXX XXXXXX"
                                autocomplete="tel">
                        </div>

                        <div class="reg_field">
                            <label for="guide_division">Division</label>
                            <input type="text" id="guide_division" name="guide_division" placeholder="e.g. Sylhet">
                        </div>

                        <div class="reg_field">
                            <label for="guide_district">District</label>
                            <input type="text" id="guide_district" name="guide_district" placeholder="e.g. Moulvibazar">
                        </div>

                    </div>

                    <div id="form_manager" class="role_form" style="display:none;">

                        <div class="reg_field">
                            <label for="manager_ID">Manager ID</label>
                            <input type="text" id="manager_ID" name="manager_ID"
                                placeholder="Choose a unique manager ID">
                        </div>

                        <div class="reg_field">
                            <label for="manager_name">Full Name</label>
                            <input type="text" id="manager_name" name="manager_name" placeholder="Your full name"
                                autocomplete="name">
                        </div>

                        <div class="reg_field">
                            <label for="manager_email">Email Address</label>
                            <input type="email" id="manager_email" name="manager_email" placeholder="you@example.com"
                                autocomplete="email">
                        </div>

                        <div class="reg_field">
                            <label for="manager_mobile">Mobile Number</label>
                            <input type="tel" id="manager_mobile" name="manager_mobile" placeholder="+880 1XXX XXXXXX"
                                autocomplete="tel">
                        </div>

                        <div class="reg_field">
                            <label for="hotel_registration_number">Hotel Registration Number</label>
                            <input type="text" id="hotel_registration_number" name="hotel_registration_number"
                                placeholder="Official hotel reg. number">
                        </div>

                    </div>

                    <div id="terms_row">
                        <label id="terms_label">
                            <input type="checkbox" id="terms_check" name="terms" required>
                            <span id="terms_box"></span>
                            <span id="terms_text">I agree to the <a href="#" id="terms_link">Terms of Service</a></span>
                        </label>
                    </div>

                    <button type="submit" id="register_submit">Create Account</button>

                </form>

                <p id="signin_text">Already have an account? <a href="login.php" id="signin_link">Sign in</a></p>

            </div>
        </div>
    </div>

    <script>
        const radios = document.querySelectorAll('input[name="role"]');
        const forms = document.querySelectorAll('.role_form');
        const roleInput = document.getElementById('role_input');
        const cards = document.querySelectorAll('.role_card');

        function switchRole(val) {
            forms.forEach(f => f.style.display = 'none');
            document.getElementById('form_' + val).style.display = 'flex';
            roleInput.value = val;
            cards.forEach(c => c.classList.remove('active'));
            document.getElementById('role_' + val + '_card').classList.add('active');
        }

        radios.forEach(r => r.addEventListener('change', () => switchRole(r.value)));
        cards.forEach(c => {
            c.addEventListener('click', () => {
                const val = c.querySelector('input[type="radio"]').value;
                switchRole(val);
            });
        });
        switchRole('tourist');
    </script>

</body>

</html>