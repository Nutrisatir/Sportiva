document.addEventListener('DOMContentLoaded', function() {
  
  // --- FUNGSI SIDEBAR KERANJANG (CART) ---
  const cartButtons = document.querySelectorAll('.cart-link');
  const sidebar = document.querySelector('.cart-sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  const closeButton = document.querySelector('.close-btn');

  if (cartButtons.length > 0 && sidebar && overlay && closeButton) {
    const openSidebar = function(event) { 
      event.preventDefault(); 
      sidebar.classList.add('is-open'); 
      overlay.classList.add('is-open'); 
    };
    const closeSidebar = function() { 
      sidebar.classList.remove('is-open'); 
      overlay.classList.remove('is-open'); 
    };
    cartButtons.forEach(button => button.addEventListener('click', openSidebar));
    closeButton.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function(event) { if (event.key === 'Escape') closeSidebar(); });
  }

  // --- LOGIKA MODAL LOGIN/REGISTER ---
  const modalContainer = document.getElementById('auth-modal-container');
  
  // Kode ini hanya berjalan jika modal ada di halaman (yaitu, jika pengguna belum login)
  if (modalContainer) {
    const loginBtnHeader = document.getElementById('login-btn-header');
    const registerBtnHeader = document.getElementById('register-btn-header');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');
    const switchToLoginLink = document.getElementById('switch-to-login');
    const switchToRegisterLink = document.getElementById('switch-to-register');

    const openModal = () => modalContainer.classList.add('is-visible');
    const closeModal = () => modalContainer.classList.remove('is-visible');

    const showLoginForm = () => {
        loginForm.classList.remove('hidden', 'from-right');
        registerForm.classList.add('hidden');
    };

    const showRegisterForm = () => {
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden', 'from-right');
    };

    loginBtnHeader.addEventListener('click', (e) => { e.preventDefault(); showLoginForm(); openModal(); });
    registerBtnHeader.addEventListener('click', (e) => { e.preventDefault(); showRegisterForm(); openModal(); });
    closeModalBtn.addEventListener('click', closeModal);
    modalContainer.addEventListener('click', (e) => { if(e.target === modalContainer) closeModal(); });
    switchToLoginLink.addEventListener('click', showLoginForm);
    switchToRegisterLink.addEventListener('click', showRegisterForm);
  }

  // --- FUNGSI SLIDER UNTUK HALAMAN HOME (index.html) ---
  const heroSection = document.querySelector('.hero-section');
  if (heroSection) {
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const slidesData = [
        { image: "url('images/slider/Football_Slider.png')", title: "Football", description: "Details On Training Sessions, Tournaments, And Court Booking." },
        { image: "url('images/slider/Badminton_Slider.png')", title: "Badminton", description: "Find Your Partner, Join a Match, and Book Your Favorite Court." },
        { image: "url('images/slider/Basketball_Slider.png')", title: "Basketball", description: "Slam Dunk Your Way to Victory, Join a Team or Book a Court." },
        { image: "url('images/slider/Tabletennis_Slider.png')", title: "Table Tennis", description: "Serve, Spin, and Smash. Book a Table for a Quick Match." },
        { image: "url('images/slider/Volleyball_Slider.png')", title: "Volleyball", description: "Spike it Down! Join thrilling matches and book beach or indoor courts." }
    ];
    let currentSlideIndex = 0; let slideInterval;
    const infoTitle = document.querySelector('.info-box h1');
    const infoDesc = document.querySelector('.info-box p');
    const dots = document.querySelectorAll('.slider-pagination .dot');

    function createSlides() {
        if (!sliderWrapper) return;
        sliderWrapper.innerHTML = ''; // Kosongkan dulu
        slidesData.forEach(item => {
            const div = document.createElement('div');
            div.className = 'slide';
            div.style.backgroundImage = item.image;
            sliderWrapper.appendChild(div);
        });
        sliderWrapper.style.width = `${slidesData.length * 100}%`;
    }

    function updateSlide(index) {
        if (!sliderWrapper || !infoTitle || !infoDesc || !dots[index]) return;
        sliderWrapper.style.transform = `translateX(${-index * 100 / slidesData.length}%)`;
        infoTitle.textContent = slidesData[index].title;
        infoDesc.textContent = slidesData[index].description;
        dots.forEach(dot => dot.classList.remove('active'));
        dots[index].classList.add('active');
        currentSlideIndex = index;
    }
    
    function nextSlide() { updateSlide((currentSlideIndex + 1) % slidesData.length); }
    function resetInterval() { clearInterval(slideInterval); slideInterval = setInterval(nextSlide, 5000); }

    document.querySelector('.next-arrow')?.addEventListener('click', () => { nextSlide(); resetInterval(); });
    document.querySelector('.prev-arrow')?.addEventListener('click', () => {
        let newIndex = (currentSlideIndex - 1 + slidesData.length) % slidesData.length;
        updateSlide(newIndex);
        resetInterval();
    });

    createSlides();
    updateSlide(0);
    slideInterval = setInterval(nextSlide, 5000);
  }
});