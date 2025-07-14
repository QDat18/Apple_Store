// scripts/product.js
document.addEventListener('DOMContentLoaded', () => {
    // Đóng modal khi click bên ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('quickViewModal');
        if (event.target === modal) {
            closeModal();
        }
    };

    // Đóng modal khi nhấn phím Esc
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Xử lý nút carousel
    const carousel = document.getElementById('productCarousel');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    function updateCarouselButtons() {
        prevBtn.disabled = carousel.scrollLeft <= 0;
        nextBtn.disabled = carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth;
    }

    carousel.addEventListener('scroll', updateCarouselButtons);
    updateCarouselButtons();
});