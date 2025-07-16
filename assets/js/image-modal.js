/**
 * Module d'agrandissement d'images
 * Permet de cliquer sur une image pour l'afficher en grand dans un modal
 */
document.addEventListener('DOMContentLoaded', function() {
    // Créer le modal s'il n'existe pas déjà
    if (!document.getElementById('imageModal')) {
        const modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'image-modal';
        modal.innerHTML = `
            <span id="closeModal" class="modal-close">&times;</span>
            <img id="modalImage" class="modal-content">
            <div id="modalPlaceholder" class="modal-placeholder" style="display: none;"></div>
        `;
        document.body.appendChild(modal);
    }

    // Récupérer les éléments du modal
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const closeModal = document.getElementById('closeModal');
    const modalPlaceholder = document.getElementById('modalPlaceholder');

    // Fonction pour initialiser les éléments cliquables
    function initializeZoomableImages(selector) {
        const zoomableElements = document.querySelectorAll(selector);
        
        zoomableElements.forEach(function(el) {
            // Vérifier si l'élément est un logo à exclure
            if (shouldExcludeElement(el)) {
                return; // Ignorer les logos
            }
            
            // Ajouter classe pour le curseur si pas déjà présent
            if (!el.classList.contains('zoomable')) {
                el.classList.add('zoomable');
            }
            
            // Effet de survol pour les éléments qui ne sont pas des diapos Swiper
            if (!el.classList.contains('swiper-slide')) {
                el.addEventListener('mouseenter', function() {
                    const img = el.querySelector('img') || el.querySelector('div');
                    if (img) {
                        img.style.transform = 'scale(1.05)';
                    }
                });
                
                el.addEventListener('mouseleave', function() {
                    const img = el.querySelector('img') || el.querySelector('div');
                    if (img) {
                        img.style.transform = 'scale(1)';
                    }
                });
            }
            
            // Ouvrir le modal au clic
            el.addEventListener('click', function(e) {
                // Si c'est une diapo Swiper, empêcher les conflits avec la navigation
                if (el.classList.contains('swiper-slide')) {
                    // Ignorer si on a cliqué sur un bouton de navigation
                    if (e.target.closest('.swiper-button-next') || 
                        e.target.closest('.swiper-button-prev') ||
                        e.target.closest('.swiper-pagination')) {
                        return;
                    }
                }
                
                modal.style.display = 'flex';
                const imgElement = el.querySelector('img');
                
                if (imgElement) {
                    // Si c'est une vraie image
                    modalImg.src = imgElement.src;
                    modalImg.style.display = 'block';
                    modalPlaceholder.style.display = 'none';
                } else {
                    // Si c'est un placeholder
                    const placeholderDiv = el.querySelector('div');
                    if (placeholderDiv) {
                        // Crée une image de placeholder avec le contenu du div
                        const emoji = placeholderDiv.innerText;
                        modalImg.style.display = 'none';
                        modalPlaceholder.style.display = 'flex';
                        modalPlaceholder.innerText = emoji;
                    }
                }
            });
        });
    }
    
    // Fonction pour vérifier si un élément doit être exclu
    function shouldExcludeElement(element) {
        // 1. Vérifier si l'élément est dans un témoignage
        if (element.closest('.testimonial-item') || element.closest('.testimonial-card')) {
            // 2. Vérifier si c'est un logo de source ou un badge de source
            if (element.closest('.source-badge') || 
                element.closest('.source-logo') ||
                element.classList.contains('source-logo')) {
                return true;
            }
            
            // 3. Vérifier si l'image a un alt qui contient des noms de plateformes
            const img = element.querySelector('img') || element;
            if (img && img.alt) {
                const altText = img.alt.toLowerCase();
                if (altText.includes('tripadvisor') || 
                    altText.includes('google') || 
                    altText.includes('booking') ||
                    altText.includes('yelp') ||
                    altText.includes('facebook') ||
                    altText.includes('foursquare') ||
                    altText.includes('opentable') ||
                    altText.includes('lafourchette')) {
                    return true;
                }
            }
            
            // 4. Vérifier si l'image a la classe source-logo directement
            if (img && img.classList && img.classList.contains('source-logo')) {
                return true;
            }
        }
        
        // 5. Exclure les images avec la classe "no-zoom"
        if (element.classList.contains('no-zoom') || 
            (element.querySelector('img') && element.querySelector('img').classList.contains('no-zoom'))) {
            return true;
        }
        
        // 6. Exclure tous les logos de sources peu importe où ils sont
        if (element.classList.contains('source-logo') || 
            element.querySelector('.source-logo')) {
            return true;
        }
        
        return false;
    }

    // Fermer le modal
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Fermer le modal au clic en dehors de l'image
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal.click();
        }
    });

    // Initialiser les éléments zoomables existants (en excluant les témoignages)
    initializeZoomableImages('.zoomable-image:not(.testimonial-item .zoomable-image):not(.testimonial-card .zoomable-image), .swiper-slide:not(.testimonial-card .swiper-slide):not(.testimonials-swiper .swiper-slide)');

    // Exposer la fonction d'initialisation globalement
    window.initializeZoomableImages = initializeZoomableImages;
}); 