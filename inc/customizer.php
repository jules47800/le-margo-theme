<?php
/**
 * Le Margo Theme Customizer
 *
 * @package Le Margo
 */

if (!defined('ABSPATH')) {
    exit; // Ne pas autoriser l'accès direct
}

/**
 * Ajouter les sections, réglages et contrôles au Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Manager de personnalisation de WordPress.
 */
function le_margo_customize_register($wp_customize) {
    
    // 1. Section des couleurs
    $wp_customize->add_section('le_margo_colors_section', array(
        'title'      => __('Couleurs du Thème', 'le-margo'),
        'priority'   => 30,
        'description' => __('Gérez ici les couleurs principales du site.', 'le-margo'),
    ));

    // Section Informations du Restaurant
    $wp_customize->add_section('le_margo_restaurant_info_section', array(
        'title'      => __('Informations du Restaurant', 'le-margo'),
        'priority'   => 25,
        'description' => __('Gérez ici les informations principales du restaurant.', 'le-margo'),
    ));

    // Nom du restaurant
    $wp_customize->add_setting('le_margo_restaurant_name', array(
        'default'   => 'Le Margo',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('le_margo_restaurant_name_control', array(
        'label'    => __('Nom du Restaurant', 'le-margo'),
        'section'  => 'le_margo_restaurant_info_section',
        'settings' => 'le_margo_restaurant_name',
        'type'     => 'text',
    ));

    // Téléphone
    $wp_customize->add_setting('le_margo_restaurant_phone', array(
        'default'   => '05 53 00 00 00',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('le_margo_restaurant_phone_control', array(
        'label'    => __('Numéro de Téléphone', 'le-margo'),
        'section'  => 'le_margo_restaurant_info_section',
        'settings' => 'le_margo_restaurant_phone',
        'type'     => 'text',
    ));

    // Adresse
    $wp_customize->add_setting('le_margo_restaurant_address', array(
        'default'   => '6 avenue du 6 juin 1944, 24500 Eymet',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('le_margo_restaurant_address_control', array(
        'label'    => __('Adresse', 'le-margo'),
        'section'  => 'le_margo_restaurant_info_section',
        'settings' => 'le_margo_restaurant_address',
        'type'     => 'textarea',
    ));

    // SIRET
    $wp_customize->add_setting('le_margo_restaurant_siret', array(
        'default'   => '987 558 673',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('le_margo_restaurant_siret_control', array(
        'label'    => __('Numéro SIRET', 'le-margo'),
        'section'  => 'le_margo_restaurant_info_section',
        'settings' => 'le_margo_restaurant_siret',
        'type'     => 'text',
    ));

    // Liste des couleurs à ajouter
    $colors = array(
        '--color-black'       => array('label' => __('Couleur Texte Principal', 'le-margo'), 'default' => '#1a1a1a'),
        '--color-white'       => array('label' => __('Fond Principal (Blanc)', 'le-margo'), 'default' => '#fefefe'),
        '--color-beige'       => array('label' => __('Arrière-plan (Beige)', 'le-margo'), 'default' => '#f4f1eb'),
        '--color-beige-dark'  => array('label' => __('Beige Foncé (Bordures)', 'le-margo'), 'default' => '#e8e3d9'),
        '--color-warm-gray'   => array('label' => __('Texte Secondaire (Gris)', 'le-margo'), 'default' => '#8b8680'),
        '--color-dark-brown'  => array('label' => __('Couleur d\'Accent (Brun)', 'le-margo'), 'default' => '#2d2824'),
    );

    foreach ($colors as $variable => $details) {
        // Nettoyer le nom de la variable pour l'utiliser comme ID
        $setting_id = str_replace(['--', '-'], ['', '_'], $variable);

        // Réglage (Setting)
        $wp_customize->add_setting($setting_id, array(
            'default'   => $details['default'],
            'transport' => 'refresh', // ou 'postMessage' pour un aperçu en direct plus avancé
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        // Contrôle (Control)
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $setting_id . '_control', array(
            'label'      => $details['label'],
            'section'    => 'le_margo_colors_section',
            'settings'   => $setting_id,
        )));
    }

    // 2. Section Typographie
    $wp_customize->add_section('le_margo_typography_section', array(
        'title'      => __('Typographie', 'le-margo'),
        'priority'   => 35,
        'description' => __('Gérez les polices et tailles de texte.', 'le-margo'),
    ));

    // Réglage pour la police principale
    $wp_customize->add_setting('le_margo_primary_font', array(
        'default'   => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Contrôle pour la police principale
    $wp_customize->add_control('le_margo_primary_font_control', array(
        'label'    => __('Police Principale', 'le-margo'),
        'section'  => 'le_margo_typography_section',
        'settings' => 'le_margo_primary_font',
        'type'     => 'select',
        'choices'  => array(
            'Inter' => 'Inter (moderne, sans-serif)',
            'Poppins' => 'Poppins (géométrique, sans-serif)',
            'Lato' => 'Lato (chaleureux, sans-serif)',
            'Lora' => 'Lora (élégant, serif)',
            'Cormorant Garamond' => 'Cormorant Garamond (raffiné, serif)',
            'Playfair Display' => 'Playfair Display (classique, serif)',
        ),
    ));

    // --- Tailles de police ---

    // Taille de la police de base
    $wp_customize->add_setting('le_margo_body_font_size', array( 'default'   => '16', 'sanitize_callback' => 'absint' ));
    $wp_customize->add_control('le_margo_body_font_size_control', array(
        'label' => __('Taille texte de corps (px)', 'le-margo'),
        'section'  => 'le_margo_typography_section', 'settings' => 'le_margo_body_font_size', 'type' => 'number',
        'input_attrs' => array('min' => 12, 'max' => 22, 'step' => 1),
    ));

    // Taille des titres principaux (h1)
    $wp_customize->add_setting('le_margo_h1_font_size', array( 'default' => '56', 'sanitize_callback' => 'absint' ));
    $wp_customize->add_control('le_margo_h1_font_size_control', array(
        'label' => __('Taille Titre Principal (H1 en px)', 'le-margo'),
        'section'  => 'le_margo_typography_section', 'settings' => 'le_margo_h1_font_size', 'type' => 'number',
        'input_attrs' => array('min' => 24, 'max' => 90, 'step' => 1),
    ));

    // Taille de la description du Hero
    $wp_customize->add_setting('le_margo_hero_desc_font_size', array( 'default' => '24', 'sanitize_callback' => 'absint' ));
    $wp_customize->add_control('le_margo_hero_desc_font_size_control', array(
        'label' => __('Taille description page d\'accueil (px)', 'le-margo'),
        'section'  => 'le_margo_typography_section', 'settings' => 'le_margo_hero_desc_font_size', 'type' => 'number',
        'input_attrs' => array('min' => 16, 'max' => 40, 'step' => 1),
    ));
}
add_action('customize_register', 'le_margo_customize_register');

/**
 * Générer le CSS à partir des réglages du Customizer et l'injecter dans l'en-tête.
 */
function le_margo_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            <?php
            $colors = array(
                '--color-black' => 'color_black',
                '--color-white' => 'color_white',
                '--color-beige' => 'color_beige',
                '--color-beige-dark' => 'color_beige_dark',
                '--color-warm-gray' => 'color_warm_gray',
                '--color-dark-brown' => 'color_dark_brown',
            );

            foreach ($colors as $variable => $setting_id) {
                $default_value = get_theme_mod($setting_id . '_default', ''); // Récupérer la valeur par défaut si elle existe
                $value = get_theme_mod($setting_id, $default_value);
                if (!empty($value)) {
                    echo esc_html($variable) . ': ' . esc_html($value) . ';';
                }
            }

            // Variables de typographie
            $primary_font = get_theme_mod('le_margo_primary_font', 'Inter');
            $body_font_size = get_theme_mod('le_margo_body_font_size', '16');
            $h1_font_size = get_theme_mod('le_margo_h1_font_size', '56');
            $hero_desc_font_size = get_theme_mod('le_margo_hero_desc_font_size', '24');
            
            echo '--font-primary: "' . esc_html($primary_font) . '", sans-serif;';
            
            // Appliquer les tailles de police
            echo '--font-size-base: ' . esc_html($body_font_size) . 'px;';
            echo '--font-size-h1: ' . esc_html($h1_font_size) . 'px;';
            echo '--font-size-hero-desc: ' . esc_html($hero_desc_font_size) . 'px;';
             ?>
        }

        html {
            font-size: var(--font-size-base);
        }

        h1, .hero-card__title {
            font-size: var(--font-size-h1);
        }

        .hero-card__description {
            font-size: var(--font-size-hero-desc);
        }
    </style>
    <?php
}
add_action('wp_head', 'le_margo_customizer_css');

/**
 * Inclure le nouveau fichier customizer.php
 */
// Cette fonction sera ajoutée à functions.php 