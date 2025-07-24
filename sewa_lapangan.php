<?php
$page_title = 'Sewa Lapangan - Sportiva';
$active_page = 'sewa';
$css_files = ['sewa_lapangan_styles.css'];
$include_auth_modal = true;
require_once 'includes/header.php';
require_once 'config/database.php';

// --- PENGATURAN PAGINASI ---
$venues_per_page = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $venues_per_page;

// --- Ambil semua filter dari URL ---
$search_term = $_GET['venue_name'] ?? '';
$selected_city = $_GET['city'] ?? '';
$selected_sport_id = $_GET['sport_id'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'name_asc';
$min_price_filter = $_GET['min_price'] ?? '';
$max_price_filter = $_GET['max_price'] ?? '';

// Ambil data untuk dropdown filter
try {
    $stmt_cities = $pdo->query("SELECT DISTINCT city FROM venues WHERE city IS NOT NULL AND city != '' ORDER BY city ASC");
    $cities = $stmt_cities->fetchAll(PDO::FETCH_ASSOC);
    $stmt_sports_filter = $pdo->query("SELECT id, name FROM sports ORDER BY name ASC");
    $sports_for_filter = $stmt_sports_filter->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error mengambil data filter: " . $e->getMessage());
}

// === BAGIAN YANG DIPERBAIKI SECARA TOTAL: KONSTRUKSI SQL ===

// 1. Definisikan bagian-bagian query
$sql_from_joins = "FROM venues v 
                   LEFT JOIN venue_sports vs ON v.id = vs.venue_id
                   LEFT JOIN schedules s ON v.id = s.venue_id";
$sql_group_by = "GROUP BY v.id"; // Group by ID utama sudah cukup

$where_clauses = [];
$params = [];

// 2. Bangun klausa WHERE
if (!empty($search_term)) {
    $where_clauses[] = "v.name LIKE :search_term";
    $params[':search_term'] = '%' . $search_term . '%';
}
if (!empty($selected_city)) {
    $where_clauses[] = "v.city = :city";
    $params[':city'] = $selected_city;
}
if (!empty($selected_sport_id)) {
    $where_clauses[] = "vs.sport_id = :sport_id";
    $params[':sport_id'] = $selected_sport_id;
}
$sql_where = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

// 3. Bangun klausa HAVING untuk filter harga
$having_clauses = [];
if ($min_price_filter !== '' && is_numeric($min_price_filter)) {
    $having_clauses[] = "min_price >= :min_price";
    $params[':min_price'] = $min_price_filter;
}
if ($max_price_filter !== '' && is_numeric($max_price_filter)) {
    $having_clauses[] = "min_price <= :max_price";
    $params[':max_price'] = $max_price_filter;
}
$sql_having = !empty($having_clauses) ? " HAVING " . implode(" AND ", $having_clauses) : "";

// 4. Bangun query untuk MENGHITUNG TOTAL HASIL
// === PERBAIKAN UTAMA DI SINI ===
// Subquery SEKARANG menyertakan MIN(s.price) agar 'having clause' bisa bekerja.
$count_query_string = "SELECT COUNT(*) FROM (
                            SELECT v.id, MIN(s.price) as min_price 
                            {$sql_from_joins} 
                            {$sql_where} 
                            {$sql_group_by} 
                            {$sql_having}
                       ) AS filtered_venues";
$stmt_count = $pdo->prepare($count_query_string);
$stmt_count->execute($params);
$total_venues = $stmt_count->fetchColumn();
$total_pages = $total_venues > 0 ? ceil($total_venues / $venues_per_page) : 0;


// 5. Bangun query AKHIR untuk mengambil data per halaman
$venues = [];
$venue_count_on_page = 0;
if ($total_venues > 0) {
    $sql_select_data = "SELECT v.id, v.name, v.address, v.city, v.main_image, v.rating, MIN(s.price) as min_price";
    $sql_order_by = "";
    switch ($sort_by) {
        case 'price_asc': $sql_order_by = " ORDER BY CASE WHEN min_price IS NULL THEN 1 ELSE 0 END, min_price ASC, v.name ASC"; break;
        case 'price_desc': $sql_order_by = " ORDER BY min_price DESC, v.name ASC"; break;
        case 'name_desc': $sql_order_by = " ORDER BY v.name DESC"; break;
        default: $sql_order_by = " ORDER BY v.name ASC"; break;
    }

    $sql_limit = " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $venues_per_page;
    $params[':offset'] = $offset;

    $final_sql = $sql_select_data . " " . $sql_from_joins . " " . $sql_where . " " . $sql_group_by . " " . $sql_having . " " . $sql_order_by . " " . $sql_limit;
    $stmt = $pdo->prepare($final_sql);

    foreach ($params as $key => &$value) {
        $type = ($key === ':limit' || $key === ':offset') ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindParam($key, $value, $type);
    }
    $stmt->execute();
    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $venue_count_on_page = count($venues);

    if ($venue_count_on_page > 0) {
        $venue_ids = array_column($venues, 'id');
        $placeholders = implode(',', array_fill(0, count($venue_ids), '?'));
        $sql_sports = "SELECT vs.venue_id, s.name, s.icon_svg FROM venue_sports vs JOIN sports s ON vs.sport_id = s.id WHERE vs.venue_id IN ($placeholders)";
        $stmt_sports = $pdo->prepare($sql_sports);
        $stmt_sports->execute($venue_ids);
        $venue_sports_data = $stmt_sports->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
    }
}
?>

<style>
/* ... (SEMUA CSS DARI SEBELUMNYA TETAP SAMA) ... */
:root {
  --text-white: #ffffff; --text-dark-blue: #0D4072; --background-light: #F8F9FA;
  --border-color: #DEE2E6; --text-dark: #212529; --text-muted: #6C757D;
  --font-primary: 'Poppins', sans-serif; --star-color: #ffc107;
}
body { background-color: var(--background-light); font-family: var(--font-primary); }
.main-content { flex-grow: 1; padding: 32px 40px; max-width: 1320px; margin: 0 auto; width: 100%; }
.search-form { display: flex; gap: 12px; align-items: stretch; flex-wrap: wrap; margin-bottom: 24px;}
.input-group { flex: 1 1 200px; min-width: 180px; display: flex; align-items: center; background-color: var(--text-white); border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; height: 48px; transition: border-color 0.2s, box-shadow 0.2s; }
.input-group:focus-within { border-color: var(--text-dark-blue); box-shadow: 0 0 0 2px rgba(13, 64, 114, 0.2); }
.input-icon { width: 18px; height: 18px; color: #9CA3AF; margin-right: 8px; flex-shrink: 0; }
.input-group input, .input-group select { width: 100%; height: 100%; border: none; outline: none; background: transparent; font-size: 14px; font-family: var(--font-primary); color: var(--text-dark); }
.search-button, .filter-button { height: 48px; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s ease; padding: 0 24px; flex-shrink: 0; border: none; }
.search-button { background-color: var(--text-dark-blue); color: var(--text-white); }
.filter-button { background-color: #e7f6fd; color: var(--text-dark-blue); padding: 0 16px; }
.filter-button:hover { background-color: #d0ebfb; }
.filter-button svg { width: 20px; height: 20px; }
.results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.results-count { font-size: 14px; color: var(--text-muted); }
.results-count strong { color: var(--text-dark); font-weight: 600; }
.sort-options { font-size: 14px; color: var(--text-muted); }
.sort-options select { border: 1px solid var(--border-color); border-radius: 6px; padding: 4px 8px; background: var(--text-white); font-weight: 500; color: var(--text-dark); cursor: pointer; outline: none; margin-left: 4px; }
.venue-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
.venue-card { background-color: var(--text-white); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; display: flex; flex-direction: column; }
.venue-card a { text-decoration: none; color: inherit; display: flex; flex-direction: column; flex-grow: 1; }
.venue-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
.card-image-container { width: 100%; height: 180px; overflow: hidden; }
.venue-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
.card-info { padding: 16px; display: flex; flex-direction: column; gap: 8px; flex-grow: 1; }
.venue-type { font-size: 12px; color: var(--text-muted); font-weight: 500; }
.card-info h3 { font-size: 18px; font-weight: 600; color: var(--text-dark); margin: 0; line-height: 1.3; }
.venue-rating-location { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); }
.venue-rating-location .icon-star { width: 16px; height: 16px; color: var(--star-color); flex-shrink: 0; }
.venue-sports { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 4px; }
.sport-item { display: flex; align-items: center; gap: 5px; font-size: 13px; color: var(--text-muted); }
.sport-item svg { width: 16px; height: 16px; color: var(--text-muted); }
.card-bottom { margin-top: auto; padding-top: 16px; }
.venue-price { font-size: 14px; color: var(--text-muted); display: flex; align-items: baseline; gap: 4px; }
.venue-price strong { font-size: 18px; font-weight: 700; color: var(--text-dark-blue); font-family: var(--font-secondary); }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
.modal-overlay.active { opacity: 1; visibility: visible; }
.modal-content { background: white; padding: 24px; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); width: 90%; max-width: 450px; transform: scale(0.95); transition: transform 0.3s ease; }
.modal-overlay.active .modal-content { transform: scale(1); }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); }
.modal-header h3 { margin: 0; font-size: 20px; color: var(--text-dark); }
.modal-close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted); }
.modal-body h4 { font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 12px; }
.price-filter-inputs { display: flex; gap: 16px; align-items: center; }
.price-input-group { flex: 1; position: relative; }
.price-input-group label { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }
.price-input-group input { width: 100%; height: 48px; border-radius: 8px; border: 1px solid var(--border-color); padding-left: 40px; font-size: 14px; }
.price-input-group input:focus { border-color: var(--text-dark-blue); outline: none; box-shadow: 0 0 0 2px rgba(13, 64, 114, 0.2); }
.modal-footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-color); }
.apply-filter-btn { width: 100%; height: 48px; background-color: var(--text-dark-blue); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
.apply-filter-btn:hover { background-color: #0C3A6B; }
.no-results-container { text-align: center; padding: 60px 20px; background-color: #fff; border-radius: 16px; border: 1px solid var(--border-color); margin-top: 24px; }
.no-results-container h3 { margin-top: 0; }
.pagination-container { margin-top: 48px; display: flex; justify-content: center; align-items: center; }
.pagination { list-style: none; padding: 0; margin: 0; display: flex; gap: 8px; }
.pagination li a, .pagination li span { display: flex; justify-content: center; align-items: center; min-width: 40px; height: 40px; padding: 0 12px; text-decoration: none; color: var(--text-dark); background-color: var(--text-white); border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 500; font-size: 14px; transition: all 0.2s ease; }
.pagination li a:hover { border-color: var(--text-dark-blue); color: var(--text-dark-blue); }
.pagination li.active a { background-color: var(--text-dark-blue); color: var(--text-white); border-color: var(--text-dark-blue); font-weight: 700; }
.pagination li.disabled a, .pagination li.disabled span { color: #cbd5e1; pointer-events: none; }
.pagination li.ellipsis span { border-color: transparent; background-color: transparent; }
</style>

<main class="main-content">
    <form class="search-form" action="sewa_lapangan.php" method="GET" id="filter-form">
        <!-- Input fields -->
        <div class="input-group">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
            <input type="text" name="venue_name" placeholder="Cari nama venue" value="<?php echo htmlspecialchars($search_term); ?>">
        </div>
        <div class="input-group">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <select name="city" onchange="this.form.submit()">
                <option value="">Semua Kota</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city['city']); ?>" <?php if ($selected_city == $city['city']) echo 'selected'; ?>><?php echo htmlspecialchars($city['city']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group">
           <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.84l8.57 3.9a2 2 0 0 0 1.66 0l8.57-3.9a1 1 0 0 0 0-1.84Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
           <select name="sport_id" onchange="this.form.submit()">
               <option value="">Semua Olahraga</option>
               <?php foreach ($sports_for_filter as $sport): ?>
                   <option value="<?php echo $sport['id']; ?>" <?php if ($selected_sport_id == $sport['id']) echo 'selected'; ?>><?php echo htmlspecialchars($sport['name']); ?></option>
               <?php endforeach; ?>
           </select>
        </div>
        
        <button type="button" class="filter-button" id="open-filter-modal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
        </button>
        
        <button type="submit" class="search-button">Cari Venue</button>
        
        <input type="hidden" name="min_price" id="modal_min_price" value="<?php echo htmlspecialchars($min_price_filter); ?>">
        <input type="hidden" name="max_price" id="modal_max_price" value="<?php echo htmlspecialchars($max_price_filter); ?>">
    </form>
  
    <div class="results-header">
        <div class="results-count">Menampilkan <strong><?php echo $total_venues; ?></strong> venue</div>
        <div class="sort-options">
            Urutkan: 
            <select name="sort_by" onchange="this.form.submit()" form="filter-form">
                <option value="name_asc" <?php if($sort_by == 'name_asc') echo 'selected'; ?>>Nama (A-Z)</option>
                <option value="name_desc" <?php if($sort_by == 'name_desc') echo 'selected'; ?>>Nama (Z-A)</option>
                <option value="price_asc" <?php if($sort_by == 'price_asc') echo 'selected'; ?>>Harga Termurah</option>
                <option value="price_desc" <?php if($sort_by == 'price_desc') echo 'selected'; ?>>Harga Termahal</option>
            </select>
        </div>
    </div>

    <?php if ($total_venues > 0): ?>
        <div class="venue-grid">
            <?php foreach ($venues as $venue): ?>
            <article class="venue-card">
              <a href="venue_detail.php?id=<?php echo $venue['id']; ?>">
                <div class="card-image-container">
                  <img src="images/<?php echo htmlspecialchars($venue['main_image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>" onerror="this.onerror=null;this.src='images/default.jpg';">
                </div>
                <div class="card-info">
                    <span class="venue-type">Venue</span>
                    <h3><?php echo htmlspecialchars($venue['name']); ?></h3>
                    <div class="venue-rating-location">
                        <?php if (!empty($venue['rating']) && $venue['rating'] > 0): ?>
                            <svg class="icon-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.868 2.884c.321-.662 1.215-.662 1.536 0l1.681 3.468 3.82.556c.734.107 1.028.997.494 1.503l-2.764 2.694.652 3.803c.124.722-.638 1.278-1.284.944L10 13.6l-3.419 1.796c-.646.334-1.408-.222-1.284-.944l.652-3.803L2.27 8.411c-.534-.506-.24-1.396.494-1.503l3.82-.556 1.681-3.468z" clip-rule="evenodd" /></svg>
                            <span><?php echo number_format($venue['rating'], 2); ?> ·</span>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($venue['city']); ?></span>
                    </div>
                    <?php if (!empty($venue_sports_data[$venue['id']])): ?>
                    <div class="venue-sports">
                        <?php foreach (array_slice($venue_sports_data[$venue['id']], 0, 2) as $sport): ?>
                        <div class="sport-item">
                            <?php echo $sport['icon_svg']; ?>
                            <span><?php echo htmlspecialchars($sport['name']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="card-bottom">
                        <?php if (!empty($venue['min_price']) && $venue['min_price'] > 0): ?>
                            <div class="venue-price">
                                <span>Mulai</span>
                                <strong>Rp<?php echo number_format($venue['min_price'], 0, ',', '.'); ?></strong>
                                <span>/sesi</span>
                            </div>
                        <?php else: ?>
                            <div class="venue-price"><span>Hubungi untuk harga</span></div>
                        <?php endif; ?>
                    </div>
                </div>
              </a>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav class="pagination-container" aria-label="Page navigation">
            <ul class="pagination">
                <?php
                $query_params = $_GET;
                $range = 2;
                $last_page_shown = 0;
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
                        if ($last_page_shown && $i > $last_page_shown + 1) {
                            echo '<li class="disabled ellipsis"><span>...</span></li>';
                        }
                        $active_class = ($i == $current_page) ? 'active' : '';
                        $query_params['page'] = $i;
                        echo '<li class="' . $active_class . '"><a href="?' . http_build_query($query_params) . '">' . $i . '</a></li>';
                        $last_page_shown = $i;
                    }
                }
                if ($current_page < $total_pages) {
                    $query_params['page'] = $current_page + 1;
                    echo '<li><a href="?' . http_build_query($query_params) . '">→</a></li>';
                }
                ?>
            </ul>
        </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="no-results-container">
            <h3>Hasil Tidak Ditemukan</h3>
            <p>Coba ubah atau hapus filter pencarian Anda untuk melihat lebih banyak hasil.</p>
        </div>
    <?php endif; ?>
</main>

<!-- HTML MODAL FILTER -->
<div class="modal-overlay" id="filter-modal-overlay">
    <div class="modal-content">
        <div class="modal-header"><h3>Filter</h3><button class="modal-close-btn" id="close-filter-modal">×</button></div>
        <div class="modal-body">
            <h4>Biaya</h4>
            <div class="price-filter-inputs">
                <div class="price-input-group"><label for="min_price_display">Rp</label><input type="number" id="min_price_display" placeholder="0" value="<?php echo htmlspecialchars($min_price_filter); ?>"></div>
                <div class="price-input-group"><label for="max_price_display">Rp</label><input type="number" id="max_price_display" placeholder="5.000.000+" value="<?php echo htmlspecialchars($max_price_filter); ?>"></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="apply-filter-btn" id="apply-filter-btn">Terapkan</button></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openModalBtn = document.getElementById('open-filter-modal');
    const closeModalBtn = document.getElementById('close-filter-modal');
    const modalOverlay = document.getElementById('filter-modal-overlay');
    const applyFilterBtn = document.getElementById('apply-filter-btn');
    const filterForm = document.getElementById('filter-form');
    const minPriceDisplay = document.getElementById('min_price_display');
    const maxPriceDisplay = document.getElementById('max_price_display');
    const minPriceHidden = document.getElementById('modal_min_price');
    const maxPriceHidden = document.getElementById('modal_max_price');
    if (openModalBtn) { openModalBtn.addEventListener('click', () => modalOverlay.classList.add('active')); }
    const closeModal = () => modalOverlay.classList.remove('active');
    if (closeModalBtn) { closeModalBtn.addEventListener('click', closeModal); }
    if (modalOverlay) { modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); }); }
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            minPriceHidden.value = minPriceDisplay.value;
            maxPriceHidden.value = maxPriceDisplay.value;
            filterForm.submit();
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>