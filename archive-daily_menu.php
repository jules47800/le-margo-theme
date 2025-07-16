<?php
/**
 * Le modèle pour l'affichage des archives de menus
 *
 * @package Le Margo
 */

// Redirection permanente vers la page d'accueil
wp_redirect(home_url('/'));
exit;

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('Notre Menu', 'le-margo'); ?></h1>
        </header>

        <?php
        // Récupérer uniquement le menu le plus récent
        $args = array(
            'post_type'      => 'daily_menu',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        
        $menu_query = new WP_Query($args);
        
        if ($menu_query->have_posts()) :
            $menu_query->the_post();
            $menu_file = get_post_meta(get_the_ID(), '_menu_pdf', true);
        ?>
            <div class="menu-content">
                <div class="menu-info">
                    <div class="menu-prices">
                        <h2><?php esc_html_e('Nos Formules', 'le-margo'); ?></h2>
                        <div class="price-item">
                            <span class="price-title"><?php esc_html_e('Entrée + Plat + Dessert', 'le-margo'); ?></span>
                            <span class="price-value">26€</span>
                        </div>
                        <div class="price-item">
                            <span class="price-title"><?php esc_html_e('Entrée + Plat ou Plat + Dessert', 'le-margo'); ?></span>
                            <span class="price-value">21€</span>
                        </div>
                    </div>

                    <?php if ($menu_file) : ?>
                        <div class="menu-download">
                            <h2><?php esc_html_e('Menu du Jour', 'le-margo'); ?></h2>
                            <p><?php esc_html_e('Découvrez notre menu du jour, élaboré avec des produits frais et de saison.', 'le-margo'); ?></p>
                            <a href="<?php echo esc_url($menu_file); ?>" class="btn" download>
                                <?php esc_html_e('Télécharger le menu', 'le-margo'); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="menu-download">
                            <p class="no-menu"><?php esc_html_e('Le menu n\'est pas encore disponible.', 'le-margo'); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="menu-notes">
                        <h2><?php esc_html_e('Informations', 'le-margo'); ?></h2>
                        <ul>
                            <li><?php esc_html_e('Tous nos produits sont cuisinés maison', 'le-margo'); ?></li>
                            <li><?php esc_html_e('Produits de saison sourcés localement', 'le-margo'); ?></li>
                            <li><?php esc_html_e('Carte des vins naturels disponible', 'le-margo'); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-cta">
                    <a href="<?php echo esc_url(home_url('/reserver')); ?>" class="btn btn-primary">
                        <?php esc_html_e('Réserver une table', 'le-margo'); ?>
                    </a>
                </div>
            </div>
        <?php 
        else : 
        ?>
            <div class="no-menus">
                <p><?php esc_html_e('Aucun menu n\'est disponible pour le moment. Veuillez revenir bientôt.', 'le-margo'); ?></p>
            </div>
        <?php 
        endif;
        
        wp_reset_postdata();
        ?>
    </div>
</main><!-- #main -->

<?php
get_footer(); 