<?php
/**
 * Redirections pour les anciennes pages supprimées
 * @package Le_Margo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Redirection des anciennes pages supprimées vers l'accueil
 */
function le_margo_redirect_old_pages() {
    // Si on accède à l'ancienne page à propos
    if (is_page('a-propos') || is_page('about') || 
        strpos($_SERVER['REQUEST_URI'], '/a-propos') !== false ||
        strpos($_SERVER['REQUEST_URI'], '/about') !== false) {
        
        // Redirection 301 permanente vers l'accueil
        wp_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'le_margo_redirect_old_pages', 1);

/**
 * Envoyer un en-tête 410 (Gone) pour les pages définitivement supprimées
 */
function le_margo_send_410_for_deleted_pages() {
    if (is_page('a-propos') || 
        strpos($_SERVER['REQUEST_URI'], '/a-propos') !== false) {
        
        status_header(410); // 410 = Gone (page définitivement supprimée)
        nocache_headers();
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Page supprimée - Le Margo</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Cette page a été supprimée</h1>
    <p>La page que vous recherchez a été définitivement supprimée.</p>
    <p><a href="' . home_url() . '">Retour à l\'accueil du Margo</a></p>
</body>
</html>';
        exit;
    }
}
// Décommentez cette ligne si vous préférez un 410 au lieu d'une redirection 301
// add_action('template_redirect', 'le_margo_send_410_for_deleted_pages', 1);
?> 