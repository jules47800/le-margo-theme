<?php
/*
 * Template Name: Réserver
 * Page de réservation du restaurant Le Margo
 */
get_header();
?>
<main id="main" class="site-main">
    <header class="page-header">
        <h1 class="page-title"><?php _e('Réserver', 'le-margo'); ?></h1>
        <p class="page-subtitle"><?php _e('Une table, un moment', 'le-margo'); ?></p>
    </header>

    <section class="reservation-form-section">
        <div class="container">
            <?php get_template_part('template-parts/reservation-form'); ?>
        </div>
    </section>

    <section class="closing-section">
        <h2 class="section-title"><?php _e('Le Margo vous attend', 'le-margo'); ?></h2>
        <p><?php _e('Une cuisine qui raconte une histoire', 'le-margo'); ?></p>
        <p><?php _e('Des saveurs qui marquent les esprits', 'le-margo'); ?></p>
        <p><?php _e('Un moment qui devient souvenir', 'le-margo'); ?></p>
    </section>
</main>
<?php get_footer(); ?> 