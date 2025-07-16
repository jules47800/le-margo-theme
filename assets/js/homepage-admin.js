jQuery(document).ready(function($) {
    'use strict';

    var mediaFrame;
    var galleryDataInput = $('#gallery_images_data');
    var previewContainer = $('#gallery-preview-container');

    // Initialiser le tri
    previewContainer.sortable({
        items: '.gallery-item-preview',
        cursor: 'move',
        update: function(event, ui) {
            updateGalleryData();
        }
    });

    // Bouton "Ajouter des images"
    $('#add-gallery-images').on('click', function(e) {
        e.preventDefault();

        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: 'Sélectionner ou téléverser des images pour la galerie',
            button: {
                text: 'Utiliser ces images'
            },
            multiple: 'add'
        });

        mediaFrame.on('select', function() {
            var selection = mediaFrame.state().get('selection');
            var shapes = ['normal', 'tall', 'wide'];

            selection.each(function(attachment) {
                var image = attachment.toJSON();
                // Choisir une forme au hasard
                var randomShape = shapes[Math.floor(Math.random() * shapes.length)];
                appendImageToPreview(image.id, image.sizes.thumbnail.url, randomShape);
            });
            updateGalleryData();
        });

        mediaFrame.open();
    });

    // Supprimer une image
    previewContainer.on('click', '.remove-image', function(e) {
        e.preventDefault();
        if (window.confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
            $(this).closest('.gallery-item-preview').remove();
            updateGalleryData();
        }
    });
    
    // Changer la forme de l'image
    previewContainer.on('change', '.image-shape', function() {
        updateGalleryData();
    });

    // Fonction pour ajouter une image à la prévisualisation
    function appendImageToPreview(id, url, shape) {
        var previewHtml = `
            <div class="gallery-item-preview" data-id="${id}">
                <img src="${url}" />
                <div class="item-controls">
                    <select class="image-shape">
                        <option value="normal" ${shape === 'normal' ? 'selected' : ''}>Normale</option>
                        <option value="tall" ${shape === 'tall' ? 'selected' : ''}>Haute</option>
                        <option value="wide" ${shape === 'wide' ? 'selected' : ''}>Large</option>
                    </select>
                    <button type="button" class="button remove-image">✕</button>
                </div>
            </div>`;
        previewContainer.append(previewHtml);
    }

    // Mettre à jour le champ caché avec les données de la galerie
    function updateGalleryData() {
        var galleryData = [];
        previewContainer.find('.gallery-item-preview').each(function() {
            galleryData.push({
                id: $(this).data('id'),
                shape: $(this).find('.image-shape').val()
            });
        });
        galleryDataInput.val(JSON.stringify(galleryData));
    }
}); 