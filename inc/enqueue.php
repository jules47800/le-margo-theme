<?php
/**
 * Le Margo - Fonctions pour la mise en file des scripts et styles
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Configuration des scripts et styles
 */
function le_margo_enqueue_assets($hook = '') {
    // Styles communs
    wp_enqueue_style('le-margo-style', get_stylesheet_uri(), array(), LE_MARGO_VERSION);
    wp_enqueue_style('le-margo-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
    wp_enqueue_style('le-margo-main', get_template_directory_uri() . '/assets/css/main.css', array('le-margo-google-fonts'), LE_MARGO_VERSION);
    wp_enqueue_style('le-margo-animations', get_template_directory_uri() . '/assets/css/animations.css', array(), LE_MARGO_VERSION);
    wp_enqueue_style('le-margo-checkboxes', get_template_directory_uri() . '/assets/css/custom-checkboxes.css', array(), LE_MARGO_VERSION);

    // Scripts communs
    wp_enqueue_script('le-margo-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), LE_MARGO_VERSION, true);
    wp_enqueue_script('le-margo-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), LE_MARGO_VERSION, true);

    // Scripts spécifiques à l'administration
    if (is_admin()) {
        wp_enqueue_media();
        wp_enqueue_style('le-margo-admin-css', get_template_directory_uri() . '/assets/css/admin.css', array(), LE_MARGO_VERSION);
        wp_enqueue_script('le-margo-admin', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), LE_MARGO_VERSION, true);
        wp_localize_script('le-margo-admin', 'le_margo_admin', array(
            'nonce' => wp_create_nonce('le_margo_upload_pdf'),
            'pdf_only_message' => __('Seuls les fichiers PDF sont autorisés.', 'le-margo')
        ));

        // Scripts pour les pages de réservation (calendrier et logique)
        $reservation_pages = [
            'toplevel_page_le-margo-reservations',
            'reservations_page_le-margo-reservation-settings'
        ];
        if (in_array($hook, $reservation_pages)) {
            wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
            wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.9', true);
            wp_enqueue_script('flatpickr-fr', 'https://npmcdn.com/flatpickr/dist/l10n/fr.js', ['flatpickr'], '4.6.9', true);

            wp_enqueue_script('le-margo-admin-reservations', get_template_directory_uri() . '/assets/js/admin-reservations.js', ['jquery', 'flatpickr'], LE_MARGO_VERSION, true);

            // Localisation des paramètres et du nonce pour les actions d'édition
            wp_localize_script('le-margo-admin-reservations', 'le_margo_res_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('le_margo_reservation_edit'),
                'i18n' => array(
                    'editTitle' => __('Modifier la réservation', 'le-margo'),
                    'save' => __('Enregistrer', 'le-margo'),
                    'cancel' => __('Annuler', 'le-margo'),
                    'updated' => __('Réservation mise à jour.', 'le-margo'),
                    'error' => __('Erreur lors de la mise à jour.', 'le-margo')
                )
            ));
        }
    }

    // Scripts spécifiques à la page de réservation
    if (is_page('reserver')) {
        wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.9');
        wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.9', true);
        wp_enqueue_script('flatpickr-fr', 'https://npmcdn.com/flatpickr/dist/l10n/fr.js', array('flatpickr'), '4.6.9', true);
        wp_enqueue_style('le-margo-reservation', get_template_directory_uri() . '/assets/css/reservation.css', array(), '1.0.0');
        wp_enqueue_script('le-margo-reservation', get_template_directory_uri() . '/assets/js/reservation.js', array('jquery'), '1.0.1', true);

        // CORRECTION : Centralisation de la configuration des scripts de réservation
        wp_localize_script('le-margo-reservation', 'le_margo_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'restaurant_capacity' => intval(get_option('le_margo_restaurant_capacity', 4)),
            'daily_schedule' => get_option('le_margo_daily_schedule', array()),
            'holiday_dates' => get_option('le_margo_holiday_dates', ''), // Ajout des dates de vacances
            'version' => '2.1.0',
            'restaurant_phone' => get_theme_mod('le_margo_restaurant_phone', '05 53 00 00 00')
        ));
    }

    // Swiper JS pour les carousels
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.5');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.5', true);
    wp_enqueue_script('le-margo-swiper-init', get_template_directory_uri() . '/assets/js/swiper-init.js', array('swiper-js'), LE_MARGO_VERSION, true);
    wp_enqueue_script('le-margo-image-modal', get_template_directory_uri() . '/assets/js/image-modal.js', array('jquery'), LE_MARGO_VERSION, true);

    // Lightbox pour la galerie de la page d'accueil
    if (is_front_page()) {
        wp_enqueue_script('le-margo-gallery-lightbox', get_template_directory_uri() . '/assets/js/gallery-lightbox.js', array('jquery'), LE_MARGO_VERSION, true);

        // Ajout de SortableJS pour le glisser-déposer
        wp_enqueue_script('sortable-js', 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js', array(), null, true);
        wp_enqueue_script('le-margo-gallery-sortable', get_template_directory_uri() . '/assets/js/gallery-sortable.js', array('sortable-js'), LE_MARGO_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'le_margo_enqueue_assets');
add_action('admin_enqueue_scripts', 'le_margo_enqueue_assets');

/**
 * Charger dynamiquement la police Google Font choisie dans le Customizer.
 */
function le_margo_enqueue_custom_fonts() {
    $primary_font = get_theme_mod('le_margo_primary_font', 'Inter');
    
    if ($primary_font) {
        $font_families = array(
            'Inter' => 'Inter:wght@300;400;500',
            'Poppins' => 'Poppins:wght@300;400;500',
            'Lato' => 'Lato:wght@300;400;700',
            'Lora' => 'Lora:wght@400;500;600',
            'Cormorant Garamond' => 'Cormorant+Garamond:wght@400;500;600',
            'Playfair Display' => 'Playfair+Display:wght@400;500;600',
        );

        if (isset($font_families[$primary_font])) {
            $font_query = $font_families[$primary_font];
            $fonts_url = 'https://fonts.googleapis.com/css2?family=' . $font_query . '&display=swap';
            wp_enqueue_style('le-margo-custom-font', $fonts_url, array(), null);
        }
    }
}
add_action('wp_enqueue_scripts', 'le_margo_enqueue_custom_fonts'); 