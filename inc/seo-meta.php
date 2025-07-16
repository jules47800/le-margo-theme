<?php
/**
 * Gestion des balises meta et SEO pour Le Margo
 *
 * @package Le Margo
 */

/**
 * Ajouter des balises meta personnalisées pour l'optimisation SEO
 */
function le_margo_add_meta_tags() {
    // Balise canonical pour éviter les contenus dupliqués
    echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '" />' . "\n";
    
    // Meta language
    echo '<meta http-equiv="content-language" content="fr-fr" />' . "\n";
    
    // Meta pour les réseaux sociaux (Open Graph)
    echo '<meta property="og:locale" content="fr_FR" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '" />' . "\n";
    
    // Description pour Open Graph
    if (is_single() || is_page()) {
        if (has_excerpt()) {
            $description = get_the_excerpt();
        } else {
            $description = get_bloginfo('description');
        }
        echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
    }
    
    // URL pour Open Graph
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
    
    // Image pour Open Graph
    if (is_single() || is_page()) {
        if (has_post_thumbnail()) {
            $img_src = get_the_post_thumbnail_url(get_the_ID(), 'large');
            echo '<meta property="og:image" content="' . esc_url($img_src) . '" />' . "\n";
        } else {
            echo '<meta property="og:image" content="' . esc_url(get_template_directory_uri() . '/assets/images/le-margo-social.jpg') . '" />' . "\n";
        }
    }
    
    // Balises Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr(wp_get_document_title()) . '" />' . "\n";
    
    if (is_single() || is_page()) {
        if (has_excerpt()) {
            $description = get_the_excerpt();
        } else {
            $description = get_bloginfo('description');
        }
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
    }
    
    // Balises spécifiques pour Eymet
    if (is_front_page()) {
        echo '<meta name="geo.placename" content="Eymet, Dordogne, France" />' . "\n";
        echo '<meta name="geo.position" content="44.66857;0.3989" />' . "\n";
        echo '<meta name="geo.region" content="FR-24" />' . "\n";
        echo '<meta name="ICBM" content="44.66857, 0.3989" />' . "\n";
    }
}
add_action('wp_head', 'le_margo_add_meta_tags', 5);

/**
 * Personnaliser le titre de la page
 */
function le_margo_title_tag($title) {
    // Personnaliser le titre pour la page d'accueil
    if (is_front_page()) {
        return 'Le Margo | Le Meilleur Restaurant d\'Eymet, Dordogne | Cuisine Locale';
    }
    
    // Ajouter la localisation à chaque titre de page
    if (is_single() || is_page()) {
        if (!is_page('contact') && !is_page('mentions-legales')) {
            return $title . ' | Restaurant à Eymet, Dordogne';
        }
    }
    
    return $title;
}
add_filter('pre_get_document_title', 'le_margo_title_tag', 10);

/**
 * Ajouter des attributs alt aux images pour l'accessibilité et le SEO
 */
function le_margo_image_alt_tags($attr, $attachment) {
    // Si l'attribut alt est vide, utiliser le titre de l'image
    if (empty($attr['alt'])) {
        $attr['alt'] = get_the_title($attachment->ID);
        
        // Ajouter Eymet dans l'attribut alt pour les images pertinentes
        if (strpos(strtolower($attr['alt']), 'plat') !== false || 
            strpos(strtolower($attr['alt']), 'restaurant') !== false || 
            strpos(strtolower($attr['alt']), 'menu') !== false) {
            $attr['alt'] .= ' - Restaurant Le Margo à Eymet';
        }
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'le_margo_image_alt_tags', 10, 2);

/**
 * Optimiser les URLs pour le SEO
 */
function le_margo_optimize_permalinks() {
    global $wp_rewrite;
    
    // Supprimer la date des permaliens pour les articles
    $wp_rewrite->set_permalink_structure('/%postname%/');
    
    // Mettre à jour les règles de réécriture
    $wp_rewrite->flush_rules();
}
register_activation_hook(__FILE__, 'le_margo_optimize_permalinks');
// Commenter la ligne ci-dessous après la première exécution pour éviter de ralentir le site
// add_action('init', 'le_margo_optimize_permalinks');

/**
 * Ajouter des mots-clés localisés dans le contenu
 */
function le_margo_add_keywords_to_content($content) {
    if (is_single() || is_page()) {
        // Ne pas modifier les pages spécifiques
        if (is_page('contact') || is_page('mentions-legales')) {
            return $content;
        }
        
        // Ajouter une section "Informations pratiques" à la fin du contenu
        $additional_content = '<div class="local-info">';
        $additional_content .= '<h3>' . __('Informations Pratiques', 'le-margo') . '</h3>';
        $additional_content .= '<p>' . __('Le Margo est un restaurant situé au cœur d\'Eymet, une charmante bastide médiévale en Dordogne. Notre établissement propose une cuisine locale et authentique, élaborée à partir de produits frais de la région.', 'le-margo') . '</p>';
        $additional_content .= '<p>' . __('Vous cherchez où manger à Eymet ? Venez découvrir notre restaurant et profitez d\'un moment convivial dans un cadre chaleureux au centre-ville d\'Eymet.', 'le-margo') . '</p>';
        $additional_content .= '<p><strong>' . __('Adresse :', 'le-margo') . '</strong> 15 rue du Château, 24500 Eymet, Dordogne, France</p>';
        $additional_content .= '<p><strong>' . __('Réservation :', 'le-margo') . '</strong> <a href="tel:+33553227890">+33 5 53 22 78 90</a></p>';
        $additional_content .= '</div>';
        
        return $content . $additional_content;
    }
    
    return $content;
}
add_filter('the_content', 'le_margo_add_keywords_to_content');

/**
 * Améliorer le SEO local en ajoutant du microformat hCard
 */
function le_margo_add_hcard_footer() {
    ?>
    <div class="vcard" style="display:none">
        <span class="fn org">Le Margo</span>
        <span class="adr">
            <span class="street-address">15 rue du Château</span>,
            <span class="postal-code">24500</span>
            <span class="locality">Eymet</span>,
            <span class="region">Dordogne</span>,
            <span class="country-name">France</span>
        </span>
        <span class="tel">+33 5 53 22 78 90</span>
        <a class="url" href="<?php echo esc_url(home_url('/')); ?>">https://lemargo-eymet.fr</a>
        <span class="geo">
            <span class="latitude">44.66857</span>
            <span class="longitude">0.3989</span>
        </span>
    </div>
    <?php
}
add_action('wp_footer', 'le_margo_add_hcard_footer'); 