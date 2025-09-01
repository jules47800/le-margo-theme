<?php
/**
 * Le Margo - Fonctions de configuration de base du thème
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Configuration du thème
 */
function le_margo_setup() {
    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_theme_textdomain('le-margo', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
     * Let WordPress manage the document title.
     */
    add_theme_support('title-tag');

    /*
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support('post-thumbnails');

    // Support du logo personnalisé
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'le-margo'),
    ));
    
    // Support des images mises en avant
    add_theme_support('post-thumbnails');
    
    // Support pour le format d'image personnalisé
    add_image_size('le-margo-hero', 1920, 1080, true);
    add_image_size('le-margo-menu', 600, 400, true);
    add_image_size('le-margo-gallery-normal', 450, 450, true); // Pour les images normales
    add_image_size('le-margo-gallery-wide', 900, 450, true); // Pour les images larges
    add_image_size('le-margo-gallery-tall', 450, 900, true); // Pour les images hautes
    
    // Support de la traduction
    load_theme_textdomain('le-margo', get_template_directory() . '/languages');
    
    // Enregistrement des menus
    register_nav_menus(
        array(
            'menu-principal' => esc_html__('Menu Principal', 'le-margo'),
            'menu-footer' => esc_html__('Menu Pied de page', 'le-margo'),
        )
    );
    
    // Support HTML5
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Enregistrement des modèles de page
    add_theme_support('block-templates');
    
    // Ajout des modèles de page personnalisés
    add_filter('theme_page_templates', function($templates) {
        return array_merge($templates, array(
            'front-page.php' => __('Page d\'accueil', 'le-margo'),
            'page-reserver.php' => __('Page Réservation', 'le-margo'),
            'page-eymet.php' => __('Page Eymet', 'le-margo'),
        ));
    });
}
add_action('after_setup_theme', 'le_margo_setup'); 