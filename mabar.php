<?php
// --- PENGATURAN HALAMAN & HEADER ---
$page_title = 'Mabar - Sportiva';
$active_page = 'mabar';
$css_files = ['sewa_lapangan_styles.css', 'mabar_styles.css']; // Pastikan mabar_styles.css ada di sini
$include_auth_modal = true; // Kita butuh modal login di sini
require_once 'includes/header.php';
require_once 'config/database.php';

// --- MENGAMBIL DATA SEMUA EVENT ---
// Mengambil semua event, diurutkan dari yang terbaru
$stmt = $pdo->query("SELECT * FROM mabar_events ORDER BY event_date DESC, start_time DESC");
$events = $stmt->fetchAll();
?>

<!-- CSS untuk link kartu (opsional, tapi sangat disarankan) -->
<style>
  .event-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }

  .event-card-link:hover .event-card {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(37, 40, 43, 0.12);
  }
</style>

<main class="main-content">
  <!-- Banner Section -->
  <section class="banner-section">
    <img src="images/uploads_2025_03_708a78c4e7b02d17d271da0e1f24567121bf7e8d746747043712e29d4602c717dcb55f2d_default_png_1749836038102_256.png" alt="Tournament Banner">
  </section>

  <!-- Results Section -->
  <section class="results-section">
    <div class="results-header">
      <p>Menampilkan <strong><?php echo count($events); ?> event mabar</strong></p>
    </div>

    <div class="content-area">
      <div class="event-grid">
        
        <?php if (empty($events)): ?>
          <p>Belum ada event mabar yang tersedia saat ini.</p>
        <?php else: ?>
          <?php foreach ($events as $event): ?>
            <!-- SETIAP KARTU DIBUNGKUS DENGAN LINK DINAMIS KE HALAMAN DETAIL -->
            <a href="mabar_detail.php?id=<?php echo htmlspecialchars($event['id']); ?>" class="event-card-link">
              <article class="event-card">
                <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                <div class="event-meta">
                  <p>🏸 <?php echo htmlspecialchars($event['sport'] . ' • ' . $event['skill_level']); ?></p>
                  <p>🗓️ <?php echo date('D, d M Y, H:i', strtotime($event['event_date'] . ' ' . $event['start_time'])); ?></p>
                  <p>📍 <?php echo htmlspecialchars($event['venue_name']); ?></p>
                </div>
                <div class="event-tags">
                  <?php 
                    if (!empty($event['tags'])) {
                      $tags = explode(',', $event['tags']);
                      foreach ($tags as $tag_text) {
                        // Logika untuk menentukan kelas tag
                        $tag_class = (strpos(strtolower($tag_text), 'superhost') !== false) ? 'superhost' : 'booked';
                        echo "<span class='tag " . $tag_class . "'>" . htmlspecialchars(trim($tag_text)) . "</span>";
                      }
                    }
                  ?>
                </div>
                <div class="event-participants">
                  <p class="slot-info"><?php echo htmlspecialchars($event['slots_filled']); ?> / <?php echo htmlspecialchars($event['total_slots']); ?></p>
                </div>
                <hr class="card-divider">
                <div class="host-info">
                  <img src="images/<?php echo htmlspecialchars($event['host_logo']); ?>" class="host-logo" alt="Host Logo">
                  <div class="host-details">
                    <p class="host-name"><?php echo htmlspecialchars($event['host_name']); ?></p>
                  </div>
                </div>
              </article>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </section>
</main>

<?php 
// Sertakan footer
require_once 'includes/footer.php'; 
?>