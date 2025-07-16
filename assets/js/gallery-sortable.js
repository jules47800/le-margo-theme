document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Exécuter seulement sur les écrans larges (desktop)
    if (!window.matchMedia('(min-width: 1025px)').matches) {
        return;
    }

    var grid = document.querySelector('.gallery-grid');

    if (grid) {
        new Sortable(grid, {
            animation: 150, // Vitesse de l'animation de réorganisation
            ghostClass: 'sortable-ghost', // Classe CSS pour l'élément fantôme
            chosenClass: 'sortable-chosen', // Classe CSS pour l'élément sélectionné
            dragClass: 'sortable-drag', // Classe CSS pour l'élément en cours de déplacement
            onStart: function () {
                // Rendre les liens non cliquables pendant le glissement
                grid.querySelectorAll('.gallery-image-link').forEach(function(link) {
                    link.style.pointerEvents = 'none';
                });
            },
            onEnd: function () {
                // Rétablir la cliquabilité des liens après le glissement
                setTimeout(function() {
                    grid.querySelectorAll('.gallery-image-link').forEach(function(link) {
                        link.style.pointerEvents = 'auto';
                    });
                }, 10);
            },
        });
    }
}); 