/**
 * Effet de zoom interactif pour la galerie photo
 * L'image suit la position du curseur pour créer un effet d'exploration
 */

document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(item => {
        const img = item.querySelector('img');
        
        if (!img) return; // Skip si pas d'image (cartes noires)
        
        item.addEventListener('mouseenter', function() {
            // Activer l'effet de zoom
            img.style.transform = 'scale(1.2)';
        });
        
        item.addEventListener('mousemove', function(e) {
            const rect = item.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Calculer la position relative (0 à 1)
            const xPercent = x / rect.width;
            const yPercent = y / rect.height;
            
            // Convertir en translation pour l'effet de déplacement
            // Plus l'image est zoomée, plus elle peut se déplacer
            const moveX = (xPercent - 0.5) * 20; // 20px de déplacement max
            const moveY = (yPercent - 0.5) * 20;
            
            // Appliquer la transformation
            img.style.transform = `scale(1.2) translate(${moveX}px, ${moveY}px)`;
        });
        
        item.addEventListener('mouseleave', function() {
            // Retour à l'état normal
            img.style.transform = 'scale(1) translate(0, 0)';
        });
    });
}); 