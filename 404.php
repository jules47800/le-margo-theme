<?php
/**
 * La page 404 personnalisée du thème Le Margo
 * @package Le Margo
 */
get_header();
?>
<main id="primary" class="site-main">
    <section class="section error-404-section" style="min-height: 60vh; display: flex; align-items: center; justify-content: center; background: var(--color-beige-light);">
        <div class="container" style="text-align: center; max-width: 600px;">
            <div style="font-size: 6rem; font-weight: bold; color: var(--color-primary); margin-bottom: 10px;">404</div>
            <h1 style="font-size: 2.2rem; color: var(--color-secondary); margin-bottom: 20px;"><?php _e('Oups, cette page n\'existe pas !', 'le-margo'); ?></h1>
            <p style="font-size: 1.15rem; color: var(--color-gray); margin-bottom: 35px;">
                <?php _e('Il semblerait que la page que vous cherchez n\'existe plus ou n\'ait jamais existé.', 'le-margo'); ?><br>
                <?php _e('Pas de panique, revenez à l\'accueil pour continuer votre expérience gourmande chez', 'le-margo'); ?> <strong><?php _e('Le Margo', 'le-margo'); ?></strong> !
            </p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn" style="min-width: 200px; font-weight: 600; font-size: 1.1rem; padding: 14px 32px; background: var(--color-primary); color: #fff; border-radius: 8px; text-decoration: none; transition: background 0.2s;"><?php _e('Retour à l\'accueil', 'le-margo'); ?></a>
            <div style="margin-top: 40px;">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="Logo Le Margo" style="height: 60px; opacity: 0.7;">
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?> 