<?php
/**
 * Le Margo - Fonctions pour l'enregistrement des widgets
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Enregistrement des widgets
 */
function le_margo_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__('Barre latérale', 'le-margo'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Ajoutez vos widgets ici.', 'le-margo'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
    
    register_sidebar(
        array(
            'name'          => esc_html__('Pied de page', 'le-margo'),
            'id'            => 'footer-1',
            'description'   => esc_html__('Zone de widgets pour le pied de page.', 'le-margo'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );
}
add_action('widgets_init', 'le_margo_widgets_init'); 