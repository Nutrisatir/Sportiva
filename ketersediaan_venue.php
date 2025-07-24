<?php
require_once 'config/database.php';
session_start();

// --- LOGIKA PHP (TIDAK ADA PERUBAHAN) ---
$css_files = ['sewa_lapangan_styles.css', 'venue_detail_styles.css'];
$venue_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$venue_id) {
    header('Location: sewa_lapangan.php');
    exit();
}
$stmt_venue = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt_venue->execute([$venue_id]);
$venue = $stmt_venue->fetch(PDO::FETCH_ASSOC);
if (!$venue) {
    header('Location: sewa_lapangan.php');
    exit();
}
$stmt_sports = $pdo->prepare("SELECT s.id, s.name, s.icon_svg FROM venue_sports vs JOIN sports s ON vs.sport_id = s.id WHERE vs.venue_id = ?");
$stmt_sports->execute([$venue_id]);
$sports_at_venue = $stmt_sports->fetchAll(PDO::FETCH_ASSOC);
$courts = [];
foreach ($sports_at_venue as $sport) {
    $stmt_schedules = $pdo->prepare("SELECT * FROM schedules WHERE venue_id = ? AND sport_id = ? ORDER BY start_time ASC");
    $stmt_schedules->execute([$venue_id, $sport['id']]);
    $schedules = $stmt_schedules->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($schedules)) {
        $courts[$sport['id']] = [
            'name' => "Lapangan " . $sport['name'], 'sport_name' => $sport['name'], 'description' => 'Lapangan menggunakan Vinyl',
            'tags' => [
                ['icon' => $sport['icon_svg'], 'text' => $sport['name']],
                ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.243 3.243a1 1 0 01.963-.22l6 2.25a1 1 0 01.548.905v10.148a1 1 0 01-1.548.905l-6-2.25a1 1 0 01-.963-.22L3.243 8.31a1 1 0 010-1.62l6-4.447z" clip-rule="evenodd" /></svg>', 'text' => 'Indoor'],
                ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v.518l4.447 5.93a.75.75 0 01-.944 1.22l-1.42-1.065a.75.75 0 00-.91-.03L10 11.482l-1.873-1.124a.75.75 0 00-.91-.03L6 11.435a.75.75 0 01-.944-1.22L9.5 4.268V3.75A.75.75 0 0110 3zM10 6.25a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5a.75.75 0 01.75-.75z" clip-rule="evenodd" /></svg>', 'text' => 'Vinyl']
            ], 'schedules' => $schedules
        ];
    }
}
$page_title = 'Ketersediaan - ' . htmlspecialchars($venue['name']);
$active_page = 'sewa';
$include_auth_modal = true;
require_once 'includes/header.php';
?>

<style>
/* --- FONT & ROOT VARIABLES (TIDAK ADA PERUBAHAN) --- */
@import url('https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap');
:root {
  --text-dark: #25282B; --text-muted: #52575C; --text-muted-light: #A0A4A8;
  --border-color: #E8E8E8; --brand-primary: #0C3C6E; --brand-blue-selected: #14A4EE;
  --color-booked: #f8f9fa; --color-booked-text: #A0A4A8; --color-success: #22c55e;
  --font-content: 'Rubik', sans-serif;
}
/* --- STYLING KONTEN UTAMA --- */
.figma-content-wrapper { font-family: var(--font-content); flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-start; align-items: center; padding: 40px 20px; width: 100%; background-color: #ffffff; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 900'%3e%3cdefs%3e%3clinearGradient id='a' x1='0' x2='0' y1='1' y2='0'%3e%3cstop offset='0' stop-color='%23F5F5F5'/%3e%3cstop offset='1' stop-color='%23F9F9F9'/%3e%3c/linearGradient%3e%3clinearGradient id='b' x1='0' x2='0' y1='0' y2='1'%3e%3cstop offset='0' stop-color='%23FFFFFF'/%3e%3cstop offset='1' stop-color='%23FCFCFC'/%3e%3c/linearGradient%3e%3c/defs%3e%3cg fill='%23FAFAFA' fill-opacity='0.6'%3e%3crect fill='url(%23a)' width='1600' height='900'/%3e%3cpath d='M1600 0H0v900h1600V0zM1248 135c-22.1 0-43.2 2.4-63.5 7L931 292.5c-19.6-11.8-42.5-18.2-66.5-18.2-24.3 0-47.5 6.6-67.5 18.7L327 135c-20.9-4.5-42.5-7-64-7-55.2 0-106.5 13.5-151 39.5L0 256.5v-270L151.5 0c44.5-26 95.8-39.5 151.5-39.5 21.5 0 43.1 2.5 64 7l470 157.5c20-12.1 43.2-18.7 67.5-18.7 24 0 46.9 6.4 66.5 18.2l253.5-150.5c20.3-4.6 41.4-7 63.5-7 55.2 0 106.5 13.5 151 39.5L1600 86.5V225l-102.5-81.5c-44.5-26-95.8-39.5-151-39.5zm-146.5 315.5c-19.6-11.8-42.5-18.2-66.5-18.2-24.3 0-47.5 6.6-67.5 18.7L500 608c-20.9-4.5-42.5-7-64-7-55.2 0-106.5 13.5-151 39.5L182.5 722v-270L285 434c44.5-26 95.8-39.5 151.5-39.5 21.5 0 43.1 2.5 64 7l470 157.5c20-12.1 43.2-18.7 67.5-18.7 24 0 46.9 6.4 66.5 18.2l253.5-150.5c20.3-4.6 41.4-7 63.5-7 55.2 0 106.5 13.5 151 39.5L1600 521V660l-102.5-81.5c-44.5-26-95.8-39.5-151-39.5-22.1 0-43.2 2.4-63.5 7L931 738c-19.6-11.8-42.5-18.2-66.5-18.2-24.3 0-47.5 6.6-67.5 18.7L327 600c-20.9-4.5-42.5-7-64-7-55.2 0-106.5 13.5-151 39.5L0 691.5V421.5l102.5 81.5c44.5 26 95.8 39.5 151.5-39.5 21.5 0 43.1 2.5 64-7l470-157.5c20 12.1 43.2 18.7 67.5 18.7 24 0 46.9-6.4 66.5-18.2l253.5 150.5c20.3 4.6 41.4 7 63.5 7 55.2 0 106.5-13.5 151-39.5L1600 450.5V312l-102.5 81.5c-44.5 26-95.8 39.5-151 39.5z'/%3e%3c/g%3e%3c/svg%3e"); background-attachment: fixed; background-size: cover; }
.page-title { width: 100%; max-width: 1440px; display: flex; align-items: center; gap: 16px; font-size: 24px; font-weight: 500; margin-bottom: 30px; color: var(--text-dark); }
.page-title a { color: var(--text-dark); display: flex; align-items: center; }
.main-content-flex { display: flex; justify-content: center; align-items: flex-start; gap: 0; width: 100%; max-width: 1440px; }
.left-column { width: 440px; flex-shrink: 0; }
.right-column { flex-grow: 1; border-left: 1px solid var(--border-color); padding-left: 32px; }
.court-image-slider img { width: 100%; height: 250px; object-fit: cover; border-radius: 12px; cursor: pointer; }
.court-details h3 { font-size: 18px; font-weight: 500; line-height: 28px; letter-spacing: 0.15px; color: var(--text-dark); margin: 0; }
.court-details .description { font-size: 14px; line-height: 20px; letter-spacing: 0.25px; color: var(--text-muted); margin: 0 0 16px 0; }
.feature-item { display: flex; align-items: center; gap: 8px; color: var(--text-dark); font-size: 14px; line-height: 20px; letter-spacing: 0.25px; margin-bottom: 8px; }
.feature-item svg { width: 16px; height: 16px; flex-shrink: 0; fill: currentColor; }
.schedule-dropdown { background: var(--brand-primary); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; margin-top: 24px; display: flex; justify-content: center; align-items: center; gap: 10px; width: fit-content; font-weight: 700; /* MODIFIKASI: Diubah dari 500 ke 700 agar bold */ font-size: 14px; line-height: 20px; letter-spacing: 0.25px; }
.schedule-dropdown .chevron { transition: transform 0.3s ease-in-out; }
.schedule-dropdown.open .chevron { transform: rotate(180deg); }
.time-slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; max-height: 0; opacity: 0; overflow: hidden; transition: max-height 0.5s ease-in-out, opacity 0.5s ease-in-out, margin-top 0.5s ease-in-out; }
.time-slots-grid.open { max-height: 2000px; opacity: 1; margin-top: 24px; }
.time-slot { border-radius: 8px; padding: 13px 9px; text-align: center; cursor: pointer; transition: all 0.2s ease; background-color: white; outline: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 2.5px; }
.time-slot:hover { outline-color: var(--brand-blue-selected); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
.time-slot .duration { font-size: 10px; font-weight: 500; line-height: 12px; letter-spacing: 0.1px; color: var(--text-muted-light); }
.time-slot .time { font-size: 14px; font-weight: 500; line-height: 20px; letter-spacing: 0.25px; margin: 0; color: var(--text-dark); }
.time-slot .price { font-size: 14px; font-weight: 400; line-height: 20px; letter-spacing: 0.25px; color: var(--text-muted); }
.time-slot.booked { background: var(--color-booked); cursor: not-allowed; outline-color: #f1f1f1; }
.time-slot.booked .time, .time-slot.booked .price { color: var(--color-booked-text); }
.time-slot.selected { background: var(--brand-primary); color: white; outline-color: var(--brand-primary); }
.time-slot.selected .duration, .time-slot.selected .time, .time-slot.selected .price { color: white; }
.image-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); z-index: 1500; display: flex; justify-content: center; align-items: center; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s; }
.image-modal-overlay.visible { opacity: 1; visibility: visible; }
.image-modal-content { position: relative; padding: 0; transform: scale(0.9); transition: transform 0.3s; background: none; max-width: 98vw; max-height: 98vh; }
.image-modal-overlay.visible .image-modal-content { transform: scale(1); }
.image-modal-content img { display: block; width: auto; height: auto; border-radius: 8px; max-width: 98vw; max-height: 98vh; }
.image-modal-close { position: absolute; top: -15px; right: -15px; width: 36px; height: 36px; background: white; color: var(--brand-primary); border-radius: 50%; border: 2px solid var(--brand-primary); display: flex; justify-content: center; align-items: center; font-size: 24px; font-weight: bold; cursor: pointer; line-height: 1; }

/* === CSS KERANJANG BARU --- */
.cart-sidebar {
    font-family: var(--font-content);
    position: fixed;
    top: 0;
    right: 0;
    width: 450px;
    height: 100%;
    background-color: white;
    box-shadow: -4px 0 15px rgba(0,0,0,0.1);
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    z-index: 2000;
    display: flex;
    flex-direction: column;
}
.cart-sidebar.is-visible {
    transform: translateX(0);
}
.cart-header {
    display: flex;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
    position: relative; /* MODIFIKASI: Ditambahkan untuk positioning tombol close */
    justify-content: center; /* MODIFIKASI: Diubah dari space-between agar judul ke tengah */
}
.cart-header h3 {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 16px;
    margin: 0;
    color: var(--brand-primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.cart-close-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    position: absolute; /* MODIFIKASI: Diposisikan absolut */
    right: 24px; /* MODIFIKASI: Diberi jarak dari kanan */
    top: 50%; /* MODIFIKASI: Ditambahkan untuk center vertikal */
    transform: translateY(-50%); /* MODIFIKASI: Ditambahkan untuk center vertikal */
}
.cart-close-btn:hover { color: var(--text-dark); }
.cart-close-btn svg { width: 24px; height: 24px; }
.cart-body {
    flex-grow: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.cart-item {
    padding: 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.cart-item-title {
    font-weight: 500;
    color: var(--text-dark);
}
.cart-item-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.cart-item-detail > div {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 14px;
    color: var(--text-muted);
}
.cart-item-detail strong {
    font-weight: 500;
    color: var(--text-dark);
}
.cart-item-remove-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #ef4444;
    padding: 5px;
}
.cart-item-remove-btn:hover { color: #b91c1c; }
.cart-item-remove-btn svg { width: 20px; height: 20px; }
.cart-footer {
    padding: 24px;
    border-top: 1px solid var(--border-color);
    background-color: #f8f9fa;
    flex-shrink: 0;
}
.cart-total {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 16px;
}
.cart-total span {
    font-size: 16px;
    color: var(--text-muted);
}
.cart-total strong {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-dark);
}
.btn-checkout {
    display: block;
    width: 100%;
    padding: 14px;
    background-color: var(--brand-primary);
    color: white;
    text-align: center;
    border-radius: 12px;
    font-weight: 500;
    font-size: 16px;
    text-decoration: none;
    transition: background-color 0.2s;
}
.btn-checkout:hover { background-color: #082a4d; }

/* Toast Notification Styling */
.toast-notification { /* ... (tidak ada perubahan) ... */ }
</style>

<!-- KONTEN UTAMA HALAMAN (TIDAK ADA PERUBAHAN) -->
<main class="figma-content-wrapper">
    <h2 class="page-title">
        <a href="venue_detail.php?id=<?php echo $venue_id; ?>">
             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a> 
        Pilih Lapangan
    </h2>

    <div class="main-content-flex">
        <?php if (empty($courts)): ?>
            <p>Tidak ada lapangan atau jadwal yang tersedia untuk venue ini.</p>
        <?php else: ?>
            <?php $court = reset($courts); ?>
            <div class="left-column">
                <div class="court-image-slider">
                    <img src="images/<?php echo htmlspecialchars($venue['main_image']); ?>" data-full-image="images/<?php echo htmlspecialchars($venue['main_image']); ?>" alt="<?php echo htmlspecialchars($court['name']); ?>">
                </div>
            </div>

            <div class="right-column">
                <div class="court-details">
                    <h3><?php echo htmlspecialchars($court['name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars($court['description']); ?></p>
                    <div class="court-features">
                        <?php foreach ($court['tags'] as $tag):?><div class="feature-item"><?php echo $tag['icon']; ?><span><?php echo htmlspecialchars($tag['text']); ?></span></div><?php endforeach; ?>
                    </div>
                    <button class="schedule-dropdown">
                        <span><?php echo count($court['schedules']); ?> Jadwal Tersedia</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="chevron"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="time-slots-grid">
                        <?php foreach ($court['schedules'] as $schedule): ?>
                            <?php if ($schedule['is_booked']): ?>
                                <div class="time-slot booked" title="Sudah di-booking"><div class="duration">60 Menit</div><div class="time"><?php echo date('H:i', strtotime($schedule['start_time'])); ?> - <?php echo date('H:i', strtotime($schedule['end_time'])); ?></div><div class="price">Booked</div></div>
                            <?php else: ?>
                                <div class="time-slot available" data-id="<?php echo $schedule['id']; ?>" data-court="<?php echo htmlspecialchars($court['name']); ?>" data-time="<?php echo date('H:i', strtotime($schedule['start_time'])) . ' - ' . date('H:i', strtotime($schedule['end_time'])); ?>" data-price="<?php echo $schedule['price']; ?>"><div class="duration">60 Menit</div><div class="time"><?php echo date('H:i', strtotime($schedule['start_time'])); ?> - <?php echo date('H:i', strtotime($schedule['end_time'])); ?></div><div class="price">Rp<?php echo number_format($schedule['price'], 0, ',', '.'); ?></div></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- HTML KERANJANG BARU (TIDAK ADA PERUBAHAN) -->
<aside class="cart-sidebar" id="cart-sidebar">
    <div class="cart-header">
        <h3>Jadwal Dipilih</h3>
        <button class="cart-close-btn" id="cart-close-btn" aria-label="Tutup Keranjang">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    <div class="cart-body" id="cart-body">
        <!-- Item keranjang akan ditambahkan di sini oleh JavaScript -->
    </div>
    <div class="cart-footer">
        <div class="cart-total">
            <span>Total</span>
            <strong id="cart-total-price">Rp0</strong>
        </div>
        <a href="#" class="btn-checkout">Lanjut Pembayaran</a>
    </div>
</aside>

<!-- Modal & Toast (TIDAK ADA PERUBAHAN) -->
<div class="toast-notification" id="toast-notification">Jadwal ditambahkan!</div>
<div class="image-modal-overlay" id="imageModal"><div class="image-modal-content"><span class="image-modal-close" id="imageModalClose">×</span><img src="" alt="Detail Lapangan" id="modalImageContent"></div></div>

<!-- SCRIPT (TIDAK ADA PERUBAHAN) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.schedule-dropdown').forEach(button => {
        button.addEventListener('click', () => {
            const grid = button.nextElementSibling;
            if (grid && grid.classList.contains('time-slots-grid')) {
                grid.classList.toggle('open');
                button.classList.toggle('open');
            }
        });
    });

    const cartSidebar = document.getElementById('cart-sidebar');
    const cartBody = document.getElementById('cart-body');
    const closeCartBtn = document.getElementById('cart-close-btn');
    const toast = document.getElementById('toast-notification');
    const cartTotalPriceEl = document.getElementById('cart-total-price');
    
    let selectedSlots = [];
    const selectedDate = "<?php echo date('D, d M Y'); ?>";

    const showCart = () => cartSidebar.classList.add('is-visible');
    const hideCart = () => cartSidebar.classList.remove('is-visible');
    const showToast = (message) => {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    };

    function updateCartUI() {
        cartBody.innerHTML = '';
        let totalPrice = 0;
        selectedSlots.forEach(slot => {
            totalPrice += parseInt(slot.price);
            const itemHTML = `
                <div class="cart-item" data-id="${slot.id}">
                    <div class="cart-item-title">${slot.court}</div>
                    <div class="cart-item-detail">
                        <div>
                            <div>${slot.date}</div>
                            <div>${slot.time}</div>
                            <strong>Rp${parseInt(slot.price).toLocaleString('id-ID')}</strong>
                        </div>
                        <button class="cart-item-remove-btn" data-id="${slot.id}" aria-label="Hapus jadwal ${slot.court} jam ${slot.time}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>
                </div>`;
            cartBody.insertAdjacentHTML('beforeend', itemHTML);
        });
        cartTotalPriceEl.textContent = `Rp${totalPrice.toLocaleString('id-ID')}`;
        if (selectedSlots.length === 0) {
            hideCart();
        } else {
            showCart();
        }
    }
    
    document.querySelector('.time-slots-grid').addEventListener('click', function(e) {
        const slotEl = e.target.closest('.time-slot.available');
        if (!slotEl) return;
        const slotData = {
            id: slotEl.dataset.id,
            court: slotEl.dataset.court,
            time: slotEl.dataset.time,
            price: slotEl.dataset.price,
            date: selectedDate
        };
        const isSelected = slotEl.classList.toggle('selected');
        if (isSelected) {
            selectedSlots.push(slotData);
            showToast('Jadwal ditambahkan!');
        } else {
            selectedSlots = selectedSlots.filter(s => s.id !== slotData.id);
        }
        updateCartUI();
    });
    
    closeCartBtn.addEventListener('click', hideCart);

    cartBody.addEventListener('click', function(e){
        const removeButton = e.target.closest('.cart-item-remove-btn');
        if (!removeButton) return;
        const idToRemove = removeButton.dataset.id;
        selectedSlots = selectedSlots.filter(slot => slot.id !== idToRemove);
        const timeSlotElement = document.querySelector(`.time-slot.available[data-id="${idToRemove}"]`);
        if (timeSlotElement) {
            timeSlotElement.classList.remove('selected');
        }
        updateCartUI();
    });

    const imageModal = document.getElementById('imageModal');
    const modalImageContent = document.getElementById('modalImageContent');
    const closeModalBtn = document.getElementById('imageModalClose');
    document.querySelectorAll('.court-image-slider img').forEach(image => {
        image.addEventListener('click', function() {
            modalImageContent.src = this.dataset.fullImage;
            imageModal.classList.add('visible');
        });
    });
    const closeModal = () => imageModal.classList.remove('visible');
    closeModalBtn.addEventListener('click', closeModal);
    imageModal.addEventListener('click', (e) => e.target === imageModal && closeModal());
});
</script>

<?php 
require_once 'includes/footer.php'; 
?>