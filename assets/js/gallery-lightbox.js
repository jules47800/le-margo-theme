jQuery(document).ready(function($) {
    'use strict';

    // Sélection des éléments
    var modal = $('#image-modal');
    var modalImg = $('#modal-image-content');
    var captionText = $('#modal-caption');
    var closeBtn = $('.close-modal-btn');

    // Quand on clique sur une image de la galerie
    $('.gallery-image-link').on('click', function(e) {
        e.preventDefault(); // Empêcher le comportement par défaut du lien

        var imgSrc = $(this).data('full-src');
        var imgAlt = $(this).data('alt');

        modal.show();
        modalImg.attr('src', imgSrc);
        captionText.text(imgAlt);
    });

    // Fonction pour fermer le modal
    function closeModal() {
        modal.hide();
    }

    // Clic sur le bouton de fermeture
    closeBtn.on('click', closeModal);

    // Clic en dehors de l'image pour fermer
    modal.on('click', function(e) {
        if ($(e.target).is(modal)) {
            closeModal();
        }
    });

    // Fermeture avec la touche "Echap"
    $(document).on('keyup', function(e) {
        if (e.key === "Escape" && modal.is(':visible')) {
            closeModal();
        }
    });
}); 