<?php
/**
 * Script d'application automatique des meta données SEO
 *
 * @package Le_Margo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuration des meta données par page
 */
function le_margo_get_seo_config() {
    return array(
        'front-page' => array(
            'title' => 'Le Margo | Restaurant à Eymet, Dordogne',
            'description' => 'Le Margo, restaurant à Eymet où cuisine du marché rime avec convivialité. Une carte qui évolue au fil des saisons, des produits locaux et une ambiance chaleureuse.'
        ),

        'page-menus' => array(
            'title' => 'La Carte | Le Margo - Restaurant à Eymet',
            'description' => 'Notre carte bistronomique : produits frais du marché, poissons du jour, viandes maturées. Une cuisine sincère qui respecte les saisons, dans une ambiance décontractée.'
        ),
        'page-galerie' => array(
            'title' => 'En Images | Le Margo - Restaurant Eymet',
            'description' => 'Découvrez l\'ambiance du Margo en images : cuisine bistronomique revisitée, produits du marché, moments conviviaux. Un restaurant qui cultive l\'art de recevoir à Eymet.'
        ),
        'page-reserver' => array(
            'title' => 'Réserver | Le Margo - Restaurant Eymet',
            'description' => 'Réservez votre table au Margo, restaurant d\'Eymet. Du mardi au samedi soir, venez partager un moment convivial autour d\'une cuisine bistronomique créative.'
        ),
        'page-eymet' => array(
            'title' => 'Le Margo à Eymet | Restaurant en Dordogne',
            'description' => 'Le Margo, votre restaurant au cœur d\'Eymet. Au 6 avenue du 6 juin 1944, savourez une cuisine bistronomique contemporaine dans un cadre chaleureux.'
        ),
        'archive-daily_menu' => array(
            'title' => 'Le Menu du Jour | Restaurant Le Margo Eymet',
            'description' => 'Notre ardoise du jour : une cuisine bistronomique généreuse qui suit le marché. Des plats fait maison qui changent chaque jour selon les arrivages et la saison.'
        ),
        'archive-testimonial' => array(
            'title' => 'Avis Clients | Restaurant Le Margo à Eymet',
            'description' => 'Les retours de nos clients sur leur expérience au Margo. Un restaurant apprécié pour sa cuisine sincère et son ambiance conviviale au cœur d\'Eymet.'
        ),
        'page-politique-confidentialite' => array(
            'title' => 'Politique de Confidentialité | Le Margo Eymet',
            'description' => 'Consultez notre politique de confidentialité. Le Margo s\'engage à protéger vos données personnelles conformément à la réglementation en vigueur.'
        ),
        'page-suppression-donnees' => array(
            'title' => 'Suppression des Données | Le Margo Eymet',
            'description' => 'Informations sur la suppression de vos données personnelles au Margo. Nous respectons votre droit à la vie privée.'
        ),
        '404' => array(
            'title' => 'Page Non Trouvée | Le Margo - Restaurant Eymet',
            'description' => 'La page que vous recherchez n\'existe plus. Découvrez notre carte bistronomique et nos suggestions du jour sur le site du Margo à Eymet.'
        )
    );
}

/**
 * Applique les meta données à une page
 */
function le_margo_apply_seo_meta($post_id, $template_name) {
    $config = le_margo_get_seo_config();
    
    // Retire le .php de la fin du nom du template
    $template_name = str_replace('.php', '', $template_name);
    
    if (isset($config[$template_name])) {
        update_post_meta($post_id, '_le_margo_meta_title', $config[$template_name]['title']);
        update_post_meta($post_id, '_le_margo_meta_description', $config[$template_name]['description']);
    }
}

/**
 * Applique les meta données lors de la sauvegarde d'une page
 */
function le_margo_auto_apply_seo_meta($post_id) {
    // Vérifie si c'est une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Vérifie si c'est le bon type de contenu
    if (!in_array(get_post_type($post_id), array('page', 'post'))) {
        return;
    }

    // Récupère le template de la page
    $template = get_page_template_slug($post_id);
    
    // Si pas de template spécifique mais c'est la page d'accueil
    if (empty($template) && get_option('page_on_front') == $post_id) {
        $template = 'front-page';
    }
    
    if ($template) {
        le_margo_apply_seo_meta($post_id, $template);
    }
}
add_action('save_post', 'le_margo_auto_apply_seo_meta');

/**
 * Applique les meta données à toutes les pages existantes
 */
function le_margo_apply_seo_to_all_pages() {
    $pages = get_pages();
    
    foreach ($pages as $page) {
        $template = get_page_template_slug($page->ID);
        
        // Gestion spéciale pour la page d'accueil
        if (empty($template) && get_option('page_on_front') == $page->ID) {
            $template = 'front-page';
        }
        
        if ($template) {
            le_margo_apply_seo_meta($page->ID, $template);
        }
    }
}

// Fonction pour appliquer manuellement les meta données à toutes les pages
function le_margo_manual_apply_seo() {
    if (current_user_can('manage_options')) {
        le_margo_apply_seo_to_all_pages();
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Les meta données SEO ont été appliquées avec succès à toutes les pages.</p></div>';
        });
    }
}

// Ajoute un bouton dans l'admin pour appliquer les meta données
function le_margo_add_seo_button() {
    if (current_user_can('manage_options')) {
        add_management_page(
            'Appliquer les Meta SEO',
            'Appliquer les Meta SEO',
            'manage_options',
            'apply-seo-meta',
            function() {
                if (isset($_POST['apply_seo'])) {
                    le_margo_manual_apply_seo();
                }
                ?>
                <div class="wrap">
                    <h1>Appliquer les Meta Données SEO</h1>
                    <form method="post">
                        <p>Cliquez sur le bouton ci-dessous pour appliquer automatiquement les meta données SEO à toutes vos pages.</p>
                        <input type="submit" name="apply_seo" class="button button-primary" value="Appliquer les Meta Données">
                    </form>
                </div>
                <?php
            }
        );
    }
}
add_action('admin_menu', 'le_margo_add_seo_button'); 