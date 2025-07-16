<?php
/**
 * Le Margo - Fonctions pour les types de publication personnalisés et taxonomies
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Types de publication personnalisés
 */
function le_margo_custom_post_types() {    
    // Type de publication pour les témoignages
    register_post_type(
        'testimonial',
        array(
            'labels' => array(
                'name'               => __('Témoignages', 'le-margo'),
                'singular_name'      => __('Témoignage', 'le-margo'),
                'menu_name'          => __('Témoignages', 'le-margo'),
                'add_new'            => __('Ajouter un témoignage', 'le-margo'),
                'add_new_item'       => __('Ajouter un nouveau témoignage', 'le-margo'),
                'edit_item'          => __('Modifier le témoignage', 'le-margo'),
                'new_item'           => __('Nouveau témoignage', 'le-margo'),
                'view_item'          => __('Voir le témoignage', 'le-margo'),
                'search_items'       => __('Rechercher des témoignages', 'le-margo'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'menu_icon'   => 'dashicons-format-quote',
            'rewrite'     => array('slug' => 'temoignages'),
        )
    );
    
    // Type de publication pour les menus - simplifié
    register_post_type(
        'daily_menu',
        array(
            'labels' => array(
                'name'               => __('Menus', 'le-margo'),
                'singular_name'      => __('Menu', 'le-margo'),
                'menu_name'          => __('Menus', 'le-margo'),
                'add_new'            => __('Ajouter un menu', 'le-margo'),
                'add_new_item'       => __('Ajouter un nouveau menu', 'le-margo'),
                'edit_item'          => __('Modifier le menu', 'le-margo'),
                'new_item'           => __('Nouveau menu', 'le-margo'),
                'view_item'          => __('Voir le menu', 'le-margo'),
                'search_items'       => __('Rechercher des menus', 'le-margo'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'supports'     => array('title', 'custom-fields'),
            'menu_icon'    => 'dashicons-media-document',
            'rewrite'      => array('slug' => 'menu'),
            'show_in_menu' => true,
        )
    );
}
add_action('init', 'le_margo_custom_post_types');

/**
 * Taxonomies personnalisées
 */
function le_margo_custom_taxonomies() {
    // Aucune taxonomie nécessaire
}
add_action('init', 'le_margo_custom_taxonomies'); 