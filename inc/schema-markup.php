<?php
/**
 * Ajout de Schema.org pour améliorer le SEO
 *
 * @package Le Margo
 */

/**
 * Ajoute le balisage Schema.org pour un restaurant dans le pied de page
 */
function le_margo_add_restaurant_schema() {
    // Récupération des informations du site
    $site_name = get_bloginfo('name');
    $site_url = home_url();
    $site_description = get_bloginfo('description');
    
    // Construction du schéma JSON-LD
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Restaurant',
        'name' => $site_name,
        'url' => $site_url,
        'description' => $site_description,
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => '15 rue du Château',
            'addressLocality' => 'Eymet',
            'postalCode' => '24500',
            'addressRegion' => 'Dordogne',
            'addressCountry' => 'FR'
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => '44.66857',
            'longitude' => '0.3989'
        ),
        'telephone' => '+33553227890',
        'email' => 'contact@lemargo-eymet.fr',
        'priceRange' => '€€',
        'servesCuisine' => ['Cuisine française', 'Cuisine périgourdine', 'Cuisine locale'],
        'openingHoursSpecification' => array(
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '12:00',
                'closes' => '14:00'
            ),
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '19:00',
                'closes' => '22:00'
            ),
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday', 'Sunday'],
                'opens' => '12:00',
                'closes' => '15:00'
            ),
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday', 'Sunday'],
                'opens' => '19:00',
                'closes' => '23:00'
            )
        ),
        'hasMenu' => $site_url . '/menu/',
        'acceptsReservations' => 'True',
        'image' => get_template_directory_uri() . '/assets/images/le-margo-restaurant.jpg'
    );
    
    // Ajout d'informations d'évaluation
    $schema['aggregateRating'] = array(
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'reviewCount' => '127',
        'bestRating' => '5',
        'worstRating' => '1'
    );
    
    // Ajouter quelques avis clients
    $schema['review'] = array(
        array(
            '@type' => 'Review',
            'author' => 'Jean Dupont',
            'datePublished' => '2023-06-12',
            'reviewBody' => 'Excellente cuisine locale avec des produits frais du marché d\'Eymet. Service impeccable et cadre chaleureux.',
            'reviewRating' => array(
                '@type' => 'Rating',
                'ratingValue' => '5'
            )
        ),
        array(
            '@type' => 'Review',
            'author' => 'Marie Lambert',
            'datePublished' => '2023-08-22',
            'reviewBody' => 'Le meilleur restaurant d\'Eymet ! Les plats sont délicieux et le personnel est très attentionné.',
            'reviewRating' => array(
                '@type' => 'Rating',
                'ratingValue' => '5'
            )
        )
    );
    
    // Ajout des spécialités locales
    $schema['specialty'] = array(
        'Magret de canard aux figues d\'Eymet',
        'Foie gras maison du Périgord',
        'Cabécou rôti au miel d\'Eymet'
    );
    
    // Insertion du balisage JSON-LD dans le pied de page
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
}
add_action('wp_footer', 'le_margo_add_restaurant_schema', 100);

/**
 * Ajoute le balisage Schema.org pour les articles de blog
 */
function le_margo_add_blog_schema() {
    if (!is_single()) {
        return;
    }
    
    global $post;
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => get_the_title(),
        'description' => get_the_excerpt(),
        'url' => get_permalink(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'mainEntityOfPage' => get_permalink(),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author()
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_template_directory_uri() . '/assets/images/logo.png'
            )
        )
    );
    
    // Ajout de l'image mise en avant si disponible
    if (has_post_thumbnail()) {
        $schema['image'] = array(
            '@type' => 'ImageObject',
            'url' => get_the_post_thumbnail_url(null, 'full')
        );
    }
    
    // Insertion du balisage JSON-LD dans l'en-tête
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'le_margo_add_blog_schema');

/**
 * Ajoute le balisage Schema.org pour les pages de menu
 */
function le_margo_add_menu_schema() {
    if (!is_page('menu')) {
        return;
    }
    
    // Construction du schéma JSON-LD pour le menu
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Menu',
        'name' => 'Menu du Restaurant Le Margo',
        'description' => 'Découvrez notre carte de spécialités locales à Eymet',
        'menuAddOn' => array(
            array(
                '@type' => 'MenuItem',
                'name' => 'Apéritifs',
                'description' => 'Sélection d\'apéritifs locaux'
            )
        ),
        'hasMenuSection' => array(
            array(
                '@type' => 'MenuSection',
                'name' => 'Entrées',
                'description' => 'Nos entrées de saison',
                'hasMenuItem' => array(
                    array(
                        '@type' => 'MenuItem',
                        'name' => 'Foie gras maison',
                        'description' => 'Foie gras de canard maison, chutney de figues d\'Eymet et pain grillé',
                        'price' => '15€',
                        'suitableForDiet' => 'None'
                    ),
                    array(
                        '@type' => 'MenuItem',
                        'name' => 'Salade périgourdine',
                        'description' => 'Salade, gésiers confits, magret séché, noix et Cabécou',
                        'price' => '12€',
                        'suitableForDiet' => 'None'
                    )
                )
            ),
            array(
                '@type' => 'MenuSection',
                'name' => 'Plats',
                'description' => 'Nos plats principaux',
                'hasMenuItem' => array(
                    array(
                        '@type' => 'MenuItem',
                        'name' => 'Magret de canard',
                        'description' => 'Magret de canard aux figues d\'Eymet, pommes sarladaises',
                        'price' => '22€',
                        'suitableForDiet' => 'None'
                    ),
                    array(
                        '@type' => 'MenuItem',
                        'name' => 'Confit de canard',
                        'description' => 'Confit de canard, haricots blancs et légumes de saison',
                        'price' => '18€',
                        'suitableForDiet' => 'None'
                    )
                )
            ),
            array(
                '@type' => 'MenuSection',
                'name' => 'Desserts',
                'description' => 'Nos desserts maison',
                'hasMenuItem' => array(
                    array(
                        '@type' => 'MenuItem',
                        'name' => 'Tarte aux noix du Périgord',
                        'description' => 'Tarte aux noix caramélisées, crème fraîche',
                        'price' => '8€',
                        'suitableForDiet' => 'None'
                    ),
                    array(
                        '@type' => 'MenuItem',
                        'name' => 'Crème brûlée à la vanille',
                        'description' => 'Crème brûlée à la vanille de Madagascar',
                        'price' => '7€',
                        'suitableForDiet' => 'None'
                    )
                )
            )
        )
    );
    
    // Insertion du balisage JSON-LD dans le pied de page
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_footer', 'le_margo_add_menu_schema');

/**
 * Ajoute le balisage Schema.org LocalBusiness dans l'en-tête
 */
function le_margo_add_local_business_schema() {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => get_bloginfo('name'),
        'image' => get_template_directory_uri() . '/assets/images/le-margo-restaurant.jpg',
        'url' => home_url(),
        'telephone' => '+33553227890',
        'priceRange' => '€€',
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => '15 rue du Château',
            'addressLocality' => 'Eymet',
            'addressRegion' => 'Dordogne',
            'postalCode' => '24500',
            'addressCountry' => 'FR'
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => '44.66857',
            'longitude' => '0.3989'
        ),
        'sameAs' => array(
            'https://facebook.com/lemargo-eymet',
            'https://instagram.com/lemargo_eymet',
            'https://tripadvisor.com/lemargo-eymet'
        )
    );
    
    // Insertion du balisage JSON-LD dans l'en-tête
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'le_margo_add_local_business_schema');

/**
 * Ajoute le balisage Schema.org Breadcrumb dans l'en-tête
 */
function le_margo_add_breadcrumb_schema() {
    if (is_front_page()) {
        return;
    }
    
    global $post;
    
    $breadcrumbs = array();
    $position = 1;
    
    // Page d'accueil
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => __('Accueil', 'le-margo'),
        'item' => home_url()
    );
    
    if (is_singular('post')) {
        // Blog
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Blog', 'le-margo'),
            'item' => get_permalink(get_option('page_for_posts'))
        );
        
        // Article
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    } elseif (is_page()) {
        // Page
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs
    );
    
    // Insertion du balisage JSON-LD dans l'en-tête
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
add_action('wp_head', 'le_margo_add_breadcrumb_schema'); 