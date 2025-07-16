<?php
/**
 * Le Margo - Fonctions et définitions du thème
 */

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
require_once get_template_directory() . '/inc/seo-meta.php';
require_once get_template_directory() . '/inc/menu-admin.php';
require_once get_template_directory() . '/inc/testimonial-metaboxes.php';
require_once get_template_directory() . '/inc/reservations-core.php';

/**
 * Enregistrement et traitement des réservations
 */

// Enregistrer les scripts et styles pour la page de réservation
function le_margo_reservation_scripts() {
    // Ne charger les scripts que sur la page de réservation
    if (is_page('reserver')) {
        // Flatpickr pour le sélecteur de date
        wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.9');
        wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.9', true);
        wp_enqueue_script('flatpickr-fr', 'https://npmcdn.com/flatpickr/dist/l10n/fr.js', array('flatpickr'), '4.6.9', true);

        // Nos styles et scripts personnalisés
        wp_enqueue_style('le-margo-reservation', get_template_directory_uri() . '/assets/css/reservation.css', array(), '1.0.0');
        wp_enqueue_script('le-margo-reservation', get_template_directory_uri() . '/assets/js/reservation.js', array('jquery'), '1.0.1', true);
        
        // Les paramètres sont maintenant chargés dans le_margo_enqueue_assets pour éviter les doublons.
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

    $reservation_manager = le_margo_get_reservation_manager();
    
    // Déterminer la source de la requête (admin ou public)
    // Le formulaire admin inclut un champ 'status', le public non.
    $source = isset($_POST['status']) ? 'admin' : 'public';
    
    // 2. Assainissement et validation des données
    $required_fields = ['customer_name', 'customer_phone', 'customer_email', 'people', 'date', 'time'];
    $data = array_intersect_key($_POST, array_flip($required_fields));
    
    // Fiabiliser le format de la date
    $date_obj = DateTime::createFromFormat('d/m/Y', $data['date']);
    if (!$date_obj) {
        le_margo_redirect_with_error('date_format_error', 'Le format de la date est incorrect. Veuillez utiliser JJ/MM/AAAA.');
        return;
    }
    
    // Correction et standardisation des clés pour correspondre au reste du système
    $data['reservation_date'] = $date_obj->format('Y-m-d');
    $data['reservation_time'] = $data['time'];
    unset($data['date'], $data['time']);

    $data['consent_data_processing'] = isset($_POST['consent_data_processing']) ? 1 : 0;
    $data['consent_data_storage'] = isset($_POST['consent_data_storage']) ? 1 : 0;
    $data['status'] = ($source === 'admin' && isset($_POST['status'])) ? sanitize_text_field($_POST['status']) : 'pending';

    // 3. Vérification de la disponibilité (maintenant avec la source)
    $is_available = $reservation_manager->check_availability($data['reservation_date'], $data['reservation_time'], $data['people'], $source);
    
    if (!$is_available) {
        // Log de l'échec pour le débogage
        error_log("Tentative de réservation échouée pour le {$data['reservation_date']} à {$data['reservation_time']} pour {$data['people']} personnes. Source: {$source}. Créneau non disponible ou règle non respectée.");

        le_margo_redirect_with_error(
            'availability_error',
            'Désolé, ce créneau n\'est plus disponible ou ne respecte pas nos conditions de réservation (ex: réservation moins de 2h à l\'avance). Veuillez choisir une autre date ou heure.'
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
    }
    
    error_log("Réservation créée avec l'ID: $reservation_id");

    // 5. Mettre à jour les statistiques du client
    if (function_exists('le_margo_update_customer_visits')) {
        le_margo_update_customer_visits($data['customer_email'], $reservation_id);
        error_log("Statistiques mises à jour pour le client {$data['customer_email']} et la réservation ID: $reservation_id");
    } else {
        error_log("ERREUR: La fonction le_margo_update_customer_visits() est introuvable. Les statistiques ne sont pas mises à jour.");
    }

    // Envoi des emails avec gestion d'erreur améliorée
    error_log('=== DÉBUT ENVOI EMAILS ===');
    try {
        $email_sent = $reservation_manager->send_confirmation_email($reservation_id);
        error_log('Résultat envoi email de confirmation: ' . ($email_sent ? 'SUCCÈS' : 'ÉCHEC'));
        
        if (!$email_sent) {
            error_log('ATTENTION: Échec de l\'envoi des emails de confirmation');
            // Ne pas faire échouer la réservation si l'email échoue
            // La réservation est créée, on informe juste l'utilisateur
        }
    } catch (Exception $e) {
        error_log('EXCEPTION lors de l\'envoi des emails: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        // Ne pas faire échouer la réservation
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
 * Fonction utilitaire pour afficher les horaires d'ouverture dynamiques
 */
function le_margo_display_opening_hours($echo = true) {
    $jours = array(
        'monday'    => __('Lundi', 'le-margo'),
        'tuesday'   => __('Mardi', 'le-margo'),
        'wednesday' => __('Mercredi', 'le-margo'),
        'thursday'  => __('Jeudi', 'le-margo'),
        'friday'    => __('Vendredi', 'le-margo'),
        'saturday'  => __('Samedi', 'le-margo'),
        'sunday'    => __('Dimanche', 'le-margo'),
    );
    $horaires = get_option('le_margo_opening_hours');
    $out = '<ul class="horaires-restaurant">';
    foreach ($jours as $key => $label) {
        $val = isset($horaires[$key]) ? $horaires[$key] : '';
        $out .= '<li><strong>' . $label . ' :</strong> ' . ($val ? esc_html($val) : '<span style="color:#aaa">Fermé</span>') . '</li>';
    }
    $out .= '</ul>';
    if ($echo) {
        echo $out;
    } else {
        return $out;
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

// 1. Schema.org Markup pour Restaurant
function le_margo_add_schema_markup() {
    if (is_front_page() || is_page('a-propos') || is_page('reserver')) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => 'Le Margo',
            'description' => 'Restaurant gastronomique à Eymet, cuisine locale et vins naturels. Produits frais bio du Périgord dans une ambiance intimiste.',
            'url' => home_url(),
            'logo' => get_template_directory_uri() . '/assets/images/le-margo-logo.png',
            'image' => array(
                get_template_directory_uri() . '/assets/images/restaurant-exterieur-eymet.jpg',
                get_template_directory_uri() . '/assets/images/plat-signature-lemargo.webp',
                get_template_directory_uri() . '/assets/images/restaurant-interieur-ambiance.jpg'
            ),
            'telephone' => '+33602556315',
            'email' => 'sasdamaeymet@gmail.com',
            'priceRange' => '€€',
            'currenciesAccepted' => 'EUR',
            'paymentAccepted' => 'Cash, Credit Card',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '6 avenue du 6 juin 1944',
                'addressLocality' => 'Eymet',
                'addressRegion' => 'Dordogne',
                'postalCode' => '24500',
                'addressCountry' => 'FR'
            ),
            'geo' => array(
                '@type' => 'GeoCoordinates',
                'latitude' => '44.66685638628374',
                'longitude' => '0.3969304765728053'
            ),
            'openingHours' => array(
                'Mo-We off',
                'Th 09:00-15:00,19:00-23:00',
                'Fr 19:00-23:00',
                'Sa 19:00-23:00',
                'Su off'
            ),
            'servesCuisine' => array(
                'French',
                'Contemporary',
                'Local',
                'Organic',
                'Seasonal'
            ),
            'hasMenu' => array(
                '@type' => 'Menu',
                'name' => 'Menu du jour',
                'description' => 'Menu saisonnier avec produits locaux bio'
            ),
            'aggregateRating' => array(
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => '47',
                'bestRating' => '5'
            ),
            'founder' => array(
                array(
                    '@type' => 'Person',
                    'name' => 'Antoine Bursens'
                ),
                array(
                    '@type' => 'Person',
                    'name' => 'Floriane Valladon'
                )
            ),
            'specialities' => array(
                'Cuisine créative',
                'Produits locaux',
                'Vins naturels',
                'Cuisine bio',
                'Terroir du Périgord'
            ),
            'amenityFeature' => array(
                array(
                    '@type' => 'LocationFeatureSpecification',
                    'name' => 'Wifi',
                    'value' => true
                ),
                array(
                    '@type' => 'LocationFeatureSpecification',
                    'name' => 'Terrasse',
                    'value' => true
                ),
                array(
                    '@type' => 'LocationFeatureSpecification',
                    'name' => 'Parking',
                    'value' => true
                )
            ),
            'sameAs' => array(
                'https://instagram.com/lemargoeymet',
                'https://www.facebook.com/lemargoeymet'
            )
        );
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
add_action('wp_head', 'le_margo_add_schema_markup');

// 2. Breadcrumbs SEO
function le_margo_breadcrumbs() {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array()
    );
    
    $position = 1;
    
    // Home
    $schema['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Accueil',
        'item' => home_url()
    );
    
    if (is_page()) {
        $schema['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }
    
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'le_margo_breadcrumbs');

// 3. Méta-descriptions optimisées
function le_margo_seo_meta_description() {
    $description = '';
    
    if (is_front_page()) {
        $description = 'Restaurant Le Margo à Eymet (Dordogne) - Cuisine locale créative, vins naturels, produits bio du Périgord. Réservation 06 02 55 63 15. Meilleur restaurant Eymet 2024.';
    } elseif (is_page('a-propos')) {
        $description = 'Découvrez l\'histoire du restaurant Le Margo à Eymet : Antoine Bursens et Floriane Valladon, cuisine créative avec produits locaux bio du Périgord. Restaurant gastronomique Dordogne.';
    } elseif (is_page('galerie')) {
        $description = 'Photos du restaurant Le Margo Eymet : plats gastronomiques, vins naturels, ambiance restaurant. Découvrez notre cuisine créative locale en images.';
    } elseif (is_page('eymet')) {
        $description = 'Découvrez Eymet, bastide médiévale de Dordogne (1270). Le Margo vous guide dans cette perle du Périgord : château, place centrale, rivière Dropt, marché traditionnel.';
    } elseif (is_page('reserver')) {
        $description = 'Réservez votre table au restaurant Le Margo Eymet. Restaurant gastronomique Dordogne, cuisine locale bio, vins naturels. Réservation en ligne ou 06 02 55 63 15.';
    }
    
    if (!empty($description)) {
        echo '<meta name="description" content="' . esc_attr($description) . '">';
    }
}
add_action('wp_head', 'le_margo_seo_meta_description', 1);

// 4. Open Graph et Twitter Cards
function le_margo_social_meta_tags() {
    $title = '';
    $description = '';
    $image = get_template_directory_uri() . '/assets/images/restaurant-exterieur-eymet.jpg';
    
    if (is_front_page()) {
        $title = 'Le Margo - Restaurant Gastronomique Eymet Dordogne | Cuisine Locale & Vins Naturels';
        $description = 'Restaurant Le Margo à Eymet : cuisine créative avec produits locaux bio, vins naturels, ambiance intimiste. Réservez au 06 02 55 63 15.';
    } elseif (is_page()) {
        $title = get_the_title() . ' - Le Margo Restaurant Eymet';
        $description = get_the_excerpt() ?: 'Restaurant Le Margo Eymet - Cuisine locale créative et vins naturels';
    }
    
    // Open Graph
    echo '<meta property="og:title" content="' . esc_attr($title) . '">';
    echo '<meta property="og:description" content="' . esc_attr($description) . '">';
    echo '<meta property="og:image" content="' . esc_url($image) . '">';
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">';
    echo '<meta property="og:type" content="website">';
    echo '<meta property="og:site_name" content="Le Margo">';
    echo '<meta property="og:locale" content="fr_FR">';
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">';
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">';
    echo '<meta name="twitter:image" content="' . esc_url($image) . '">';
    echo '<meta name="twitter:site" content="@lemargoeymet">';
}
add_action('wp_head', 'le_margo_social_meta_tags', 2);

// 5. Génération de sitemap XML personnalisé
function le_margo_generate_sitemap() {
    if (isset($_GET['sitemap']) && $_GET['sitemap'] === 'lemargo') {
        header('Content-Type: application/xml; charset=utf-8');
        
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Pages principales
        $pages = array(
            '' => array('priority' => '1.0', 'changefreq' => 'weekly'),
            'a-propos' => array('priority' => '0.8', 'changefreq' => 'monthly'),
            'galerie' => array('priority' => '0.7', 'changefreq' => 'monthly'),
            'eymet' => array('priority' => '0.9', 'changefreq' => 'monthly'),
            'reserver' => array('priority' => '0.9', 'changefreq' => 'daily')
        );
        
        foreach ($pages as $slug => $config) {
            $url = home_url($slug ? '/' . $slug : '');
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $url . '</loc>';
            $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $sitemap .= '<changefreq>' . $config['changefreq'] . '</changefreq>';
            $sitemap .= '<priority>' . $config['priority'] . '</priority>';
            $sitemap .= '</url>';
        }
        
        $sitemap .= '</urlset>';
        echo $sitemap;
        exit;
    }
}
add_action('init', 'le_margo_generate_sitemap');

// 6. Mots-clés locaux pour le SEO
function le_margo_add_local_keywords() {
    $keywords = array(
        'restaurant Eymet',
        'restaurant Dordogne',
        'restaurant Périgord',
        'gastronomie Eymet',
        'cuisine locale Dordogne',
        'vins naturels Eymet',
        'restaurant bio Périgord',
        'Le Margo',
        'bastide médiévale restaurant',
        'Antoine Bursens',
        'Floriane Valladon',
        'produits locaux Dordogne',
        'meilleur restaurant Eymet',
        'restaurant gastronomique 24500'
    );
    
    echo '<meta name="keywords" content="' . implode(', ', $keywords) . '">';
}
add_action('wp_head', 'le_margo_add_local_keywords', 3);

// 7. Optimisation des images pour le SEO
function le_margo_optimize_images() {
    // Ajouter des attributs alt automatiques pour les images sans alt
    add_filter('the_content', function($content) {
        $content = preg_replace_callback('/<img[^>]+>/i', function($matches) {
            $img = $matches[0];
            if (strpos($img, 'alt=') === false) {
                $img = str_replace('<img', '<img alt="Restaurant Le Margo Eymet - Cuisine locale"', $img);
            }
            return $img;
        }, $content);
        return $content;
    });
}
add_action('init', 'le_margo_optimize_images');

// 8. Données structurées pour les événements
function le_margo_events_schema() {
    if (is_page('eymet') || is_front_page()) {
        $events = array(
            array(
                '@type' => 'Event',
                'name' => 'Marché traditionnel d\'Eymet',
                'description' => 'Marché hebdomadaire avec produits locaux sur la place centrale d\'Eymet',
                'location' => array(
                    '@type' => 'Place',
                    'name' => 'Place Centrale Eymet',
                    'address' => 'Place Centrale, 24500 Eymet, France'
                ),
                'startDate' => 'every Thursday 08:00',
                'organizer' => array(
                    '@type' => 'Organization',
                    'name' => 'Ville d\'Eymet'
                )
            ),
            array(
                '@type' => 'Event',
                'name' => 'Wine Tasting avec Emma Jenkins',
                'description' => 'Dégustation de vins naturels tous les mardis soirs au restaurant Le Margo',
                'location' => array(
                    '@type' => 'Restaurant',
                    'name' => 'Le Margo',
                    'address' => '6 avenue du 6 juin 1944, 24500 Eymet, France'
                ),
                'startDate' => 'every Tuesday 19:00',
                'organizer' => array(
                    '@type' => 'Organization',
                    'name' => 'Le Margo'
                )
            )
        );
        
        foreach ($events as $event) {
            echo '<script type="application/ld+json">' . json_encode($event) . '</script>';
        }
    }
}
add_action('wp_head', 'le_margo_events_schema');

// 9. Optimisation pour Google My Business
function le_margo_business_schema() {
    $business = array(
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Le Margo',
        'image' => get_template_directory_uri() . '/assets/images/restaurant-exterieur-eymet.jpg',
        'telephone' => '+33602556315',
        'email' => 'sasdamaeymet@gmail.com',
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => '6 avenue du 6 juin 1944',
            'addressLocality' => 'Eymet',
            'addressRegion' => 'Nouvelle-Aquitaine',
            'postalCode' => '24500',
            'addressCountry' => 'France'
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => 44.66685638628374,
            'longitude' => 0.3969304765728053
        ),
        'url' => home_url(),
        'priceRange' => '€€',
        'openingHours' => 'Th-Sa',
        'smokingAllowed' => false,
        'wheelchairAccessible' => true
    );
    
    echo '<script type="application/ld+json">' . json_encode($business) . '</script>';
}
add_action('wp_head', 'le_margo_business_schema');

// 10. Optimisation pour Core Web Vitals
function le_margo_performance_optimization() {
    // Précharger les ressources critiques
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/main.css" as="style">';
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style">';
    
    // DNS prefetch pour les domaines externes
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
    echo '<link rel="dns-prefetch" href="//www.google.com">';
    echo '<link rel="dns-prefetch" href="//instagram.com">';
}
add_action('wp_head', 'le_margo_performance_optimization', 1);

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
