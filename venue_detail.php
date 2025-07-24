<?php
require_once 'config/database.php';
session_start(); // Diperlukan untuk logika login
$css_files = ['sewa_lapangan_styles.css', 'venue_detail_styles.css'];
$venue_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$venue_id) {
    header('Location: sewa_lapangan.php');
    exit();
}

// Ambil data utama venue
$stmt = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt->execute([$venue_id]);
$venue = $stmt->fetch();
if (!$venue) {
    header('Location: sewa_lapangan.php');
    exit();
}

// Ambil data jadwal
$stmt_schedules = $pdo->prepare("SELECT * FROM schedules WHERE venue_id = ? ORDER BY start_time ASC");
$stmt_schedules->execute([$venue_id]);
$schedules = $stmt_schedules->fetchAll();

// Ambil data olahraga terkait
$stmt_sports = $pdo->prepare("
    SELECT s.name, s.icon_svg 
    FROM venue_sports vs
    JOIN sports s ON vs.sport_id = s.id
    WHERE vs.venue_id = ?
");
$stmt_sports->execute([$venue_id]);
$sports = $stmt_sports->fetchAll();

// Data Statis (seperti di video, karena tidak ada di DB Anda)
$facilities = [
    'Cafe & Resto' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>',
    'Jual Makanan Ringan' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 21z" /></svg>',
    'Jual Minuman' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
    'Musholla' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>',
    'Parkir Mobil' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 013.375-3.375h9.75a3.375 3.375 0 013.375 3.375v1.875m-17.25 4.5h17.25" /></svg>',
    'Parkir Motor' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.502l1.54-2.665a.375.375 0 00-.086-.512L7.5 9.75M15.91 11.672a.375.375 0 010 .656" /></svg>'
];
$venue_rules = [
    'Pelanggan harus datang tepat waktu.',
    'Dilarang membawa air mineral gelas.',
    'Dilarang bersandar dijaring.',
    'Dilarang membawa senjata tajam dan minuman keras.',
    'Dilarang memakai sepatu berdempet.'
];

$page_title = htmlspecialchars($venue['name']) . ' - Sportiva';
$active_page = 'sewa';
$include_auth_modal = true;
require_once 'includes/header.php';
?>

<style>
:root {
  --text-white: #ffffff; --text-dark-blue: #0D4072; --background-light: #F8F9FA;
  --border-color: #DEE2E6; --text-dark: #212529; --text-muted: #6C757D;
  --font-primary: 'Poppins', sans-serif; --star-color: #ffc107; --blue-light-bg: #e7f6fd;
}
.main-content-wrapper { max-width: 1200px; margin: 24px auto; padding: 0 20px; }
.breadcrumb { font-size: 14px; margin-bottom: 24px; color: var(--text-muted); }
.breadcrumb a { color: var(--text-dark-blue); text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.venue-gallery { position: relative; margin-bottom: 32px; }
.venue-gallery img { width: 100%; height: 450px; object-fit: cover; border-radius: 16px; }
.gallery-btn { position: absolute; bottom: 20px; right: 20px; background: rgba(255,255,255,0.9); color: var(--text-dark); border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; }
.venue-content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 48px; }
.venue-sidebar { position: -webkit-sticky; position: sticky; top: 120px; align-self: flex-start; }
.venue-details-main h1 { font-size: 32px; font-weight: 600; color: var(--text-dark); margin: 0 0 8px 0; }
.venue-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; color: var(--text-muted); }
.venue-meta .rating { display: flex; align-items: center; gap: 4px; }
.venue-meta .icon-star { width: 18px; height: 18px; color: var(--star-color); }
.venue-sports-tags { display: flex; gap: 8px; margin-bottom: 24px; }
.sport-tag { background: var(--blue-light-bg); color: var(--text-dark-blue); padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
.sport-tag svg { width: 16px; height: 16px; }
.venue-section { padding-top: 24px; border-top: 1px solid var(--border-color); margin-top: 24px; }
.venue-section h2 { font-size: 20px; font-weight: 600; margin: 0 0 16px 0; }
.venue-section p, .venue-section ol { line-height: 1.7; color: #333; }
.venue-section ol { padding-left: 20px; margin: 0; }
.read-more-link { color: var(--text-dark-blue); font-weight: 600; cursor: pointer; display: inline-block; margin-top: 8px; }
.location-box { display: flex; justify-content: space-between; align-items: center; background: var(--background-light); padding: 20px; border-radius: 12px; margin-top: 16px; }
.map-link { background: var(--text-white); border: 1px solid var(--border-color); color: var(--text-dark-blue); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; }
.facility-list { list-style: none; padding: 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.facility-list li { display: flex; align-items: center; gap: 12px; }
.facility-list svg { width: 20px; height: 20px; color: var(--text-dark-blue); }
.btn-link-style { background: none; border: none; color: var(--text-dark-blue); font-weight: 600; cursor: pointer; padding: 0; font-size: 14px; }
.pricing-card, .benefits-card { background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 24px; }
.price-label { font-size: 14px; color: var(--text-muted); }
.price-amount { font-size: 28px; font-weight: 700; color: var(--text-dark-blue); margin: 4px 0 16px 0; }
.price-session { font-weight: 400; color: var(--text-muted); }
.btn-primary { width: 100%; background: var(--text-dark-blue); color: white; border: none; padding: 14px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
.btn-primary:hover { background-color: #081F49; }
.benefits-card h3 { font-size: 16px; margin: 0 0 16px 0; font-weight: 600; }
.benefits-card ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.benefits-card li { display: flex; align-items: center; gap: 8px; font-size: 14px; }
.benefits-card svg { width: 20px; height: 20px; color: var(--text-dark-blue); flex-shrink: 0; }
#membership-section { padding-top: 40px; }
.membership-slider-container { position: relative; }
.membership-slider { display: flex; gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory; -ms-overflow-style: none; scrollbar-width: none; padding: 16px 0; }
.membership-slider::-webkit-scrollbar { display: none; }
.membership-card { scroll-snap-align: start; flex: 0 0 300px; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; background: white; }
.membership-card h4 { font-size: 16px; margin: 0 0 8px 0; }
.membership-card ul { list-style: none; padding: 0; margin: 0 0 16px 0; font-size: 13px; color: var(--text-muted); line-height: 1.6; }
.card-footer { display: flex; justify-content: space-between; align-items: center; }
.discount-tag { font-size: 12px; font-weight: 600; color: #e53e3e; background: #fee2e2; padding: 4px 8px; border-radius: 6px; }
.btn-buy { background: var(--text-dark-blue); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 500; cursor: pointer; }
.slider-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: white; border: 1px solid var(--border-color); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.slider-arrow.prev { left: -20px; }
.slider-arrow.next { right: -20px; }
.modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 1050; opacity: 0; visibility: hidden; transition: opacity 0.3s ease; }
.modal.is-visible { opacity: 1; visibility: visible; }
.modal-content { background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; position: relative; }
.modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; }
#schedule-section { padding-top: 60px; border-top: 1px solid var(--border-color); margin-top: 40px; }
</style>

<main class="main-content-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Home</a> > <a href="sewa_lapangan.php">Sewa Lapangan</a> > <?php echo htmlspecialchars($venue['name']); ?>
    </div> 

    <div class="venue-gallery">
        <img src="images/<?php echo htmlspecialchars($venue['main_image']); ?>" alt="Venue Image">
        <button class="gallery-btn" id="open-gallery-modal">Lihat semua foto</button>
    </div>

    <div class="venue-content-grid">
        <div class="venue-details-main">
            <h1><?php echo htmlspecialchars($venue['name']); ?></h1>
            <div class="venue-meta">
                <span class="rating">
                    <svg class="icon-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.868 2.884c.321-.662 1.215-.662 1.536 0l1.681 3.468 3.82.556c.734.107 1.028.997.494 1.503l-2.764 2.694.652 3.803c.124.722-.638 1.278-1.284.944L10 13.6l-3.419 1.796c-.646.334-1.408-.222-1.284-.944l.652-3.803L2.27 8.411c-.534-.506-.24-1.396.494-1.503l3.82-.556 1.681-3.468z" clip-rule="evenodd" /></svg>
                    <?php echo htmlspecialchars($venue['rating']); ?>
                </span>
                <span>·</span>
                <span><?php echo htmlspecialchars($venue['address']); ?></span>
            </div>
            <div class="venue-sports-tags">
                <?php foreach ($sports as $sport): ?>
                    <div class="sport-tag"><?php echo $sport['icon_svg']; ?> <?php echo htmlspecialchars($sport['name']); ?></div>
                <?php endforeach; ?>
            </div>

            <section class="venue-section">
                <h2>Deskripsi</h2>
                <p><?php echo nl2br(htmlspecialchars($venue['description'])); ?></p>
            </section>
            
            <section class="venue-section">
                <h2>Aturan Venue</h2>
                <ol>
                    <?php foreach (array_slice($venue_rules, 0, 5) as $rule): ?>
                        <li><?php echo htmlspecialchars($rule); ?></li>
                    <?php endforeach; ?>
                </ol>
                <a class="read-more-link" id="open-rules-modal">Baca Selengkapnya</a>
            </section>
            
            <section class="venue-section">
                <h2>Lokasi Venue</h2>
                <div class="location-box">
                    <span><?php echo htmlspecialchars($venue['address']); ?></span>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($venue['address']); ?>" target="_blank" class="map-link">Buka Peta</a>
                </div>
            </section>

            <section class="venue-section">
                <h2>Fasilitas</h2>
                <ul class="facility-list">
                    <?php $i = 0; foreach ($facilities as $name => $icon): if ($i++ < 6): ?>
                        <li><?php echo $icon; ?> <span><?php echo htmlspecialchars($name); ?></span></li>
                    <?php endif; endforeach; ?>
                </ul>
                <button class="btn-link-style" id="open-facilities-modal">Lihat semua fasilitas</button>
            </section>

        </div>

        <div class="venue-sidebar">
            <div class="pricing-card">
                <div class="price-label">Mulai dari</div>
                <div class="price-amount">Rp<?php echo number_format($venue['min_price'], 0, ',', '.'); ?><span class="price-session"> /sesi</span></div>
                <a href="ketersediaan_venue.php?id=<?php echo $venue['id']; ?>" button class="btn-primary" id="check-availability-btn" class="btn-primary">Cek Ketersediaan</a>
            </div>
            <div class="benefits-card">
                <h3>Booking lewat aplikasi lebih banyak keuntungan</h3>
                <ul>
                    <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Opsi pembayaran down payment (DP)*</li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Reschedule jadwal booking*</li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Lebih banyak promo & voucher</li>
                </ul>
            </div>
        </div>
    </div>
    
    <section class="venue-section" id="membership-section">
        <h2>Paket Membership</h2>
        <div class="membership-slider-container">
            <button class="slider-arrow prev" id="membership-prev"><</button>
            <div class="membership-slider" id="membership-slider">
                <div class="membership-card">
                    <h4>Membership Futsal 2 Jam / Minggu</h4>
                    <ul>
                        <li>Langganan 2 slot per minggu</li>
                        <li>Perpanjang setiap 1 bulan</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Diskon 10%</span>
                        <button class="btn-buy">Beli Paket</button>
                    </div>
                </div>
                <div class="membership-card">
                    <h4>Membership Futsal 1 Jam / Minggu</h4>
                     <ul>
                        <li>Langganan 1 slot per minggu</li>
                        <li>Perpanjang setiap 1 bulan</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Diskon 10%</span>
                        <button class="btn-buy">Beli Paket</button>
                    </div>
                </div>
                <div class="membership-card">
                    <h4>Membership Badminton 3 Jam / Minggu</h4>
                     <ul>
                        <li>Langganan 3 slot per minggu</li>
                        <li>Perpanjang setiap 1 bulan</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Diskon 15%</span>
                        <button class="btn-buy">Beli Paket</button>
                    </div>
                </div>
                <div class="membership-card">
                    <h4>Paket Pelajar</h4>
                     <ul>
                        <li>Berlaku Senin-Jumat jam 08:00 - 16:00</li>
                        <li>Wajib menunjukkan kartu pelajar</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Spesial</span>
                        <button class="btn-buy">Beli Paket</button>
                    </div>
                </div>
            </div>
            <button class="slider-arrow next" id="membership-next">></button>
        </div>
    </section>

</main>

<!-- Modals -->
<div class="modal" id="gallery-modal">
    <div class="modal-content"><button class="modal-close" id="close-gallery-modal">×</button><h2>Galeri Foto</h2><p>Fitur galeri lengkap akan segera hadir.</p></div>
</div>

<div class="modal" id="rules-modal">
    <div class="modal-content">
        <button class="modal-close" id="close-rules-modal">×</button>
        <h2>Aturan Venue & Kebijakan</h2>
        <p><strong>Aturan Venue</strong></p>
        <ol>
            <?php foreach ($venue_rules as $rule): ?>
                <li><?php echo htmlspecialchars($rule); ?></li>
            <?php endforeach; ?>
        </ol>
        <br>
        <p><strong>Kebijakan refund & reschedule</strong></p>
        <p>Reschedule hingga 3 hari sebelum jadwal sewa. Hanya berlaku untuk 1 kali reschedule.</p>
        <p style="color: #c53030;">Reservasi tidak dapat dibatalkan dan tidak berlaku refund.</p>
    </div>
</div>

<div class="modal" id="facilities-modal">
    <div class="modal-content">
        <button class="modal-close" id="close-facilities-modal">×</button>
        <h2>Semua Fasilitas</h2>
        <ul class="facility-list" style="grid-template-columns: repeat(2, 1fr); margin-top: 20px;">
             <?php foreach ($facilities as $name => $icon): ?>
                <li><?php echo $icon; ?> <span><?php echo htmlspecialchars($name); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- SCROLL TO AVAILABILITY ---
    const checkBtn = document.getElementById('check-availability-btn');
    const scheduleSection = document.getElementById('schedule-section');
    if (checkBtn && scheduleSection) {
        checkBtn.addEventListener('click', () => {
            scheduleSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // --- MODAL GENERIC LOGIC ---
    function setupModal(openBtnId, closeBtnId, modalId) {
        const openBtn = document.getElementById(openBtnId);
        const closeBtn = document.getElementById(closeBtnId);
        const modal = document.getElementById(modalId);
        if (!openBtn || !closeBtn || !modal) return;
        
        openBtn.addEventListener('click', () => modal.classList.add('is-visible'));
        closeBtn.addEventListener('click', () => modal.classList.remove('is-visible'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('is-visible');
        });
    }
    setupModal('open-gallery-modal', 'close-gallery-modal', 'gallery-modal');
    setupModal('open-rules-modal', 'close-rules-modal', 'rules-modal');
    setupModal('open-facilities-modal', 'close-facilities-modal', 'facilities-modal');

    // --- MEMBERSHIP SLIDER ---
    const slider = document.getElementById('membership-slider');
    const prevBtn = document.getElementById('membership-prev');
    const nextBtn = document.getElementById('membership-next');
    if (slider && prevBtn && nextBtn) {
        const scrollAmount = 316; // width of card + gap
        prevBtn.addEventListener('click', () => slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => slider.scrollBy({ left: scrollAmount, behavior: 'smooth' }));
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>