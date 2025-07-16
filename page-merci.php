<?php
/**
 * Template pour la page de remerciement après réservation
 *
 * @package Le Margo
 */

// Sécurisation : on vérifie que l'utilisateur vient bien du processus de réservation.
// On vérifie le referer pour s'assurer qu'il vient du même site.
$referer = wp_get_referer();
if (!$referer || !str_contains($referer, home_url())) {
    // Si le referer est vide ou ne contient pas l'URL du site, on redirige.
    wp_redirect(home_url('/'));
    exit;
}

get_header();
?>

<main id="primary" class="site-main thank-you-page">
    <div class="thank-you-container">
        
        <div class="thank-you-header">
            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h1>Votre demande a bien été reçue !</h1>
            <p class="subtitle">Nous avons hâte de vous accueillir au Margo.</p>
        </div>
        
        <div class="thank-you-content">
            <div class="confirmation-details">
                <p>Un email de confirmation vient de vous être envoyé avec les détails de votre demande. Nous la traiterons dans les plus brefs délais.</p>
                <p>Pensez à vérifier votre dossier de courriers indésirables (spam) si vous ne le trouvez pas.</p>
            </div>
            
            <div class="next-steps">
                <p>En cas de question ou de modification, n'hésitez pas à nous appeler :</p>
                <a href="tel:<?php echo esc_attr(get_theme_mod('le_margo_restaurant_phone', '05 53 00 00 00')); ?>" class="phone-button">
                    <?php echo esc_html(get_theme_mod('le_margo_restaurant_phone', '05 53 00 00 00')); ?>
                </a>
            </div>
        </div>
        
        <div class="back-home-link">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="home-button">&larr; Retour à l'accueil</a>
        </div>
        
    </div>
</main>

<?php
get_footer(); 