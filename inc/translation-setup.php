<?php
/**
 * Configuration de la traduction pour le thème Le Margo.
 * Gère le changement de langue et l'affichage du sélecteur.
 */

if (!defined('ABSPATH')) {
    exit; // Accès direct non autorisé.
}

/**
 * Démarre une session PHP si elle n'est pas déjà active.
 * Nécessaire pour mémoriser la langue de l'utilisateur.
 * Gère correctement la fermeture de session pour l'API REST.
 */
function le_margo_start_session() {
    // Ne pas démarrer de session pour l'API REST
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }
    
    // Ne pas démarrer de session pour les requêtes AJAX
    if (wp_doing_ajax()) {
        return;
    }
    
    // Ne pas démarrer de session pour les requêtes cron
    if (defined('DOING_CRON') && DOING_CRON) {
        return;
    }
    
    // Ne pas démarrer de session pour les requêtes CLI
    if (defined('WP_CLI') && WP_CLI) {
        return;
    }
    
    // Vérifier si on est dans l'admin et éviter les conflits
    if (is_admin() && !wp_doing_ajax()) {
        // En admin, utiliser les cookies plutôt que les sessions
        return;
    }
    
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'le_margo_start_session', 1);

/**
 * Ferme la session PHP avant les requêtes HTTP.
 * Important pour éviter les conflits avec l'API REST.
 */
function le_margo_close_session() {
    if (session_id()) {
        session_write_close();
    }
}

// Fermer la session avant les requêtes HTTP
add_action('http_api_debug', 'le_margo_close_session', 1);
add_action('wp_remote_request', 'le_margo_close_session', 1);

// Fermer la session avant les requêtes AJAX
add_action('wp_ajax_nopriv_le_margo_ajax', 'le_margo_close_session', 1);
add_action('wp_ajax_le_margo_ajax', 'le_margo_close_session', 1);

/**
 * Retourne la liste des langues disponibles.
 * @return array
 */
function le_margo_get_available_languages() {
    return [
        'fr_FR' => 'FR',
        'en_US' => 'EN',
    ];
}

/**
 * Charge le "text domain" du thème pour rendre la traduction possible.
 */
function le_margo_load_textdomain() {
    load_theme_textdomain('le-margo', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'le_margo_load_textdomain');

/**
 * Détermine et retourne la langue actuelle de l'utilisateur.
 * La priorité est : paramètre URL > session > cookie > défaut.
 * @return string
 */
function le_margo_get_current_language() {
    $available_languages = le_margo_get_available_languages();
    $default_language = 'fr_FR';
    $cookie_name = 'le_margo_lang';
    $current_lang = $default_language;

    // Priorité 1: Paramètre dans l'URL (?lang=en_US)
    if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $available_languages)) {
        $current_lang = $_GET['lang'];
        
        // Sauvegarder dans la session si disponible
        if (session_id()) {
            $_SESSION[$cookie_name] = $current_lang;
        }
        
        // Sauvegarder dans un cookie
        setcookie($cookie_name, $current_lang, time() + (365 * 24 * 60 * 60), COOKIEPATH, COOKIE_DOMAIN);
    } 
    // Priorité 2: Session PHP (seulement si session active)
    elseif (session_id() && isset($_SESSION[$cookie_name]) && array_key_exists($_SESSION[$cookie_name], $available_languages)) {
        $current_lang = $_SESSION[$cookie_name];
    }
    // Priorité 3: Cookie
    elseif (isset($_COOKIE[$cookie_name]) && array_key_exists($_COOKIE[$cookie_name], $available_languages)) {
        $current_lang = $_COOKIE[$cookie_name];
        
        // Synchroniser avec la session si disponible
        if (session_id()) {
            $_SESSION[$cookie_name] = $current_lang;
        }
    }

    return $current_lang;
}

/**
 * Applique la langue sélectionnée au chargement de WordPress.
 * N'affecte pas le tableau de bord pour ne pas gêner l'administration.
 */
function le_margo_apply_locale($locale) {
    // Ne pas changer la locale pour l'API REST
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return $locale;
    }
    
    // Ne pas changer la locale pour les requêtes AJAX
    if (wp_doing_ajax()) {
        return $locale;
    }
    
    // Ne pas changer la locale pour les requêtes cron
    if (defined('DOING_CRON') && DOING_CRON) {
        return $locale;
    }
    
    // Ne pas changer la locale pour les requêtes CLI
    if (defined('WP_CLI') && WP_CLI) {
        return $locale;
    }
    
    if (is_admin()) {
        return $locale;
    }
    
    return le_margo_get_current_language();
}
add_filter('locale', 'le_margo_apply_locale');

/**
 * Affiche le sélecteur de langue (ex: FR | EN | ES).
 */
function the_language_switcher() {
    $available_languages = le_margo_get_available_languages();
    $current_lang_code = le_margo_get_current_language();

    // SVG des drapeaux
    $flags_svg = [
        'fr_FR' => '<svg class="flag-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16"><rect width="8" height="16" fill="#002395"/><rect x="8" width="8" height="16" fill="#fff"/><rect x="16" width="8" height="16" fill="#ED2939"/></svg>',
        'en_US' => '<svg class="flag-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 16"><rect width="24" height="16" fill="#012169"/><path d="M0 0l24 16M24 0L0 16" stroke="#fff" stroke-width="3"/><path d="M0 0l24 16M24 0L0 16" stroke="#C8102E" stroke-width="1.8"/><path d="M12 0v16M0 8h24" stroke="#fff" stroke-width="5.3"/><path d="M12 0v16M0 8h24" stroke="#C8102E" stroke-width="3.2"/></svg>'
    ];

    echo '<div class="language-switcher">';
    $links = [];
    foreach ($available_languages as $lang_code => $lang_name) {
        $url = add_query_arg('lang', $lang_code, home_url(add_query_arg(array(), $GLOBALS['wp']->request)));
        $flag_svg = isset($flags_svg[$lang_code]) ? $flags_svg[$lang_code] : esc_html($lang_name);
        
        if ($lang_code === $current_lang_code) {
            $links[] = '<span class="current-lang">' . $flag_svg . '</span>';
        } else {
            $links[] = '<a href="' . esc_url($url) . '" hreflang="' . esc_attr($lang_code) . '">' . $flag_svg . '</a>';
        }
    }
    echo implode(' ', $links); // On n'a plus besoin du séparateur |
    echo '</div>';
}

/**
 * Optimisation des performances : fermer la session à la fin du script
 */
function le_margo_shutdown_session() {
    if (session_id()) {
        session_write_close();
    }
}
add_action('shutdown', 'le_margo_shutdown_session', 999);
