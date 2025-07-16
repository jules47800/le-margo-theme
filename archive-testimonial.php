<?php
/**
 * Le modèle pour l'affichage des archives de témoignages
 *
 * @package Le Margo
 */

get_header();
?>

<main id="primary" class="site-main">

    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Témoignages de nos clients', 'le-margo'); ?></h1>
        <div class="archive-description">
            <p><?php esc_html_e('Découvrez ce que nos clients disent de leur expérience au Margo.', 'le-margo'); ?></p>
        </div>
    </header><!-- .page-header -->

    <?php if (have_posts()) : ?>

        <div class="testimonials-container">
            <?php
            /* Commencer la boucle */
            while (have_posts()) :
                the_post();

                /*
                 * Inclure le template pour le témoignage
                 */
                get_template_part('template-parts/content', 'testimonial');

            endwhile;
            ?>
        </div>

        <?php
        // Pagination
        le_margo_pagination();

    else :

        get_template_part('template-parts/content', 'none');

    endif;
    ?>

</main><!-- #main -->

<?php
get_footer(); 