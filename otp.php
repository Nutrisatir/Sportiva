<?php
// Halaman ini hanya boleh diakses jika user sudah login (session sudah ada)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
$page_title = 'Verifikasi - Sportiva';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Inter:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/otp_styles.css">
</head>
<body>
  <header class="site-header">
      <div class="header-content">
          <a href="index.php" class="logo-text">Sportiva</a>
      </div>
  </header>
  <main class="otp-container">
    <!-- Step 1: OTP Verification Intro -->
    <div class="otp-step active" id="step1">
      <img src="images/otp.png" alt="Verification" class="otp-image">
      <h2>OTP Verification</h2>
      <p>Enter email and phone number to send one time Password</p>
      <button class="btn-confirm" id="confirm-step1">Confirm</button>
    </div>
    <!-- Step 2: Verification Code Input -->
    <div class="otp-step" id="step2">
      <h2>Verification Code</h2>
      <p>We have sent the verification code to your email address</p>
      <div class="otp-inputs">
        <input type="text" maxlength="1" value="8" readonly>
        <input type="text" maxlength="1" value="5" readonly>
        <input type="text" maxlength="1" value="2" readonly>
        <input type="text" maxlength="1" autofocus>
      </div>
      <button class="btn-confirm" id="confirm-step2">Confirm</button>
    </div>
    <!-- Step 3: Success Message -->
    <div class="otp-step" id="step3">
      <div class="success-icon">✓</div>
      <h2>Success!</h2>
      <p>Congratulations! You have been successfully authenticated</p>
      <button class="btn-confirm" id="confirm-step3">Confirm</button>
    </div>
  </main>
</body>
<script src="js/otp.js"></script>
</html>