<?php
/**
 * Métaboxes pour les témoignages - Interface modernisée
 *
 * @package Le Margo
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajoute les métaboxes pour les témoignages
 */
function le_margo_add_testimonial_metaboxes() {
    add_meta_box(
        'testimonial_details',
        __('Détails du témoignage', 'le-margo'),
        'le_margo_testimonial_details_callback',
        'testimonial',
        'normal',
        'high'
    );
    
    add_meta_box(
        'testimonial_source_info',
        __('Informations de la source', 'le-margo'),
        'le_margo_testimonial_source_callback',
        'testimonial',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'le_margo_add_testimonial_metaboxes');

/**
 * Callback pour afficher le contenu principal de la métabox
 */
function le_margo_testimonial_details_callback($post) {
    // Ajout du nonce pour la sécurité
    wp_nonce_field('le_margo_testimonial_save', 'testimonial_nonce');

    // Récupération des valeurs existantes
    $rating = get_post_meta($post->ID, 'rating', true);
    $author = get_post_meta($post->ID, 'author_name', true);
    $author_location = get_post_meta($post->ID, 'author_location', true);
    $visit_date = get_post_meta($post->ID, 'visit_date', true);
    $review_date = get_post_meta($post->ID, 'review_date', true);
    $source = get_post_meta($post->ID, 'testimonial_source', true);
    $verified = get_post_meta($post->ID, 'verified_review', true);
    $helpful_count = get_post_meta($post->ID, 'helpful_count', true);
    ?>

    <div class="le-margo-testimonial-admin">
        <div class="testimonial-section testimonial-rating-section">
            <h3><?php _e('Évaluation', 'le-margo'); ?></h3>
            <div class="rating-container">
                <label class="field-label"><?php _e('Note (étoiles)', 'le-margo'); ?></label>
                <div class="rating-selector" id="rating-stars">
                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php checked($rating, $i); ?> />
                        <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> étoiles" class="star-label">★</label>
                    <?php endfor; ?>
                </div>
                <span class="rating-text"><?php echo $rating ? sprintf(__('%d/5 étoiles', 'le-margo'), $rating) : __('Aucune note', 'le-margo'); ?></span>
            </div>
        </div>

        <div class="testimonial-section testimonial-author-section">
            <h3><?php _e('Informations de l\'auteur', 'le-margo'); ?></h3>
            <div class="form-grid">
                <div class="form-field">
                    <label for="author_name" class="field-label"><?php _e('Nom de l\'auteur', 'le-margo'); ?></label>
                    <input type="text" id="author_name" name="author_name" value="<?php echo esc_attr($author); ?>" class="widefat" placeholder="Ex: Marie D." />
                </div>
                
                <div class="form-field">
                    <label for="author_location" class="field-label"><?php _e('Localisation de l\'auteur', 'le-margo'); ?></label>
                    <input type="text" id="author_location" name="author_location" value="<?php echo esc_attr($author_location); ?>" class="widefat" placeholder="Ex: Paris, France" />
                </div>
            </div>
        </div>

        <div class="testimonial-section testimonial-dates-section">
            <h3><?php _e('Dates', 'le-margo'); ?></h3>
            <div class="form-grid">
                <div class="form-field">
                    <label for="visit_date" class="field-label"><?php _e('Date de visite', 'le-margo'); ?></label>
                    <input type="date" id="visit_date" name="visit_date" value="<?php echo esc_attr($visit_date); ?>" class="widefat" />
                    <span class="field-help"><?php _e('Date à laquelle le client a visité le restaurant', 'le-margo'); ?></span>
                </div>
                
                <div class="form-field">
                    <label for="review_date" class="field-label"><?php _e('Date de l\'avis', 'le-margo'); ?></label>
                    <input type="date" id="review_date" name="review_date" value="<?php echo esc_attr($review_date); ?>" class="widefat" />
                    <span class="field-help"><?php _e('Date à laquelle l\'avis a été publié', 'le-margo'); ?></span>
                </div>
            </div>
        </div>

        <div class="testimonial-section testimonial-source-section">
            <h3><?php _e('Source et plateforme', 'le-margo'); ?></h3>
            <div class="form-field">
                <label for="testimonial_source" class="field-label"><?php _e('Plateforme d\'avis', 'le-margo'); ?></label>
                <select id="testimonial_source" name="testimonial_source" class="widefat source-selector">
                    <option value=""><?php _e('Sélectionnez une plateforme', 'le-margo'); ?></option>
                    <option value="google" <?php selected($source, 'google'); ?> data-logo="google.png"><?php _e('Google Reviews', 'le-margo'); ?></option>
                    <option value="tripadvisor" <?php selected($source, 'tripadvisor'); ?> data-logo="tripadvisor.png"><?php _e('TripAdvisor', 'le-margo'); ?></option>
                    <option value="booking" <?php selected($source, 'booking'); ?> data-logo="booking.png"><?php _e('Booking.com', 'le-margo'); ?></option>
                    <option value="yelp" <?php selected($source, 'yelp'); ?> data-logo="yelp.png"><?php _e('Yelp', 'le-margo'); ?></option>
                    <option value="facebook" <?php selected($source, 'facebook'); ?> data-logo="facebook.png"><?php _e('Facebook', 'le-margo'); ?></option>
                    <option value="foursquare" <?php selected($source, 'foursquare'); ?> data-logo="foursquare.png"><?php _e('Foursquare', 'le-margo'); ?></option>
                    <option value="opentable" <?php selected($source, 'opentable'); ?> data-logo="opentable.png"><?php _e('OpenTable', 'le-margo'); ?></option>
                    <option value="lafourchette" <?php selected($source, 'lafourchette'); ?> data-logo="lafourchette.png"><?php _e('LaFourchette', 'le-margo'); ?></option>
                    <option value="direct" <?php selected($source, 'direct'); ?>><?php _e('Direct / Livre d\'or', 'le-margo'); ?></option>
                    <option value="autre" <?php selected($source, 'autre'); ?>><?php _e('Autre', 'le-margo'); ?></option>
                </select>
                <div id="source-preview" class="source-preview">
                    <?php if ($source && $source !== 'direct' && $source !== 'autre') : ?>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/sources/' . $source . '.png'); ?>" alt="<?php echo esc_attr($source); ?>" class="source-logo-preview">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="testimonial-section testimonial-meta-section">
            <h3><?php _e('Informations supplémentaires', 'le-margo'); ?></h3>
            <div class="form-grid">
                <div class="form-field">
                    <label class="checkbox-container">
                        <input type="checkbox" name="verified_review" value="1" <?php checked($verified, '1'); ?>>
                        <span class="checkmark"></span>
                        <?php _e('Avis vérifié', 'le-margo'); ?>
                    </label>
                    <span class="field-help"><?php _e('Cochez si l\'avis provient d\'un client vérifié', 'le-margo'); ?></span>
                </div>
                
                <div class="form-field">
                    <label for="helpful_count" class="field-label"><?php _e('Nombre de "utile"', 'le-margo'); ?></label>
                    <input type="number" id="helpful_count" name="helpful_count" value="<?php echo esc_attr($helpful_count); ?>" class="small-text" min="0" placeholder="0" />
                    <span class="field-help"><?php _e('Nombre de personnes qui ont trouvé cet avis utile', 'le-margo'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <style>
    .le-margo-testimonial-admin {
        background: #fff;
        border-radius: 8px;
        padding: 0;
    }

    .testimonial-section {
        padding: 20px;
        border-bottom: 1px solid #e2e4e7;
    }

    .testimonial-section:last-child {
        border-bottom: none;
    }

    .testimonial-section h3 {
        margin: 0 0 15px 0;
        font-size: 16px;
        font-weight: 600;
        color: #23282d;
        border-bottom: 2px solid #0073aa;
        padding-bottom: 8px;
    }

    .field-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #23282d;
        font-size: 14px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-field {
        margin-bottom: 20px;
    }

    .form-field:last-child {
        margin-bottom: 0;
    }

    .field-help {
        display: block;
        font-size: 12px;
        color: #666;
        margin-top: 5px;
        font-style: italic;
    }

    /* Styles pour la notation par étoiles */
    .rating-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #e2e4e7;
    }

    .rating-selector {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        margin-bottom: 10px;
    }

    .rating-selector input {
        display: none;
    }

    .star-label {
        cursor: pointer;
        font-size: 28px;
        color: #ddd;
        margin-right: 8px;
        transition: all 0.2s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .star-label:hover,
    .star-label:hover ~ .star-label,
    .rating-selector input:checked ~ .star-label {
        color: #ffb900;
        text-shadow: 0 2px 4px rgba(255,185,0,0.3);
    }

    .rating-text {
        font-weight: 600;
        color: #0073aa;
        font-size: 14px;
    }

    /* Styles pour le sélecteur de source */
    .source-selector {
        background: #fff;
        border: 1px solid #ddd;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 14px;
    }

    .source-preview {
        margin-top: 10px;
        text-align: center;
    }

    .source-logo-preview {
        max-width: 100px;
        max-height: 40px;
        width: auto;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Styles pour les checkboxes personnalisées */
    .checkbox-container {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    .checkbox-container input[type="checkbox"] {
        display: none;
    }

    .checkmark {
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 2px solid #ddd;
        border-radius: 4px;
        margin-right: 10px;
        position: relative;
        transition: all 0.2s ease;
    }

    .checkbox-container:hover .checkmark {
        border-color: #0073aa;
    }

    .checkbox-container input:checked ~ .checkmark {
        background-color: #0073aa;
        border-color: #0073aa;
    }

    .checkbox-container input:checked ~ .checkmark:after {
        content: "";
        position: absolute;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .testimonial-section {
            padding: 15px;
        }
        
        .star-label {
            font-size: 24px;
            margin-right: 5px;
        }
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Mise à jour du texte de notation
        function updateRatingText() {
            const selectedRating = $('.rating-selector input:checked').val();
            const ratingText = selectedRating ? 
                '<?php echo esc_js(__('%d/5 étoiles', 'le-margo')); ?>'.replace('%d', selectedRating) : 
                '<?php echo esc_js(__('Aucune note', 'le-margo')); ?>';
            $('.rating-text').text(ratingText);
        }

        // Gestion des étoiles
        $('.rating-selector input').on('change', updateRatingText);

        // Prévisualisation du logo de la source
        $('#testimonial_source').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const logo = selectedOption.data('logo');
            const sourcePreview = $('#source-preview');
            
            if (logo && $(this).val() !== 'direct' && $(this).val() !== 'autre') {
                const logoUrl = '<?php echo esc_url(get_template_directory_uri() . '/assets/images/sources/'); ?>' + logo;
                sourcePreview.html('<img src="' + logoUrl + '" alt="' + $(this).val() + '" class="source-logo-preview">');
            } else {
                sourcePreview.empty();
            }
        });

        // Auto-remplissage de la date d'avis avec la date actuelle si vide
        $('#review_date').on('focus', function() {
            if (!$(this).val()) {
                const today = new Date().toISOString().split('T')[0];
                $(this).val(today);
            }
        });

        // Validation du formulaire
        $('form').on('submit', function() {
            const rating = $('.rating-selector input:checked').val();
            const author = $('#author_name').val();
            const source = $('#testimonial_source').val();
            
            if (!rating) {
                alert('<?php echo esc_js(__('Veuillez sélectionner une note.', 'le-margo')); ?>');
                return false;
            }
            
            if (!author.trim()) {
                alert('<?php echo esc_js(__('Veuillez saisir le nom de l\'auteur.', 'le-margo')); ?>');
                $('#author_name').focus();
                return false;
            }
            
            if (!source) {
                alert('<?php echo esc_js(__('Veuillez sélectionner une plateforme.', 'le-margo')); ?>');
                $('#testimonial_source').focus();
                return false;
            }
        });
    });
    </script>
    <?php
}

/**
 * Callback pour la métabox des informations de source
 */
function le_margo_testimonial_source_callback($post) {
    $source_url = get_post_meta($post->ID, 'source_url', true);
    $review_id = get_post_meta($post->ID, 'review_id', true);
    $language = get_post_meta($post->ID, 'review_language', true);
    $featured = get_post_meta($post->ID, 'featured_review', true);
    ?>
    
    <div class="le-margo-source-info">
        <div class="form-field">
            <label for="source_url" class="field-label"><?php _e('Lien vers l\'avis original', 'le-margo'); ?></label>
            <input type="url" id="source_url" name="source_url" value="<?php echo esc_attr($source_url); ?>" class="widefat" placeholder="https://..." />
            <span class="field-help"><?php _e('URL de l\'avis sur la plateforme d\'origine', 'le-margo'); ?></span>
        </div>
        
        <div class="form-field">
            <label for="review_id" class="field-label"><?php _e('ID de l\'avis', 'le-margo'); ?></label>
            <input type="text" id="review_id" name="review_id" value="<?php echo esc_attr($review_id); ?>" class="widefat" placeholder="Ex: rev_123456" />
            <span class="field-help"><?php _e('Identifiant unique de l\'avis sur la plateforme', 'le-margo'); ?></span>
        </div>
        
        <div class="form-field">
            <label for="review_language" class="field-label"><?php _e('Langue de l\'avis', 'le-margo'); ?></label>
            <select id="review_language" name="review_language" class="widefat">
                <option value="fr" <?php selected($language, 'fr'); ?>><?php _e('Français', 'le-margo'); ?></option>
                <option value="en" <?php selected($language, 'en'); ?>><?php _e('Anglais', 'le-margo'); ?></option>
                <option value="es" <?php selected($language, 'es'); ?>><?php _e('Espagnol', 'le-margo'); ?></option>
                <option value="de" <?php selected($language, 'de'); ?>><?php _e('Allemand', 'le-margo'); ?></option>
                <option value="it" <?php selected($language, 'it'); ?>><?php _e('Italien', 'le-margo'); ?></option>
                <option value="nl" <?php selected($language, 'nl'); ?>><?php _e('Néerlandais', 'le-margo'); ?></option>
                <option value="pt" <?php selected($language, 'pt'); ?>><?php _e('Portugais', 'le-margo'); ?></option>
            </select>
        </div>
        
        <div class="form-field">
            <label class="checkbox-container">
                <input type="checkbox" name="featured_review" value="1" <?php checked($featured, '1'); ?>>
                <span class="checkmark"></span>
                <?php _e('Avis en vedette', 'le-margo'); ?>
            </label>
            <span class="field-help"><?php _e('Mettre en avant cet avis', 'le-margo'); ?></span>
        </div>
        
        <div class="source-info-actions">
            <?php if ($source_url) : ?>
                <a href="<?php echo esc_url($source_url); ?>" target="_blank" class="button button-secondary">
                    <span class="dashicons dashicons-external"></span>
                    <?php _e('Voir l\'avis original', 'le-margo'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .le-margo-source-info .form-field {
        margin-bottom: 15px;
    }
    
    .source-info-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e2e4e7;
    }
    
    .source-info-actions .button {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    </style>
    <?php
}

/**
 * Sauvegarde des métadonnées
 */
function le_margo_save_testimonial_meta($post_id) {
    // Vérifications de sécurité
    if (!isset($_POST['testimonial_nonce']) || !wp_verify_nonce($_POST['testimonial_nonce'], 'le_margo_testimonial_save')) {
        return;
    }
    
    // Ne pas sauvegarder lors d'une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier les permissions
    if ('testimonial' === $_POST['post_type'] && !current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Liste des champs à sauvegarder
    $fields = array(
        'rating' => 'sanitize_text_field',
        'author_name' => 'sanitize_text_field',
        'author_location' => 'sanitize_text_field',
        'visit_date' => 'sanitize_text_field',
        'review_date' => 'sanitize_text_field',
        'testimonial_source' => 'sanitize_text_field',
        'source_url' => 'esc_url_raw',
        'review_id' => 'sanitize_text_field',
        'review_language' => 'sanitize_text_field',
        'helpful_count' => 'intval'
    );
    
    // Sauvegarder les champs texte
    foreach ($fields as $field => $sanitizer) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, call_user_func($sanitizer, $_POST[$field]));
        }
    }
    
    // Sauvegarder les checkboxes
    $checkboxes = array('verified_review', 'featured_review');
    foreach ($checkboxes as $checkbox) {
        $value = isset($_POST[$checkbox]) && $_POST[$checkbox] === '1' ? '1' : '0';
        update_post_meta($post_id, $checkbox, $value);
    }
}
add_action('save_post_testimonial', 'le_margo_save_testimonial_meta'); 