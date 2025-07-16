<?php
/*
 * Template Name: Réserver
 * Page de réservation du restaurant Le Margo
 */
get_header();
?>
<main id="main" class="site-main">
    <header class="page-header">
        <h1 class="page-title">Réserver</h1>
        <p class="page-subtitle">Une table, un moment</p>
    </header>

    <section class="reservation-form-section">
        <div class="container">
            <?php get_template_part('template-parts/reservation-form'); ?>
        </div>
    </section>

    <section class="closing-section">
        <h2 class="section-title">Le Margo vous attend</h2>
        <p>Une cuisine qui raconte une histoire</p>
        <p>Des saveurs qui marquent les esprits</p>
        <p>Un moment qui devient souvenir</p>
    </section>
</main>
<?php get_footer(); ?> 