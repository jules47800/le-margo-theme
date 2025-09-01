<?php
/**
 * Le Margo - Fonctions et définitions du thème
 */

// Vider le cache OPcode si disponible
if (function_exists('opcache_reset')) {
    opcache_reset();
}

if (!defined('_LE_MARGO_VERSION')) {
    // Remplacer le numéro de version à chaque mise à jour
    define('_LE_MARGO_VERSION', '1.0.0');
}

// Définir la constante sans underscore pour la compatibilité
if (!defined('LE_MARGO_VERSION')) {
    define('LE_MARGO_VERSION', _LE_MARGO_VERSION);
}

/**
 * Chargement des fichiers du thème
 * ----------------------------------------------------------------
 */
require_once get_template_directory() . '/inc/core-setup.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/seo-meta.php';
require_once get_template_directory() . '/inc/seo-auto-config.php';
require_once get_template_directory() . '/inc/apply-seo-now.php'; // Script temporaire
require_once get_template_directory() . '/inc/redirect-old-pages.php';

/**
 * Inclure les fichiers nécessaires
 */
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/class-le-margo-utils.php';
require_once get_template_directory() . '/inc/class-le-margo-email-manager.php';
require_once get_template_directory() . '/inc/class-le-margo-reservation-manager.php';
require_once get_template_directory() . '/inc/reservations-admin.php';
require_once get_template_directory() . '/inc/dashboard-widgets.php';
require_once get_template_directory() . '/inc/reservations-notifications.php';
require_once get_template_directory() . '/inc/customer-stats.php';
require_once get_template_directory() . '/inc/dashboard-customer-widget.php';
require_once get_template_directory() . '/inc/customer-admin-page.php';
require_once get_template_directory() . '/inc/advanced-stats-page.php';
require_once get_template_directory() . '/inc/schema-markup.php';
require_once get_template_directory() . '/inc/menu-admin.php';
require_once get_template_directory() . '/inc/testimonial-metaboxes.php';
require_once get_template_directory() . '/inc/reservations-core.php';
require_once get_template_directory() . '/inc/opening-hours-admin.php';
require_once get_template_directory() . '/inc/translation-setup.php';

/**
 * Enregistrement et traitement des réservations
 */

function le_margo_reservation_scripts() {
    // Ne charger les scripts que sur la page de réservation
    if (is_page('reserver')) {
        // Flatpickr pour le sélecteur de date
        wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.9');
        wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.9', true);
        
        // Nos styles et scripts personnalisés
        wp_enqueue_style('le-margo-reservation', get_template_directory_uri() . '/assets/css/reservation.css', array(), '1.0.1');
        wp_enqueue_script('le-margo-reservation', get_template_directory_uri() . '/assets/js/reservation.js', array('jquery', 'flatpickr'), '1.0.2', true);

        // Déterminer la locale actuelle (ex: 'fr', 'en')
        $current_locale_full = le_margo_get_current_language(); // ex: fr_FR
        $locale_short = substr($current_locale_full, 0, 2); // ex: fr

        // Charger le fichier de langue de Flatpickr dynamiquement
        if ($locale_short !== 'en') { // 'en' est par défaut, pas besoin de le charger
            wp_enqueue_script('flatpickr-l10n', "https://npmcdn.com/flatpickr/dist/l10n/{$locale_short}.js", array('flatpickr'), '4.6.9', true);
        }

        // Préparer les traductions pour le JavaScript
        $i18n_strings = array(
            'selectDate' => __('Veuillez sélectionner une date', 'le-margo'),
            'restaurantClosed' => __('Le restaurant est fermé à cette date', 'le-margo'),
            'checkingAvailability' => __('Vérification des disponibilités...', 'le-margo'),
            'selectTime' => __('Sélectionnez un horaire', 'le-margo'),
            'seats' => __('places', 'le-margo'),
            'available' => __('dispo.', 'le-margo'),
            'full' => __('Complet', 'le-margo'),
            'noOnlineBookingForGroup' => __('Désolé, nous ne prenons pas de réservations en ligne pour les groupes de plus de %d personnes.', 'le-margo'),
            'callUs' => __('N\'hésitez pas à nous appeler directement au %s pour réserver votre table. Nous ferons tout notre possible pour vous accueillir !', 'le-margo'),
            'invalidPhone' => __('Numéro de téléphone invalide', 'le-margo'),
            'invalidEmail' => __('Adresse email invalide', 'le-margo'),
            'invalidName' => __('Le nom doit contenir au moins 2 caractères', 'le-margo'),
            'currentLocale' => $locale_short,
        );

        wp_localize_script('le-margo-reservation', 'reservation_i18n', $i18n_strings);
    }
}
add_action('wp_enqueue_scripts', 'le_margo_reservation_scripts');

/**
 * Traiter le formulaire de réservation
 */
function le_margo_send_reservation() {
    // 1. Vérifications de sécurité
    if (!isset($_POST['reservation_nonce']) || !wp_verify_nonce($_POST['reservation_nonce'], 'send_reservation_nonce')) {
        le_margo_redirect_with_error('security_error', 'La vérification de sécurité a échoué.');
        return;
    }

    // Piège à spams Honeypot
    if (!empty($_POST['reservation_hp'])) {
        le_margo_redirect_with_error('spam_error', 'Erreur anti-spam.');
        return;
    }

    // Rate limiting
    $ip = Le_Margo_Security::get_client_ip();
    $rate_limiter = Le_Margo_Rate_Limiter::get_instance();
    if (!$rate_limiter->check_rate_limit($ip)) {
        le_margo_redirect_with_error('rate_limit', 'Trop de tentatives. Veuillez réessayer dans une heure.');
        return;
    }

    // Validation stricte des données
    if (!isset($_POST['customer_phone']) || !Le_Margo_Security::validate_phone_number($_POST['customer_phone'])) {
        le_margo_redirect_with_error('invalid_phone', 'Le numéro de téléphone est invalide.');
        return;
    }

    if (!isset($_POST['customer_email']) || !Le_Margo_Security::validate_email($_POST['customer_email'])) {
        le_margo_redirect_with_error('invalid_email', 'L\'adresse email est invalide.');
        return;
    }

    $reservation_manager = le_margo_get_reservation_manager();
    
    // Déterminer la source de la requête (admin ou public)
    $source = isset($_POST['status']) ? 'admin' : 'public';
    
    // 2. Assainissement et validation des données
    $required_fields = ['customer_name', 'customer_phone', 'customer_email', 'people', 'date', 'time'];
    $data = array_intersect_key($_POST, array_flip($required_fields));
    
    // Ajouter les champs optionnels
    $data['notes'] = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    
    // Validation supplémentaire des données
    if (empty($data['customer_name']) || strlen($data['customer_name']) < 2) {
        le_margo_redirect_with_error('invalid_name', 'Le nom est trop court.');
        return;
    }

    // Nettoyer et formater le numéro de téléphone
    $data['customer_phone'] = Le_Margo_Security::sanitize_phone($data['customer_phone']);
    
    // Fiabiliser le format de la date
    $date_obj = DateTime::createFromFormat('d/m/Y', $data['date']);
    if (!$date_obj) {
        le_margo_redirect_with_error('date_format_error', 'Le format de la date est incorrect. Veuillez utiliser JJ/MM/AAAA.');
        return;
    }
    
    // Vérifier que la date n'est pas dans le passé
    $today = new DateTime('today');
    if ($date_obj < $today) {
        le_margo_redirect_with_error('past_date', 'La date de réservation ne peut pas être dans le passé.');
        return;
    }
    
    // Correction et standardisation des clés
    $data['reservation_date'] = $date_obj->format('Y-m-d');
    $data['reservation_time'] = $data['time'];
    unset($data['date'], $data['time']);

    // Gestion des consentements RGPD
    $data['consent_data_processing'] = isset($_POST['consent_data_processing']) && $_POST['consent_data_processing'] == '1' ? 1 : 0;
    $data['accept_reminder'] = isset($_POST['accept_reminder']) && $_POST['accept_reminder'] == '1' ? 1 : 0;
    $data['newsletter'] = isset($_POST['newsletter']) && $_POST['newsletter'] == '1' ? 1 : 0;
    
    // Le consentement au traitement est obligatoire
    if ($data['consent_data_processing'] !== 1) {
        le_margo_redirect_with_error('consent_required', 'Vous devez accepter notre politique de confidentialité pour réserver.');
        return;
    }

    // Le statut est défini ici
    $data['status'] = ($source === 'admin' && isset($_POST['status'])) ? sanitize_text_field($_POST['status']) : 'pending';

    // 3. Vérification de la disponibilité
    $is_available = $reservation_manager->check_availability($data['reservation_date'], $data['reservation_time'], $data['people'], $source);
    
    if (!$is_available) {
        error_log("Tentative de réservation échouée pour le {$data['reservation_date']} à {$data['reservation_time']} pour {$data['people']} personnes. Source: {$source}. Créneau non disponible ou règle non respectée.");
        le_margo_redirect_with_error(
            'availability_error',
            'Désolé, ce créneau n\'est plus disponible ou ne respecte pas nos conditions de réservation. Veuillez choisir une autre date ou heure.'
        );
        return;
    }
    
    // 4. Création de la réservation
    $reservation_id = $reservation_manager->create_reservation($data);
    if (!$reservation_id) {
        error_log('ERREUR: Échec de la création de la réservation');
        global $wpdb;
        error_log('Erreur BDD: ' . $wpdb->last_error);
        Le_Margo_Error_Handler::handle_error(
            __('Erreur lors de l\'enregistrement de la réservation. Veuillez réessayer.', 'le-margo'),
            wp_get_referer()
        );
        return;
    }
    
    error_log("Réservation créée avec l'ID: $reservation_id");

    // 5. Mettre à jour les statistiques du client
    if (function_exists('le_margo_update_customer_visits')) {
        le_margo_update_customer_visits($data['customer_email'], $reservation_id);
        error_log("Statistiques mises à jour pour le client {$data['customer_email']} et la réservation ID: $reservation_id");
    }

    // Envoi des emails avec gestion d'erreur améliorée
    error_log('=== DÉBUT ENVOI EMAILS ===');
    try {
        $email_sent = $reservation_manager->send_confirmation_email($reservation_id);
        error_log('Résultat envoi email de confirmation: ' . ($email_sent ? 'SUCCÈS' : 'ÉCHEC'));
        
        if (!$email_sent) {
            error_log('ATTENTION: Échec de l\'envoi des emails de confirmation');
        }
    } catch (Exception $e) {
        error_log('EXCEPTION lors de l\'envoi des emails: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
    }
    error_log('=== FIN ENVOI EMAILS ===');

    // Nettoyage et redirection
    ob_end_clean();
    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    
    error_log("Redirection vers page de confirmation (AJAX: " . ($is_ajax ? 'OUI' : 'NON') . ")");
    error_log('=== FIN TRAITEMENT DE RÉSERVATION ===');
    
    Le_Margo_Redirect_Handler::redirect(home_url('/merci'), $is_ajax);
}

// Ajouter les hooks pour le traitement des réservations
add_action('admin_post_nopriv_send_reservation', 'le_margo_send_reservation');
add_action('admin_post_send_reservation', 'le_margo_send_reservation');

/**
 * Planifier l'envoi quotidien des emails de rappel
 */
function le_margo_schedule_reminder_emails() {
    if (!wp_next_scheduled('le_margo_daily_reminder_event')) {
        wp_schedule_event(strtotime('today 18:00:00'), 'daily', 'le_margo_daily_reminder_event');
    }
}
add_action('wp', 'le_margo_schedule_reminder_emails');

/**
 * Hook pour l'événement de rappel quotidien
 */
add_action('le_margo_daily_reminder_event', 'le_margo_send_reminder_emails');

/**
 * Mettre à jour la structure de la table des réservations
 */
function le_margo_update_reservations_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // Rendre les colonnes email et téléphone nullable pour correspondre à la réalité des réservations rapides
    $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN customer_email VARCHAR(100) NULL");
    $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN customer_phone VARCHAR(20) NULL");
    
    // Vérifier si les colonnes existent déjà
    $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
    $existing_columns = array_map(function($col) {
        return $col->Field;
    }, $columns);
    
    // Ajouter les colonnes manquantes
    if (!in_array('consent_data_processing', $existing_columns)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN consent_data_processing tinyint(1) DEFAULT 0");
    }
    
    if (!in_array('consent_data_storage', $existing_columns)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN consent_data_storage tinyint(1) DEFAULT 0");
    }
    
    // Supprimer l'ancienne colonne si elle existe
    if (in_array('gdpr_consent', $existing_columns)) {
        $wpdb->query("ALTER TABLE $table_name DROP COLUMN gdpr_consent");
    }
}
add_action('init', 'le_margo_update_reservations_table');


/**
 * Fonction utilitaire pour afficher les horaires d'ouverture personnalisés
 */
function le_margo_display_opening_hours($echo = true) {
    $days = [
        'monday'    => __('Lundi', 'le-margo'),
        'tuesday'   => __('Mardi', 'le-margo'),
        'wednesday' => __('Mercredi', 'le-margo'),
        'thursday'  => __('Jeudi', 'le-margo'),
        'friday'    => __('Vendredi', 'le-margo'),
        'saturday'  => __('Samedi', 'le-margo'),
        'sunday'    => __('Dimanche', 'le-margo'),
    ];
    
    $opening_hours = get_option('le_margo_opening_hours', []);
    
    $output = '<ul class="horaires-restaurant">';
    foreach ($days as $day_key => $day_label) {
        $hours_text = !empty($opening_hours[$day_key]) ? esc_html($opening_hours[$day_key]) : '<span class="closed-text">' . __('Fermé', 'le-margo') . '</span>';
        $output .= '<li><strong>' . esc_html($day_label) . ' :</strong> ' . $hours_text . '</li>';
    }
    $output .= '</ul>';

    if ($echo) {
        echo $output;
    } else {
        return $output;
    }
}

/**
 * Fonctions utilitaires pour les témoignages
 */

/**
 * Récupérer les témoignages en vedette
 */
function le_margo_get_featured_testimonials($limit = 6) {
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'featured_review',
                'value' => '1',
                'compare' => '='
            )
        )
    );
    
    return new WP_Query($args);
}

/**
 * Récupérer tous les témoignages avec priorisation des vedettes
 */
function le_margo_get_testimonials_prioritized($limit = 8) {
    // D'abord les témoignages en vedette
    $featured_query = le_margo_get_featured_testimonials(4);
    $featured_ids = array();
    
    if ($featured_query->have_posts()) {
        while ($featured_query->have_posts()) {
            $featured_query->the_post();
            $featured_ids[] = get_the_ID();
        }
        wp_reset_postdata();
    }
    
    // Puis compléter avec les autres témoignages
    $remaining_limit = $limit - count($featured_ids);
    $other_args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => $remaining_limit > 0 ? $remaining_limit : 0,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'post__not_in' => $featured_ids
    );
    
    $other_query = new WP_Query($other_args);
    $all_testimonials = array();
    
    // Ajouter les témoignages en vedette en premier
    if (!empty($featured_ids)) {
        $featured_args = array(
            'post_type' => 'testimonial',
            'posts_per_page' => count($featured_ids),
            'post__in' => $featured_ids,
            'orderby' => 'post__in'
        );
        $all_testimonials = get_posts($featured_args);
    }
    
    // Ajouter les autres témoignages
    if ($other_query->have_posts() && $remaining_limit > 0) {
        while ($other_query->have_posts()) {
            $other_query->the_post();
            $all_testimonials[] = get_post();
        }
        wp_reset_postdata();
    }
    
    return $all_testimonials;
}

/**
 * Obtenir le nom d'affichage d'une source de témoignage
 */
function le_margo_get_testimonial_source_name($source) {
    $sources = array(
        'google' => 'Google Reviews',
        'tripadvisor' => 'TripAdvisor',
        'booking' => 'Booking.com',
        'yelp' => 'Yelp',
        'facebook' => 'Facebook',
        'foursquare' => 'Foursquare',
        'opentable' => 'OpenTable',
        'lafourchette' => 'LaFourchette',
        'direct' => 'Livre d\'or',
        'autre' => 'Autre'
    );
    
    return isset($sources[$source]) ? $sources[$source] : $source;
}

/**
 * Vérifier si un témoignage a tous les champs requis
 */
function le_margo_is_testimonial_complete($post_id) {
    $required_fields = array('rating', 'author_name', 'testimonial_source');
    
    foreach ($required_fields as $field) {
        $value = get_post_meta($post_id, $field, true);
        if (empty($value)) {
            return false;
        }
    }
    
    // Vérifier que le contenu n'est pas vide
    $post = get_post($post_id);
    if (empty($post->post_content)) {
        return false;
    }
    
    return true;
}

/**
 * Ajouter une colonne pour le statut complet dans l'admin
 */
function le_margo_testimonial_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['rating'] = __('Note', 'le-margo');
    $new_columns['source'] = __('Source', 'le-margo');
    $new_columns['author'] = __('Auteur', 'le-margo');
    $new_columns['featured'] = __('Vedette', 'le-margo');
    $new_columns['verified'] = __('Vérifié', 'le-margo');
    $new_columns['complete'] = __('Complet', 'le-margo');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_testimonial_posts_columns', 'le_margo_testimonial_admin_columns');

/**
 * Remplir les colonnes personnalisées
 */
function le_margo_testimonial_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'rating':
            $rating = get_post_meta($post_id, 'rating', true);
            if ($rating) {
                echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                echo '<br><small>' . $rating . '/5</small>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'source':
            $source = get_post_meta($post_id, 'testimonial_source', true);
            if ($source) {
                echo esc_html(le_margo_get_testimonial_source_name($source));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'author':
            $author = get_post_meta($post_id, 'author_name', true);
            $location = get_post_meta($post_id, 'author_location', true);
            
            if ($author) {
                echo esc_html($author);
                if ($location) {
                    echo '<br><small style="color: #666;">' . esc_html($location) . '</small>';
                }
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'featured':
            $featured = get_post_meta($post_id, 'featured_review', true);
            if ($featured == '1') {
                echo '<span style="color: #856404;">★ Oui</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'verified':
            $verified = get_post_meta($post_id, 'verified_review', true);
            if ($verified == '1') {
                echo '<span style="color: #155724;">✓ Oui</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'complete':
            if (le_margo_is_testimonial_complete($post_id)) {
                echo '<span style="color: #155724;">✓ Complet</span>';
            } else {
                echo '<span style="color: #dc3232;">⚠ Incomplet</span>';
            }
            break;
    }
}
add_action('manage_testimonial_posts_custom_column', 'le_margo_testimonial_admin_column_content', 10, 2);

/**
 * Rendre les colonnes triables
 */
function le_margo_testimonial_sortable_columns($columns) {
    $columns['rating'] = 'rating';
    $columns['source'] = 'testimonial_source';
    $columns['author'] = 'author_name';
    $columns['featured'] = 'featured_review';
    $columns['verified'] = 'verified_review';
    
    return $columns;
}
add_filter('manage_edit-testimonial_sortable_columns', 'le_margo_testimonial_sortable_columns');

/**
 * Gérer le tri des colonnes personnalisées
 */
function le_margo_testimonial_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ('rating' == $orderby) {
        $query->set('meta_key', 'rating');
        $query->set('orderby', 'meta_value_num');
    } elseif ('testimonial_source' == $orderby) {
        $query->set('meta_key', 'testimonial_source');
        $query->set('orderby', 'meta_value');
    } elseif ('author_name' == $orderby) {
        $query->set('meta_key', 'author_name');
        $query->set('orderby', 'meta_value');
    } elseif ('featured_review' == $orderby) {
        $query->set('meta_key', 'featured_review');
        $query->set('orderby', 'meta_value');
    } elseif ('verified_review' == $orderby) {
        $query->set('meta_key', 'verified_review');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'le_margo_testimonial_orderby');

/**
 * Ajouter des filtres dans l'admin des témoignages
 */
function le_margo_testimonial_admin_filters() {
    global $typenow;
    
    if ($typenow == 'testimonial') {
        // Filtre par source
        $sources = array(
            'google' => 'Google Reviews',
            'tripadvisor' => 'TripAdvisor',
            'booking' => 'Booking.com',
            'yelp' => 'Yelp',
            'facebook' => 'Facebook',
            'foursquare' => 'Foursquare',
            'opentable' => 'OpenTable',
            'lafourchette' => 'LaFourchette',
            'direct' => 'Livre d\'or',
            'autre' => 'Autre'
        );
        
        $current_source = isset($_GET['testimonial_source']) ? $_GET['testimonial_source'] : '';
        
        echo '<select name="testimonial_source">';
        echo '<option value="">' . __('Toutes les sources', 'le-margo') . '</option>';
        foreach ($sources as $value => $label) {
            echo '<option value="' . $value . '"' . selected($current_source, $value, false) . '>' . $label . '</option>';
        }
        echo '</select>';
        
        // Filtre par témoignages en vedette
        $current_featured = isset($_GET['featured_review']) ? $_GET['featured_review'] : '';
        echo '<select name="featured_review">';
        echo '<option value="">' . __('Tous les témoignages', 'le-margo') . '</option>';
        echo '<option value="1"' . selected($current_featured, '1', false) . '>' . __('En vedette uniquement', 'le-margo') . '</option>';
        echo '<option value="0"' . selected($current_featured, '0', false) . '>' . __('Pas en vedette', 'le-margo') . '</option>';
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'le_margo_testimonial_admin_filters');

/**
 * Appliquer les filtres personnalisés
 */
function le_margo_testimonial_parse_query($query) {
    global $pagenow;
    
    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'testimonial') {
        $meta_query = array();
        
        if (isset($_GET['testimonial_source']) && $_GET['testimonial_source'] != '') {
            $meta_query[] = array(
                'key' => 'testimonial_source',
                'value' => $_GET['testimonial_source'],
                'compare' => '='
            );
        }
        
        if (isset($_GET['featured_review']) && $_GET['featured_review'] != '') {
            $meta_query[] = array(
                'key' => 'featured_review',
                'value' => $_GET['featured_review'],
                'compare' => '='
            );
        }
        
        if (!empty($meta_query)) {
            $query->query_vars['meta_query'] = $meta_query;
        }
    }
}
add_filter('parse_query', 'le_margo_testimonial_parse_query');

/**
 * ===============================================
 * OPTIMISATION SEO AVANCÉE POUR LE MARGO
 * ===============================================
 */

// Toutes les fonctions SEO statiques ont été supprimées - remplacées par le système dynamique seo-auto-config.php
// Seules les meta données configurées depuis l'admin des pages sont maintenant utilisées

// Ajout de la page d'options pour le mode maintenance
function le_margo_maintenance_admin_menu() {
    add_options_page(
        'Réglages du Mode Maintenance',
        'Maintenance',
        'manage_options',
        'le-margo-maintenance',
        'le_margo_maintenance_page_html'
    );
}
add_action('admin_menu', 'le_margo_maintenance_admin_menu');

// Contenu de la page d'options
function le_margo_maintenance_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('le_margo_maintenance_options');
            do_settings_sections('le-margo-maintenance');
            submit_button('Enregistrer les modifications');
            ?>
        </form>
    </div>
    <?php
}

// Enregistrement des réglages
function le_margo_maintenance_settings_init() {
    register_setting('le_margo_maintenance_options', 'le_margo_maintenance_mode_status');

    add_settings_section(
        'le_margo_maintenance_section',
        'Activer le mode maintenance',
        'le_margo_maintenance_section_callback',
        'le-margo-maintenance'
    );

    add_settings_field(
        'le_margo_maintenance_mode_field',
        'Mode maintenance',
        'le_margo_maintenance_mode_field_cb',
        'le-margo-maintenance',
        'le_margo_maintenance_section'
    );
}
add_action('admin_init', 'le_margo_maintenance_settings_init');

// Callback pour la section
function le_margo_maintenance_section_callback() {
    echo '<p>Cochez la case ci-dessous pour activer le mode maintenance. Seuls les administrateurs pourront voir le site.</p>';
}

// Callback pour le champ
function le_margo_maintenance_mode_field_cb() {
    $option = get_option('le_margo_maintenance_mode_status');
    ?>
    <label for="le_margo_maintenance_mode_status">
        <input type="checkbox" name="le_margo_maintenance_mode_status" id="le_margo_maintenance_mode_status" value="1" <?php checked(1, $option, true); ?>>
        Activer
    </label>
    <?php
}

// Fonction pour activer la page de maintenance
function le_margo_maintenance_mode() {
    // Vérifier si le mode maintenance est activé dans les options
    $maintenance_enabled = get_option('le_margo_maintenance_mode_status');

    // Si le mode est activé et que l'utilisateur n'est pas admin, afficher la page de maintenance
    if ($maintenance_enabled && !current_user_can('administrator')) {
        // S'assurer que la page de maintenance existe avant de l'inclure
        $maintenance_file = get_template_directory() . '/maintenance.php';
        if (file_exists($maintenance_file)) {
            include($maintenance_file);
            exit();
        }
    }
}
add_action('template_redirect', 'le_margo_maintenance_mode');

// Enqueue des scripts et styles
function le_margo_scripts() {
    wp_enqueue_style('le-margo-style', get_stylesheet_uri(), array(), '2.2.0');
    
    // Script pour l'effet de zoom qui suit le curseur
    wp_enqueue_script('le-margo-gallery-zoom', get_template_directory_uri() . '/assets/js/gallery-zoom.js', array(), '1.0.0', true);
    
    // Les paramètres de réservation sont maintenant chargés dans la fonction le_margo_enqueue_assets
}
add_action('wp_enqueue_scripts', 'le_margo_scripts');

/*
 * Section pour la gestion de la page d'accueil dynamique
 * ========================================================
 */

// 1. Création de la page d'administration
function le_margo_homepage_settings_menu() {
    add_theme_page(
        'Réglages de la page d\'accueil', // Titre de la page
        'Page d\'accueil',               // Titre du menu
        'manage_options',                 // Capacité requise
        'le-margo-homepage-settings',     // Slug du menu
        'le_margo_homepage_settings_page_html' // Fonction de callback pour le contenu
    );
}
add_action('admin_menu', 'le_margo_homepage_settings_menu');

// 2. Contenu HTML de la page d'administration
function le_margo_homepage_settings_page_html() {
    // Vérifier les permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    // Gérer la sauvegarde des données
    if (isset($_POST['le_margo_gallery_nonce']) && wp_verify_nonce($_POST['le_margo_gallery_nonce'], 'le_margo_gallery_save')) {
        if (isset($_POST['gallery_images'])) {
            $gallery_data = json_decode(stripslashes($_POST['gallery_images']), true);
            update_option('le_margo_homepage_gallery', $gallery_data);
        } else {
            // Si aucune image n'est envoyée, on supprime l'option
            delete_option('le_margo_homepage_gallery');
        }
        echo '<div class="notice notice-success is-dismissible"><p>Galerie mise à jour !</p></div>';
    }

    // Récupérer les données existantes
    $gallery_data = get_option('le_margo_homepage_gallery', []);
    ?>
    <div class="wrap le-margo-gallery-admin">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>Gérez ici les images de la galerie de votre page d'accueil. Ajoutez, supprimez, réorganisez et définissez la forme de chaque image.</p>
        
        <form id="le-margo-gallery-form" method="post" action="">
            <input type="hidden" name="gallery_images" id="gallery_images_data" value="<?php echo esc_attr(json_encode($gallery_data)); ?>">
            <?php wp_nonce_field('le_margo_gallery_save', 'le_margo_gallery_nonce'); ?>

            <div id="gallery-preview-container">
                <?php foreach ($gallery_data as $image) : ?>
                    <div class="gallery-item-preview" data-id="<?php echo esc_attr($image['id']); ?>">
                        <img src="<?php echo esc_url(wp_get_attachment_thumb_url($image['id'])); ?>" />
                        <div class="item-controls">
                             <select class="image-shape">
                                <option value="normal" <?php selected($image['shape'], 'normal'); ?>>Normale</option>
                                <option value="tall" <?php selected($image['shape'], 'tall'); ?>>Haute</option>
                                <option value="wide" <?php selected($image['shape'], 'wide'); ?>>Large</option>
                            </select>
                            <button type="button" class="button remove-image">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <p>
                <button type="button" id="add-gallery-images" class="button button-primary">Ajouter des images</button>
                <?php submit_button('Enregistrer la galerie', 'primary', 'save_gallery', false); ?>
            </p>
        </form>
    </div>
    <?php
}

// 3. Enqueue des scripts et styles pour la page d'admin
function le_margo_homepage_admin_assets($hook) {
    if ($hook != 'appearance_page_le-margo-homepage-settings') {
        return;
    }

    // Scripts
    wp_enqueue_media(); // Important pour le gestionnaire de médias
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script(
        'le-margo-homepage-admin-js',
        get_template_directory_uri() . '/assets/js/homepage-admin.js',
        ['jquery', 'jquery-ui-sortable'],
        _LE_MARGO_VERSION,
        true
    );

    // Styles
    wp_enqueue_style(
        'le-margo-homepage-admin-css',
        get_template_directory_uri() . '/assets/css/homepage-admin.css',
        [],
        _LE_MARGO_VERSION
    );
}
add_action('admin_enqueue_scripts', 'le_margo_homepage_admin_assets');

// 4. Autoriser le téléversement des images WebP
function le_margo_add_webp_support($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'le_margo_add_webp_support');

// 5. Augmenter la compression des images pour de meilleures performances
function le_margo_image_compression_quality($quality) {
    return 75; // 75% de qualité. La valeur par défaut est 82.
}
add_filter('jpeg_quality', 'le_margo_image_compression_quality');

/**
 * @param string $error_code Code d'erreur
 * @param string $log_message Message d'erreur à logger
 */
function le_margo_redirect_with_error($error_code, $log_message) {
    // Log de l'erreur pour le débogage
    error_log("Erreur de réservation ($error_code): $log_message");

    // Construire l'URL de redirection
    $redirect_url = home_url('/reserver/');
    
    // Pour les erreurs de validation, nous voulons garder les données du formulaire
    // Ici, nous nous concentrons sur le message d'erreur.
    // Pour une UX avancée, on pourrait stocker les `$_POST` dans une session et les re-remplir.
    $error_message = urlencode($log_message);
    $redirect_url = add_query_arg('reservation_error', $error_message, $redirect_url);

    wp_safe_redirect($redirect_url);
    exit;
}

/**
 * Ajouter les headers de sécurité
 * TEMPORAIREMENT DÉSACTIVÉ POUR TESTER GA4
 */
function le_margo_security_headers() {
    // Fonction désactivée temporairement pour debug GA4
    return;
    
    if (!is_admin()) {
        // Protection contre le clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Protection XSS
        header('X-XSS-Protection: 1; mode=block');
        
        // Protection contre le MIME-type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Politique de sécurité du contenu - TRÈS PERMISSIVE pour GA4
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https:; connect-src 'self' https: wss: data: blob:; img-src 'self' data: https: blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:;");
        
        // Référer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}
add_action('send_headers', 'le_margo_security_headers');
