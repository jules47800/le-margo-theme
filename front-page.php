<?php
/**
 * Page d'accueil - Style Chéri Bibi - Galerie pure
 * @package Le Margo
 */
get_header();
?>

<?php
// Récupérer les horaires depuis l'admin
$horaires = get_option('le_margo_opening_hours', []);

// Fonction pour formater les horaires
$format_creneaux = function($creneaux_str) {
    if (empty($creneaux_str)) return '';
    
    $creneaux = explode(',', $creneaux_str);
    $creneaux = array_map('trim', $creneaux);
    sort($creneaux);
    
    $groupes = [];
    $current_groupe = [];
    $last_time = null;
    
    foreach ($creneaux as $creneau) {
        $time = DateTime::createFromFormat('H:i', $creneau);
        if (!$time) continue;
        
        if ($last_time && $time->getTimestamp() - $last_time->getTimestamp() <= 900) {
            $current_groupe[] = $creneau;
        } else {
            if (!empty($current_groupe)) {
                $groupes[] = $current_groupe;
            }
            $current_groupe = [$creneau];
        }
        $last_time = $time;
    }
    
    if (!empty($current_groupe)) {
        $groupes[] = $current_groupe;
    }
    
    $formatted_groupes = [];
    foreach ($groupes as $groupe) {
        if (count($groupe) === 1) {
            $formatted_groupes[] = str_replace(':', 'h', $groupe[0]);
        } else {
            $debut = str_replace(':', 'h', $groupe[0]);
            $fin = str_replace(':', 'h', end($groupe));
            $formatted_groupes[] = $debut . ' - ' . $fin;
        }
    }
    
    return implode(' / ', $formatted_groupes);
};

// Créer le texte des horaires
$horaires_text = '';
$jours_ouverts = [];
foreach ($horaires as $jour => $creneaux) {
    if (!empty($creneaux)) {
        $formatted = $format_creneaux($creneaux);
        if (!empty($formatted)) {
            $jours_ouverts[] = ucfirst($jour);
        }
    }
}

if (!empty($jours_ouverts)) {
    if (count($jours_ouverts) > 1) {
        $derniers = array_pop($jours_ouverts);
        $horaires_text = implode(', ', $jours_ouverts) . ' et ' . $derniers;
    } else {
        $horaires_text = $jours_ouverts[0];
    }
}
?>

<section class="intro-cards-section">
    <div class="intro-cards-container">
        <div class="gallery-item hero-card">
            <div class="hero-card__content">
                <h1 class="hero-card__title"><?php echo esc_html(get_theme_mod('le_margo_restaurant_name', __('Le Margo', 'le-margo'))); ?></h1>
                <p class="hero-card__description"><?php _e('Restaurant Bistronomique à Eymet, Dordogne', 'le-margo'); ?></p>
                <p class="hero-card__subtitle">
                    <?php _e("Découvrez une cuisine locale et créative, élaborée à partir de produits frais et bio du Périgord, accompagnée d'une sélection de vins naturels.", 'le-margo'); ?>
                </p>
                
            </div>
            <div class="gallery-item contact-card">
                    <div class="contact-content">
                        <?php if (!empty($horaires_text)): ?>
                            <p class="horaires-jours"><?php echo $horaires_text; ?></p>
                            <?php 
                            // Afficher un exemple d'horaires (premier jour ouvert)
                            foreach ($horaires as $jour => $creneaux) {
                                if (!empty($creneaux)) {
                                    $formatted = $format_creneaux($creneaux);
                                    if (!empty($formatted)) {
                                        echo '<p class="horaires-details">' . $formatted . '</p>';
                                        break;
                                    }
                                }
                            }
                            ?>
                        <?php endif; ?>
                        <a href="<?php echo home_url('/reserver/'); ?>"><?php _e('Réserver', 'le-margo'); ?></a>
                        <a href="<?php echo esc_url(get_theme_mod('le_margo_instagram_url', 'https://instagram.com/lemargoeymet')); ?>" target="_blank" rel="noopener"><?php _e('Instagram', 'le-margo'); ?></a>
                    </div>
                </div>
        </div>

        <div class="gallery-item info-card">
            <div class="info-content">
                <p><?php echo nl2br(esc_html(get_theme_mod('le_margo_restaurant_address', "6 avenue du 6 juin 1944\n24500 Eymet"))); ?></p>
                <p><?php echo esc_html(get_theme_mod('le_margo_restaurant_phone', '05 53 00 00 00')); ?></p>
            </div>
        </div>

        
    </div>
</section>

<!-- Galerie style Chéri Bibi -->
<section class="photo-gallery">
    <div class="gallery-grid">
        <?php
        $gallery_images = get_option('le_margo_homepage_gallery', []);

        if (!empty($gallery_images)) {
            foreach ($gallery_images as $image) {
                $image_id = $image['id'];
                $shape = isset($image['shape']) ? $image['shape'] : 'normal';

                $class = 'gallery-item';
                $size = 'le-margo-gallery-normal'; // Taille par défaut

                if ($shape === 'tall') {
                    $class .= ' item-tall';
                    $size = 'le-margo-gallery-tall';
                } elseif ($shape === 'wide') {
                    $class .= ' item-wide';
                    $size = 'le-margo-gallery-wide';
                }

                // Récupérer l'alt de l'image pour l'accessibilité
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                if (empty($image_alt)) {
                    $image_alt = get_the_title($image_id);
                }

                // Utiliser wp_get_attachment_image pour une meilleure performance (srcset, lazy-load)
                $image_html = wp_get_attachment_image($image_id, $size, false, array('alt' => $image_alt));
                $full_image_url = wp_get_attachment_image_url($image_id, 'full');

                if ($image_html) {
                    echo '<div class="' . esc_attr($class) . '">';
                    // On enveloppe l'image dans un lien vers la version pleine taille
                    echo '<a href="' . esc_url($full_image_url) . '" class="gallery-image-link" data-full-src="' . esc_url($full_image_url) . '" data-alt="' . esc_attr($image_alt) . '">';
                    echo $image_html;
                    echo '</a>';
                    echo '</div>';
                }
            }
        } else {
            // Contenu de secours si la galerie est vide
            echo '<p>' . __('Veuillez configurer votre galerie dans les réglages du thème.', 'le-margo') . '</p>';
        }
        ?>
    </div>
</section>

<?php get_footer(); ?> 