document.addEventListener('DOMContentLoaded', () => {
    const steps = document.querySelectorAll('.otp-step');
    const confirmBtn1 = document.getElementById('confirm-step1');
    const confirmBtn2 = document.getElementById('confirm-step2');
    const confirmBtn3 = document.getElementById('confirm-step3');

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === stepIndex);
        });
    }

    if (confirmBtn1) {
        confirmBtn1.addEventListener('click', () => showStep(1));
    }
    if (confirmBtn2) {
        confirmBtn2.addEventListener('click', () => showStep(2));
    }
    if (confirmBtn3) {
        confirmBtn3.addEventListener('click', () => {
            // Setelah semua alur selesai, arahkan ke halaman utama
            window.location.href = 'index.php';
        });
    }
});