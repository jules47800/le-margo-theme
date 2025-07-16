/**
 * Initialisation du Swiper pour les carousels du site
 */
document.addEventListener('DOMContentLoaded', function() {
    // Carousel de la section À Propos
    if (document.querySelector('.about-swiper')) {
        const aboutSwiper = new Swiper('.about-swiper', {
            // Configuration générale
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            grabCursor: true,
            
            // Utiliser un effet standard au lieu de fade qui peut causer des problèmes
            effect: 'slide',
            
            // Pagination
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            
            // Autoplay
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            
            // Navigation
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
        });
    }

    // Carousel des témoignages sur la page d'accueil
    if (document.querySelector('.testimonials-swiper')) {
        new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            grabCursor: true,
            effect: 'slide',
            pagination: {
                el: '.testimonials-swiper .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.testimonials-swiper .swiper-button-next',
                prevEl: '.testimonials-swiper .swiper-button-prev',
            },
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 2,
                }
            }
        });
    }
}); 