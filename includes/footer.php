  <footer class="site-footer">
      <p>© 2000 - Company, Inc. All rights reserved. Address Address</p>
  </footer>
</div> <!-- .page-container -->

<div class="sidebar-overlay"></div>
<aside class="cart-sidebar">
  <div class="cart-header"><h2>Jadwal Dipilih</h2><button class="close-btn" aria-label="Close cart">×</button></div>
  <div class="cart-body"><div class="cart-empty is-visible"><p>belum ada di keranjang.</p></div></div>
</aside>

<!-- PERBAIKAN: Modal hanya akan dimuat jika pengguna BELUM LOGIN -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="auth-modal-overlay" id="auth-modal-container">
    <div class="auth-modal">
        <button class="close-modal-btn" id="close-modal-btn">×</button>
        <!-- Form Register -->
        <div class="form-container" id="register-form">
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <h3>Daftar</h3><p>Sudah punya akun? <span class="switch-link" id="switch-to-login">Masuk</span></p>
                <div class="form-group"><input type="text" name="name" placeholder="Nama Lengkap" required></div>
                <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
                <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
                <button type="submit" class="btn">Daftar</button>
            </form>
        </div>
        <!-- Form Login -->
        <div class="form-container hidden" id="login-form">
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <h3>Masuk</h3><p>Belum punya akun? <span class="switch-link" id="switch-to-register">Daftar</span></p>
                <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
                <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
                <button type="submit" class="btn">Masuk</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="js/main.js"></script>
<?php if(isset($js_files)) {
    foreach ($js_files as $js) {
        echo "<script src=\"js/$js\"></script>";
    }
} ?>
</body>
</html>