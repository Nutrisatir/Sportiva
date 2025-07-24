<?php
$page_title = 'Detail Mabar: Grand Launching Bmx';
$active_page = 'mabar';
$css_files = ['sewa_lapangan_styles.css', 'mabar_styles.css']; // Pastikan mabar_styles.css ada di sini
$include_auth_modal = true; 
require_once 'includes/header.php';
?>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    /* === CSS RESET & GLOBAL === */
    body, .main-content {
        margin: 0;
        padding: 0;
        background-color: #fcfcfc;
        font-family: 'Rubik', sans-serif;
    }
    * {
        box-sizing: border-box;
    }

    /* === STRUKTUR UTAMA === */
    .main-content-wrapper {
        background-image: url('images/_pngtree_lines_seamless_pattern_7301526_1_1752110965388_865.png');
    }
    .container {
        max-width: 1180px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 24px;
        padding-right: 24px;
    }

    /* === HERO SECTION (BANNER BIRU) === */
    .hero-section-wrapper {
        background-color: #105A93;
        color: white;
        padding: 24px 0 40px 0;
        width: 100%;
    }
    .hero-section-wrapper .breadcrumb {
        font-size: 14px; margin-bottom: 24px; opacity: 0.9;
    }
    .hero-section-wrapper .breadcrumb a { color: white; text-decoration: none; }
    .hero-section-wrapper .tag {
        display: inline-block; background-color: white; color: #105A93;
        padding: 5px 14px; border-radius: 10px;
        font-size: 13px; font-weight: 500; margin-bottom: 12px;
    }
    .hero-section-wrapper h1 {
        font-size: 32px; font-weight: 500; margin: 0 0 16px 0;
    }
    .participants-info { display: flex; align-items: center; gap: 12px; }
    .participant-avatars { display: flex; align-items: center; }
    .participant-avatars img, .avatar-empty-placeholder {
        width: 32px; height: 32px;
        border-radius: 50%; border: 2px solid white;
        margin-left: -12px;
    }
    .participant-avatars img:first-child { margin-left: 0; }
    .avatar-empty-placeholder { border-style: dashed; background-color: rgba(255,255,255,0.1); }
    .join-status a {
        background-color: white; color: #105A93; padding: 6px 14px;
        border-radius: 10px; font-size: 13px;
        font-weight: 500; text-decoration: none;
    }

    /* === DETAIL CONTENT SECTION === */
    .details-section-wrapper { padding: 48px 0; }
    .details-grid { display: flex; gap: 32px; align-items: flex-start; }

    .info-column {
        flex: 2; background-color: white; padding: 32px;
        border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .info-column h2 {
        font-size: 24px; font-weight: 500; color: #25282B;
        margin-top: 0; margin-bottom: 24px;
    }
    .info-column .section-divider { margin-top: 40px; }
    .info-column .description p {
        font-size: 15px; line-height: 1.8; color: #52575C;
        margin: 0 0 22px 0;
    }
    .info-column .location-address {
        font-size: 15px; color: #25282B; margin-bottom: 16px;
    }
    .info-column .map-image { width: 100%; height: auto; border-radius: 8px; }

    .booking-column {
        flex: 1; background-color: white; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 24px; position: sticky; top: 20px;
    }
    .price-display {
        display: flex;
        align-items: baseline;
        justify-content: flex-start;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #E8E8E8;
    }
    .price-display .amount { font-size: 28px; font-weight: 500; color: #25282B; }
    .price-display .per-person { font-size: 15px; color: #A0A4A8; margin-left: 8px; }
    
    /* === PERUBAHAN UNTUK ANIMASI TOMBOL === */
    .join-button {
        display: block; width: 100%; padding: 14px;
        background-color: #0C3C6E; color: white; text-align: center;
        border-radius: 12px; font-size: 16px;
        text-decoration: none; border: none; cursor: pointer; margin-bottom: 24px;
        font-weight: 700;
        /* Menambahkan transisi untuk animasi yang halus */
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .join-button:hover {
        /* Warna latar menjadi sedikit lebih terang */
        background-color: #105A93;
        /* Tombol terangkat sedikit */
        transform: translateY(-3px);
        /* Menambahkan bayangan untuk efek 'lift' */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    /* === AKHIR PERUBAHAN === */
    
    .info-list-item { margin-bottom: 18px; }
    .info-list-item:last-child { margin-bottom: 0; }
    .info-list-item .label {
        font-size: 16px; font-weight: 500; color: #25282B; margin: 0 0 6px 0;
    }
    .info-list-item .value { font-size: 14px; color: #52575C; margin: 0; }
    
    @media (max-width: 992px) {
        .details-grid { flex-direction: column; }
        .booking-column { position: static; width: 100%; margin-top: 32px; }
    }
</style>

<div class="main-content-wrapper">
    <!-- BANNER BIRU -->
    <section class="hero-section-wrapper">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a> > <a href="mabar.php">Mabar</a> > Mabar "Grand Launching Bmx"
            </div>
            <div class="tag">Badminton - Beginner - Pro</div>
            <h1>Mabar "Grand Launching Bmx"</h1>
            <div class="participants-info">
                <div class="participant-avatars">
                    <img src="images/user_avatar_1752110965392_704.png" alt="Avatar">
                    <img src="images/user_avatar_1752110965392_458.png" alt="Avatar">
                    <img src="images/user_avatar_1752110965393_690.png" alt="Avatar">
                    <img src="images/user_avatar_1752110965393_611.png" alt="Avatar">
                    <img src="images/user_avatar_1752110965393_885.png" alt="Avatar">
                    <img src="images/avatar_empty_1752110965393_337.png" alt="Avatar">
                    <div class="avatar-empty-placeholder"></div>
                </div>
                <span class="join-status"><a href="#">8/11 Bergabung ></a></span>
            </div>
        </div>
    </section>

    <!-- KONTEN UTAMA -->
    <section class="details-section-wrapper">
        <div class="container">
            <div class="details-grid">
                <!-- KOLOM KIRI -->
                <div class="info-column">
                    <h2>Tentang Mabar</h2>
                    <div class="description">
                        <p>Yuk kakak2 mari badmin bareng 🏸🏸</p>
                        <p>⛳️ Sudah booked di GOR Badminton Cipondoh.<br>Lap 2 : pk 18.00-21.00 (3 jam)<br>Lap 3 : pk 18.00-21.00 (3 jam)<br>Lap 4 : pk 18.00-20.00 (2 jam)</p>
                        <p>🧑‍⚖️👩‍⚖️cowok dan cewek yg mau join silahkan ya (level Begginer Plus - Intermediate). Sudah daftar harus komitmen datang ya. Max cancel hari H jam 3 sore lewat itu tetap dikenakan fee mabar ya. 🙏</p>
                        <p>Sudah ada member tetap yang main +/- 10 orang, mencari tambahan pemain dan teman baru 🤗</p>
                        <p>💰Rp 45.000/pax (sudah termasuk shuttlecock)<br>Untuk yang pertama kali join mohon tsf di awal ke rek BCA 0xx.0xxxxx0 an. Rendra Aditya</p>
                        <p>Thank you all 🙏🥳<br>NB : Mohon pastikan dalam kondisi fit ya ketika join mabar</p>
                    </div>
                    <h2 class="section-divider">Lokasi</h2>
                    <p class="location-address">Jl. Kaliurang KM. 5, Caturtunggal, Sleman</p>
                    <img class="map-image" src="images/pin_google_lokasi_1_1752110965390_255.png" alt="Peta Lokasi">
                </div>
                <!-- KOLOM KANAN -->
                <div class="booking-column">
                    <div class="price-display">
                        <span class="amount">Rp45.000</span>
                        <span class="per-person">/Peserta</span>
                    </div>
                    <button class="join-button">Gabung Mabar</button>
                    <div class="info-list">
                        <div class="info-list-item">
                            <p class="label">Waktu & Tanggal</p>
                            <p class="value">Kam, 03 Jul 2025 • 18:00 - 21:00</p>
                        </div>
                        <div class="info-list-item">
                            <p class="label">Lokasi</p>
                            <p class="value">Jl. Kaliurang KM. 5, Caturtunggal, Sleman</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>