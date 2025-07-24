<?php
// Pengaturan halaman
$page_title = 'Sportiva - Your Sports Hub';
$active_page = 'home';
$css_files = ['home_styles.css'];
$include_auth_modal = true; // Kita butuh modal login di sini

// Memuat header
require_once 'includes/header.php';
?>

<main class="hero-section">
    <div class="slider-wrapper"></div>
    <button class="slider-arrow prev-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <div class="hero-center-content">
        <div class="info-box"><h1>Football</h1><p>Details On Training Sessions, Tournaments, And Court Booking.</p></div>
        <div class="slider-pagination"><span class="dot active"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
    </div>
    <button class="slider-arrow next-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>
</main>

<?php 
// Memuat footer
require_once 'includes/footer.php'; 
?>