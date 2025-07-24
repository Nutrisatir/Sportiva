document.addEventListener('DOMContentLoaded', () => {

    // --- Elemen untuk Transisi View ---
    const detailView = document.getElementById('detail-view');
    const availabilityView = document.getElementById('availability-view');
    const checkAvailabilityBtn = document.getElementById('check-availability-btn');
    const backToDetailBtn = document.getElementById('back-to-detail-btn');

    if (checkAvailabilityBtn) {
        checkAvailabilityBtn.addEventListener('click', () => {
            detailView.classList.remove('is-active');
            availabilityView.classList.add('is-active');
            window.scrollTo(0, 0); // Scroll ke atas halaman
        });
    }

    if (backToDetailBtn) {
        backToDetailBtn.addEventListener('click', () => {
            availabilityView.classList.remove('is-active');
            detailView.classList.add('is-active');
            window.scrollTo(0, 0); // Scroll ke atas halaman
        });
    }

    // --- Elemen dan Logika untuk Keranjang (Cart) ---
    const timeSlotsGrid = document.querySelector('.time-slots-grid');
    const cartBody = document.querySelector('.cart-body');
    const cartEmptyMsg = document.querySelector('.cart-empty');
    
    // Fungsi untuk memperbarui tampilan keranjang
    function updateCartUI() {
        const items = cartBody.querySelectorAll('.cart-item');
        if (items.length > 0) {
            cartEmptyMsg.classList.remove('is-visible');
        } else {
            cartEmptyMsg.classList.add('is-visible');
        }
    }

    // Fungsi untuk menambahkan item ke keranjang
    function addToCart(time, price) {
        const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price);

        const cartItemHTML = `
            <div class="cart-item" data-time="${time}">
                <div class="cart-item-details">
                    <div class="time">${time}</div>
                    <div class="price">${priceFormatted}</div>
                </div>
                <button class="cart-item-remove">×</button>
            </div>
        `;
        cartBody.insertAdjacentHTML('beforeend', cartItemHTML);
        updateCartUI();
    }
    
    // Fungsi untuk menghapus item dari keranjang
    function removeFromCart(time) {
        const itemToRemove = cartBody.querySelector(`.cart-item[data-time="${time}"]`);
        if (itemToRemove) {
            itemToRemove.remove();
        }
        updateCartUI();
    }

    if (timeSlotsGrid) {
        timeSlotsGrid.addEventListener('click', (e) => {
            const slot = e.target.closest('.time-slot.available');
            if (!slot) return; // Keluar jika yang diklik bukan slot yang tersedia

            const time = slot.dataset.time;
            const price = slot.dataset.price;
            
            // Toggle class 'selected'
            const isSelected = slot.classList.toggle('selected');

            if (isSelected) {
                // Jika baru saja dipilih, tambahkan ke keranjang
                addToCart(time, price);
            } else {
                // Jika batal dipilih, hapus dari keranjang
                removeFromCart(time);
            }
        });
    }

    // Event listener untuk tombol hapus di keranjang (event delegation)
    cartBody.addEventListener('click', (e) => {
        if(e.target.classList.contains('cart-item-remove')) {
            const cartItem = e.target.closest('.cart-item');
            const time = cartItem.dataset.time;
            
            // Hapus dari UI keranjang
            removeFromCart(time);

            // Hapus juga status 'selected' dari slot waktunya
            const slotToDeselect = timeSlotsGrid.querySelector(`.time-slot[data-time="${time}"]`);
            if (slotToDeselect) {
                slotToDeselect.classList.remove('selected');
            }
        }
    });

    updateCartUI(); // Panggil saat awal untuk memastikan state benar
});