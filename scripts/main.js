// Navigation Pagination
class ProductNavigation {
    constructor(prevSelector, nextSelector, pageIndicatorSelector, itemsPerPage, totalItems, containerSelector) {
        this.prevBtn = document.querySelector(prevSelector);
        this.nextBtn = document.querySelector(nextSelector);
        this.pageIndicator = document.querySelector(pageIndicatorSelector);
        this.itemsPerPage = itemsPerPage;
        this.totalItems = totalItems;
        this.currentPage = 1;
        this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
        this.container = document.querySelector(containerSelector);

        if (!this.container) {
            console.error(`Container not found for selector: ${containerSelector}`);
            return; // Exit if container is not found
        }

        this.init();
    }

    init() {
        this.updateNavigation();
        this.prevBtn.addEventListener('click', () => this.changePage(-1));
        this.nextBtn.addEventListener('click', () => this.changePage(1));
    }

    changePage(direction) {
        this.currentPage += direction;
        this.updateNavigation();
        this.loadContent();
    }

    updateNavigation() {
        this.prevBtn.disabled = this.currentPage <= 1;
        this.nextBtn.disabled = this.currentPage >= this.totalPages;
        this.pageIndicator.textContent = `${this.currentPage}/${this.totalPages}`;
    }

    loadContent() {
        // Giả định có endpoint API (cần backend hỗ trợ)
        fetch(`/api/news?page=${this.currentPage}&per_page=${this.itemsPerPage}`)
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    this.container.innerHTML = data.html; // Cập nhật HTML từ API
                }
            })
            .catch(error => console.error('Error loading content:', error));
    }
}

// Initialize all components
document.addEventListener('DOMContentLoaded', () => {
    new HeroSlider();
    new CountdownTimer();
    new ProductFilter('.flash-sale-products-container', '.flash-sale-filter button');
    new ProductFilter('.featured-products-section .product-grid', '.featured-products-filter button');
    new ProductNavigation('#prev-flash-sale', '#next-flash-sale', '.flash-sale-navigation .page-indicator', 4, 4, '.flash-sale-products-container .product-grid');
    new ProductNavigation('#prev-featured', '#next-featured', '.featured-products-navigation .page-indicator', 8, 8, '.featured-products-section .product-grid');
    new BackToTop();
    new SectionObserver();
    new TestimonialsCarousel();

    // Initialize Blog Pagination
    const newsPerPage = 10; // Set your desired value
    const totalNews = document.querySelector('#blog-grid').dataset.totalNews || 0;
    const totalPages = Math.ceil(totalNews / newsPerPage);
    if (totalPages > 1) {
        new ProductNavigation('#prev-page', '#next-page', '#page-indicator', newsPerPage, totalNews, '#blog-grid');
    }
});


// Hero Slider
class HeroSlider {
    constructor() {
        this.slides = document.querySelectorAll('.slide');
        this.indicators = document.querySelector('.slide-indicators');
        this.prevBtn = document.querySelector('.prev-slide');
        this.nextBtn = document.querySelector('.next-slide');
        this.currentSlide = 0;
        this.autoSlideInterval = null;

        this.init();
    }

    init() {
        this.slides.forEach((_, index) => {
            const indicator = document.createElement('div');
            indicator.classList.add('slide-indicator');
            if (index === 0) indicator.classList.add('active');
            indicator.addEventListener('click', () => this.goToSlide(index));
            this.indicators.appendChild(indicator);
        });

        this.prevBtn.addEventListener('click', () => this.changeSlide(-1));
        this.nextBtn.addEventListener('click', () => this.changeSlide(1));
        this.startAutoSlide();
    }

    goToSlide(index) {
        this.slides.forEach((slide, i) => {
            slide.classList.remove('active');
            this.indicators.children[i].classList.remove('active');
            if (i === index) {
                slide.classList.add('active');
                this.indicators.children[i].classList.add('active');
            }
        });
        this.currentSlide = index;
    }

    changeSlide(direction) {
        this.currentSlide = (this.currentSlide + direction + this.slides.length) % this.slides.length;
        this.goToSlide(this.currentSlide);
    }

    startAutoSlide() {
        this.autoSlideInterval = setInterval(() => {
            this.changeSlide(1);
        }, 5000);

        document.querySelector('.hero-slider').addEventListener('mouseover', () => clearInterval(this.autoSlideInterval));
        document.querySelector('.hero-slider').addEventListener('mouseout', () => {
            this.autoSlideInterval = setInterval(() => this.changeSlide(1), 5000);
        });
    }
}