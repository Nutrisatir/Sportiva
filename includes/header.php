<?php
// Memulai session di setiap halaman, hanya jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Variabel untuk mengecek status login dengan mudah
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title ?? 'Sportiva - Your Sports Hub'; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="css/sidebar_styles.css">
  <link rel="stylesheet" href="css/auth_styles.css">
  <?php if(isset($css_files)) {
      foreach ($css_files as $css) {
          echo "<link rel=\"stylesheet\" href=\"css/$css\">";
      }
  } ?>
  
  <style>
    /* CSS untuk Dropdown dan Tata Letak Header */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 25px;
    }
    
    /* PERUBAHAN DI SINI: Margin atas ditambah untuk menurunkan ikon */
    .cart-link {
        margin-top: 7px; 
        margin-right: 25px;
    }

    .user-menu {
        position: relative;
    }
    .user-menu-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        color: white;
        text-decoration: none;
    }
    .user-menu-trigger .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }
    .user-menu-trigger span {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 14px;
    }

    .user-dropdown {
        display: none;
        position: absolute;
        top: 100%; 
        right: -20px; 
        width: 190px; 
        height: 120px; 

        background-image: url('images/bguser.png');
        background-size: contain; 
        background-repeat: no-repeat;
        background-position: center;
        
        background-color: transparent;
        border-radius: 0;
        box-shadow: none;
        
        padding: 38.5px 15px 10px 15px; 
    }
    .user-dropdown.show {
        display: block; 
    }
    .user-dropdown a {
        display: block;
        text-align: center;
        padding: 5px 0;
        margin: 5px 0;
        border-radius: 20px;
        text-decoration: none;
        color: white;
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 14px;
        transition: opacity 0.2s;
    }
    .user-dropdown a:hover {
        opacity: 0.9;
    }
    .user-dropdown .profile-btn {
        background: #0B3767;
    }
    .user-dropdown .logout-btn {
        background: #FF0000;
    }
  </style>

</head>
<body>
<div class="page-container">
  <header class="site-header">
      <div class="header-content">
          <div class="logo-area">
            <a href="index.php" class="logo-text">Sportiva</a>
          </div>

          <div class="header-right">
              <nav class="main-nav">
                  <ul>
                      <li><a href="index.php" class="<?php echo ($active_page == 'home') ? 'active' : ''; ?>">Home</a></li>
                      <li><a href="sewa_lapangan.php" class="<?php echo ($active_page == 'sewa') ? 'active' : ''; ?>">Sewa lapangan</a></li>
                      <li><a href="mabar.php" class="<?php echo ($active_page == 'mabar') ? 'active' : ''; ?>">Mabar</a></li>
                  </ul>
              </nav>

              <div class="header-actions">
                  <a href="#" class="cart-link">
                      <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24"><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" fill="none" stroke="white" stroke-width="1.5"/></svg>
                  </a>
                  
                  <?php if ($is_logged_in): ?>
                      <div class="user-menu">
                          <a href="#" class="user-menu-trigger">
                              <img src="images/user.png" alt="User Avatar" class="user-avatar">
                              <span><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                          </a>
                          <div class="user-dropdown">
                              <a href="#" class="profile-btn">Informasi Akun</a>
                              <a href="logout.php" class="logout-btn">Logout</a>
                          </div>
                      </div>
                  <?php else: ?>
                      <div class="auth-buttons" id="auth-buttons-logged-out">
                          <a href="#" class="btn-login" id="login-btn-header">Masuk</a>
                          <a href="#" class="btn-register" id="register-btn-header">Daftar</a>
                      </div>
                  <?php endif; ?>
              </div>
          </div>
      </div>
  </header>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuTrigger = document.querySelector('.user-menu-trigger');
        const userDropdown = document.querySelector('.user-dropdown');

        if (userMenuTrigger && userDropdown) {
            userMenuTrigger.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            window.addEventListener('click', function(event) {
                if (userDropdown.classList.contains('show') && !userDropdown.contains(event.target) && !userMenuTrigger.contains(event.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }
    });
  </script>