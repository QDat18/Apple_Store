document.addEventListener('DOMContentLoaded', () => {
    // === Loading Indicator Logic ===
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) {
        window.addEventListener('load', () => {
            loadingIndicator.style.display = 'none';
        });
    }

    // === Hero Slider Logic ===
    const slidesContainer = document.querySelector('.slides-container');
    const slides = document.querySelectorAll('.hero-slider .slide');
    const prevSlideBtn = document.querySelector('.prev-slide');
    const nextSlideBtn = document.querySelector('.next-slide');
    const slideIndicatorsContainer = document.querySelector('.slide-indicators');
    let currentSlideIndex = 0;
    let slideInterval;

    if (slides.length > 0) {
        // Create indicators
        slides.forEach((_, index) => {
            const indicator = document.createElement('div');
            indicator.classList.add('indicator');
            if (index === 0) {
                indicator.classList.add('active');
            }
            indicator.dataset.slideIndex = index;
            slideIndicatorsContainer.appendChild(indicator);
        });

        const indicators = document.querySelectorAll('.slide-indicators .indicator');

        const showSlide = (index) => {
            if (index >= slides.length) {
                currentSlideIndex = 0;
            } else if (index < 0) {
                currentSlideIndex = slides.length - 1;
            } else {
                currentSlideIndex = index;
            }

            slidesContainer.style.transform = `translateX(-${currentSlideIndex * 100}%)`;

            indicators.forEach(indicator => indicator.classList.remove('active'));
            indicators[currentSlideIndex].classList.add('active');
        };

        const nextSlide = () => {
            showSlide(currentSlideIndex + 1);
        };

        const prevSlide = () => {
            showSlide(currentSlideIndex - 1);
        };

        nextSlideBtn.addEventListener('click', nextSlide);
        prevSlideBtn.addEventListener('click', prevSlide);

        indicators.forEach(indicator => {
            indicator.addEventListener('click', (e) => {
                showSlide(parseInt(e.target.dataset.slideIndex));
                resetSlideInterval();
            });
        });

        const startSlideInterval = () => {
            slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        };

        const resetSlideInterval = () => {
            clearInterval(slideInterval);
            startSlideInterval();
        };

        startSlideInterval(); // Start the automatic slideshow
    }

    // === Flash Sale Countdown Logic ===
    const countdownElement = document.getElementById('countdown');
    if (countdownElement) {
        const flashSaleDuration = parseInt(countdownElement.dataset.duration); // Duration in seconds
        let endTime = localStorage.getItem('flashSaleEndTime');

        if (!endTime) {
            endTime = Date.now() + flashSaleDuration * 1000;
            localStorage.setItem('flashSaleEndTime', endTime);
        } else {
            endTime = parseInt(endTime);
        }

        const updateCountdown = () => {
            const now = Date.now();
            const timeLeft = endTime - now;

            if (timeLeft <= 0) {
                countdownElement.textContent = 'Sale Ended!';
                clearInterval(countdownInterval);
                localStorage.removeItem('flashSaleEndTime');
                // Optionally, hide flash sale section or disable products
                const flashSaleSection = document.getElementById('flash-sale');
                if (flashSaleSection) {
                    flashSaleSection.classList.add('sale-ended');
                    flashSaleSection.querySelector('.product-grid').innerHTML = `
                        <div class="no-products">
                            <p>This flash sale has ended. Stay tuned for the next one!</p>
                        </div>
                    `;
                    flashSaleSection.querySelectorAll('.btn-add-to-cart').forEach(btn => btn.disabled = true);
                }
                return;
            }

            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            countdownElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        };

        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call to display immediately
    }


    // === Product Filtering Logic (Flash Sale & Featured Products) ===
    const setupProductFiltering = (sectionId) => {
        const section = document.getElementById(sectionId);
        if (!section) return;

        const filterButtons = section.querySelectorAll('.filter-buttons button');
        const productCards = section.querySelectorAll('.product-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                const filter = button.dataset.filter;

                productCards.forEach(card => {
                    const productCategory = card.dataset.category;
                    if (filter === 'all' || productCategory === filter) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
                // After filtering, reset pagination if applicable
                if (section.dataset.paginationActive === 'true') {
                    // This assumes pagination is managed separately and needs a reset
                    // For simplicity, we'll just show all filtered items if no pagination,
                    // otherwise, pagination script should handle visibility.
                }
            });
        });
    };

    setupProductFiltering('flash-sale');
    setupProductFiltering('products');


    // === Product Pagination Logic (Flash Sale, Featured Products, Blog) ===
    const setupPagination = (sectionSelector, itemsSelector, itemsPerPage) => {
        const section = document.querySelector(sectionSelector);
        if (!section) return;

        const items = Array.from(section.querySelectorAll(itemsSelector));
        if (items.length <= itemsPerPage) {
            // Hide pagination if not enough items
            const paginationContainer = section.querySelector('.pagination');
            if (paginationContainer) {
                paginationContainer.style.display = 'none';
            }
            return;
        }

        section.dataset.paginationActive = 'true'; // Mark section as having active pagination

        let currentPage = 1;
        const totalPages = Math.ceil(items.length / itemsPerPage);

        const prevBtn = section.querySelector('.prev-next-btn:first-of-type');
        const nextBtn = section.querySelector('.prev-next-btn:last-of-type');
        const pageIndicator = section.querySelector('.page-indicator');

        const displayPage = () => {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            items.forEach((item, index) => {
                if (index >= start && index < end) {
                    item.style.display = 'flex'; // Use flex for product/blog cards
                } else {
                    item.style.display = 'none';
                }
            });

            if (pageIndicator) {
                pageIndicator.textContent = `${currentPage}/${totalPages}`;
            }

            if (prevBtn) prevBtn.disabled = currentPage === 1;
            if (nextBtn) nextBtn.disabled = currentPage === totalPages;
        };

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayPage();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayPage();
                }
            });
        }

        displayPage(); // Initial display
    };

    // Apply pagination to sections
    setupPagination('#flash-sale', '.product-card', 4); // 4 products per page for flash sale
    setupPagination('#products', '.product-card', 8); // 8 products per page for featured products
    setupPagination('#blog', '.blog-post', 4); // 4 posts per page for blog


    // === Testimonial Carousel Logic ===
    const testimonialCarousel = document.querySelector('.testimonial-carousel');
    if (testimonialCarousel) {
        const carouselInner = testimonialCarousel.querySelector('.carousel-inner');
        const testimonialCards = testimonialCarousel.querySelectorAll('.testimonial-card');
        const carouselPrevBtn = testimonialCarousel.querySelector('.carousel-prev');
        const carouselNextBtn = testimonialCarousel.querySelector('.carousel-next');

        let currentTestimonialIndex = 0;

        const showTestimonial = (index) => {
            if (index >= testimonialCards.length) {
                currentTestimonialIndex = 0;
            } else if (index < 0) {
                currentTestimonialIndex = testimonialCards.length - 1;
            } else {
                currentTestimonialIndex = index;
            }
            carouselInner.style.transform = `translateX(-${currentTestimonialIndex * 100}%)`;
        };

        carouselNextBtn.addEventListener('click', () => showTestimonial(currentTestimonialIndex + 1));
        carouselPrevBtn.addEventListener('click', () => showTestimonial(currentTestimonialIndex - 1));

        // Auto-play testimonials
        setInterval(() => showTestimonial(currentTestimonialIndex + 1), 7000); // Change testimonial every 7 seconds

        showTestimonial(0); // Initial display
    }

    // === Back to Top Button ===
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        const toggleVisibility = () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        };

        window.addEventListener('scroll', toggleVisibility);
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        toggleVisibility(); // Initial check
    }

    // === Add to Cart Button (Client-side interactivity) ===
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', (event) => {
            const productId = event.target.dataset.productId;
            const productCard = event.target.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            const productPrice = productCard.querySelector('.price').textContent;
            const productImage = productCard.querySelector('.product-image img').src;
            const stockQuantity = parseInt(productCard.dataset.stock);

            const storageSelect = productCard.querySelector('.storage-select');
            const selectedStorage = storageSelect ? storageSelect.value : null;

            const colorSelect = productCard.querySelector('.color-select');
            const selectedColor = colorSelect ? colorSelect.value : null;

            if (stockQuantity === 0) {
                showToast('This item is out of stock!', 'error');
                event.target.disabled = true;
                event.target.textContent = 'Out of Stock';
                return;
            }

            // Basic client-side validation for options
            if (storageSelect && !selectedStorage) {
                showToast('Please select a storage option!', 'warning');
                return;
            }
            if (colorSelect && !selectedColor) {
                showToast('Please select a color option!', 'warning');
                return;
            }

            // Simulate adding to cart (in a real app, this would be an AJAX call)
            console.log(`Adding to cart: Product ID ${productId}, Name: ${productName}, Price: ${productPrice}, Storage: ${selectedStorage}, Color: ${selectedColor}`);
            showToast(`${productName} added to cart!`, 'success');

            // Example: Update cart count in header (assuming a global cart counter)
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                let currentCount = parseInt(cartCountElement.textContent);
                cartCountElement.textContent = currentCount + 1;
            }

            // In a real scenario, you'd also send this to a backend
            // fetch('/api/cart/add', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ productId, selectedStorage, selectedColor, quantity: 1 })
            // })
            // .then(response => response.json())
            // .then(data => {
            //     if (data.success) {
            //         showToast('Product added to cart!', 'success');
            //         // Update UI, e.g., cart count
            //     } else {
            //         showToast(data.message || 'Failed to add to cart.', 'error');
            //     }
            // })
            // .catch(error => {
            //     console.error('Error adding to cart:', error);
            //     showToast('An error occurred. Please try again.', 'error');
            // });
        });
    });

    // === Toast Notification Function ===
    function showToast(message, type = 'info', duration = 3000) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            console.warn('Toast container not found!');
            return;
        }

        const toast = document.createElement('div');
        toast.classList.add('toast', type);
        toast.textContent = message;

        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.add('show');
        }, 10); // Small delay to allow CSS transition

        // Animate out and remove
        setTimeout(() => {
            toast.classList.remove('show');
            toast.classList.add('hide'); // Add hide class for fade-out
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }, duration);
    }

    // === Contact Form Submission ===
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(contactForm);
            const data = Object.fromEntries(formData.entries());

            // Simulate form submission (in a real app, this would be an AJAX call)
            console.log('Contact form submitted:', data);

            // Here you would typically send data to your backend contact.php
            fetch('contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' // or 'application/x-www-form-urlencoded' if not using FormData directly
                },
                body: JSON.stringify(data) // or new URLSearchParams(formData).toString()
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showToast('Your message has been sent successfully!', 'success');
                    contactForm.reset();
                } else {
                    showToast(result.message || 'Failed to send message. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error submitting contact form:', error);
                showToast('An error occurred. Please try again later.', 'error');
            });

            // For demonstration, just show a success toast and reset
            showToast('Your message has been sent successfully!', 'success');
            contactForm.reset();
        });
    }

});