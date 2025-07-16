<?php
/**
 * Template Name: Galerie Photos
 * Description: Galerie complète des photos du restaurant Le Margo
 *
 * @package Le Margo
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- En-tête de page -->
    <section class="page-header" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/restaurant-ambiance.jpg'); ?>');">
        <div class="page-header-overlay"></div>
        <div class="container page-header-content">
            <h1 class="page-title"><?php echo esc_html__('Galerie Photos', 'le-margo'); ?></h1>
            <p class="subtitle"><?php echo esc_html__('Découvrez l\'univers du Margo en images', 'le-margo'); ?></p>
        </div>
    </section>

    <!-- Section Galerie Complète -->
    <section class="section gallery-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?php echo esc_html__('Notre univers', 'le-margo'); ?></span>
                <h2 class="section-title"><?php echo esc_html__('Le Margo en images', 'le-margo'); ?></h2>
            </div>
            
            <!-- Galerie Restaurant -->
            <div class="gallery-category">
                <h3><?php echo esc_html__('Le Restaurant', 'le-margo'); ?></h3>
                <div class="gallery-grid">
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/restaurant-exterieur-eymet.jpg'); ?>" alt="<?php echo esc_attr__('Extérieur du restaurant Le Margo', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Façade du restaurant', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Notre belle façade au cœur d\'Eymet', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/restaurant-interieur-ambiance.jpg'); ?>" alt="<?php echo esc_attr__('Intérieur du restaurant', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Ambiance intérieure', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Un décor artistique et chaleureux', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/salle-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Salle du restaurant', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Salle principale', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Espace convivial pour vos repas', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/terrasse-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Terrasse du restaurant', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Terrasse extérieure', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Profitez des beaux jours en terrasse', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/ambiance-soiree-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Ambiance de soirée', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Ambiance de soirée', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Une atmosphère intimiste le soir', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/table-dressage-elegant.jpg'); ?>" alt="<?php echo esc_attr__('Dressage de table', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Dressage de table', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Élégance dans chaque détail', 'le-margo'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galerie Cuisine -->
            <div class="gallery-category">
                <h3><?php echo esc_html__('La Cuisine', 'le-margo'); ?></h3>
                <div class="gallery-grid">
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/cuisine-ouverte-lemargo.jpg'); ?>" alt="<?php echo esc_attr__('Cuisine ouverte', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Cuisine ouverte', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Transparence et créativité culinaire', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/chef-cuisine-preparation.jpg'); ?>" alt="<?php echo esc_attr__('Chef en préparation', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Chef en action', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Passion et savoir-faire', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/cuisine-preparation.jpg'); ?>" alt="<?php echo esc_attr__('Préparation culinaire', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Préparation culinaire', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Chaque plat est une œuvre d\'art', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/cuisine-moderne-equipement.jpg'); ?>" alt="<?php echo esc_attr__('Équipement moderne', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Équipement moderne', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Technologie au service du goût', 'le-margo'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galerie Plats -->
            <div class="gallery-category">
                <h3><?php echo esc_html__('Nos Plats', 'le-margo'); ?></h3>
                <div class="gallery-grid">
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-signature-lemargo.webp'); ?>" alt="<?php echo esc_attr__('Plat signature', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Plat signature Le Margo', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Notre création emblématique', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-creative-cuisine.jpg'); ?>" alt="<?php echo esc_attr__('Cuisine créative', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Cuisine créative', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Innovation et tradition réunies', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-gastronomique.jpg'); ?>" alt="<?php echo esc_attr__('Plat gastronomique', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Plat gastronomique', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Excellence culinaire française', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-signature-chef.jpg'); ?>" alt="<?php echo esc_attr__('Signature du chef', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Signature du chef', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Vision unique du chef', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-local-ingredients.jpg'); ?>" alt="<?php echo esc_attr__('Ingrédients locaux', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Ingrédients locaux', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Terroir du Périgord sublimé', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-seasonal-menu.jpg'); ?>" alt="<?php echo esc_attr__('Menu de saison', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Menu de saison', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Fraîcheur et saisonnalité', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/dessert-maison.jpg'); ?>" alt="<?php echo esc_attr__('Dessert maison', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Dessert fait maison', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Douceur et gourmandise', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/dressage-plat-gastronomique.jpg'); ?>" alt="<?php echo esc_attr__('Dressage gastronomique', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Dressage gastronomique', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Art de l\'assiette', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/menu-degustation-presentation.jpg'); ?>" alt="<?php echo esc_attr__('Menu dégustation', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Menu dégustation', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Voyage culinaire complet', 'le-margo'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galerie Vins -->
            <div class="gallery-category">
                <h3><?php echo esc_html__('Nos Vins', 'le-margo'); ?></h3>
                <div class="gallery-grid">
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/vins-naturels-selection.webp'); ?>" alt="<?php echo esc_attr__('Sélection de vins naturels', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Sélection de vins naturels', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Vignerons passionnés et nature', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/bar-vins-naturels.webp'); ?>" alt="<?php echo esc_attr__('Bar à vins', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Bar à vins naturels', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Espace dégustation convivial', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/bar-vins-naturels.jpg'); ?>" alt="<?php echo esc_attr__('Cave à vins', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Cave à vins', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Collection soigneusement choisie', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/cave-vins-selection.jpg'); ?>" alt="<?php echo esc_attr__('Cave et sélection', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Cave et sélection', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Découvertes et grands crus', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/vin-degustation-naturel.webp'); ?>" alt="<?php echo esc_attr__('Dégustation de vins', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Dégustation de vins', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Expérience œnologique unique', 'le-margo'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galerie Produits et Équipe -->
            <div class="gallery-category">
                <h3><?php echo esc_html__('Produits & Équipe', 'le-margo'); ?></h3>
                <div class="gallery-grid">
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/produits-locaux.jpg'); ?>" alt="<?php echo esc_attr__('Produits locaux', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Produits locaux', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Circuit court et qualité', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/ingredients-frais-locaux.jpg'); ?>" alt="<?php echo esc_attr__('Ingrédients frais', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Ingrédients frais et locaux', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Fraîcheur garantie du producteur', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/founders.webp'); ?>" alt="<?php echo esc_attr__('Fondateurs', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Antoine et Floriane', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Fondateurs passionnés du Margo', 'le-margo'); ?></p>
                        </div>
                    </div>
                    <div class="zoomable-image">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/equipe-service-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Équipe de service', 'le-margo'); ?>">
                        <div class="particles"></div>
                        <div class="image-caption">
                            <h4><?php echo esc_html__('Équipe de service', 'le-margo'); ?></h4>
                            <p><?php echo esc_html__('Accueil chaleureux et professionnel', 'le-margo'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Réservation -->
    <section class="section reservation-section">
        <div class="container">
            <div class="reservation-content">
                <h2><?php echo esc_html__('Venez découvrir Le Margo', 'le-margo'); ?></h2>
                <p><?php echo esc_html__('Réservez votre table pour vivre une expérience culinaire unique dans notre restaurant.', 'le-margo'); ?></p>
                <div class="reservation-buttons">
                    <a href="<?php echo esc_url(home_url('/reserver')); ?>" class="btn" style="min-width: 200px; font-weight: 600;"><?php echo esc_html__('Réserver une table', 'le-margo'); ?></a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.gallery-category {
    margin-bottom: var(--spacing-xl);
}

.gallery-category h3 {
    font-size: 1.8rem;
    color: var(--color-secondary);
    margin-bottom: var(--spacing-lg);
    text-align: center;
    position: relative;
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
}

.gallery-category h3:after {
    content: '';
    display: block;
    width: 60px;
    height: 2px;
    background-color: var(--color-primary);
    margin: 10px auto;
    transform: scaleX(0);
    animation: expandLine 0.8s ease 0.3s forwards;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.zoomable-image {
    position: relative;
    overflow: hidden;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    background-color: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: translateY(30px);
    animation: slideInUp 0.6s ease forwards;
    cursor: pointer;
    height: 300px;
}

/* Animation en cascade pour les images */
.zoomable-image:nth-child(1) { animation-delay: 0.1s; }
.zoomable-image:nth-child(2) { animation-delay: 0.2s; }
.zoomable-image:nth-child(3) { animation-delay: 0.3s; }
.zoomable-image:nth-child(4) { animation-delay: 0.4s; }
.zoomable-image:nth-child(5) { animation-delay: 0.5s; }
.zoomable-image:nth-child(6) { animation-delay: 0.6s; }
.zoomable-image:nth-child(7) { animation-delay: 0.7s; }
.zoomable-image:nth-child(8) { animation-delay: 0.8s; }
.zoomable-image:nth-child(9) { animation-delay: 0.9s; }

.zoomable-image:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

/* Overlay créatif avec effet de masque */
.zoomable-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, 
        rgba(181, 166, 146, 0.85) 0%,
        rgba(139, 116, 88, 0.9) 50%,
        rgba(181, 166, 146, 0.85) 100%);
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    z-index: 2;
    backdrop-filter: blur(0px);
}

.zoomable-image:hover::before {
    opacity: 1;
    backdrop-filter: blur(2px);
}

/* Effet de bordure animée */
.zoomable-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 3px solid var(--color-primary);
    border-radius: 16px;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    z-index: 3;
}

.zoomable-image:hover::after {
    opacity: 1;
    transform: scale(1);
}

.zoomable-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center center;
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    display: block;
    filter: brightness(1) contrast(1) saturate(1);
}

.zoomable-image:hover img {
    transform: scale(1.1);
    filter: brightness(1.1) contrast(1.2) saturate(1.1);
}

/* Nouveau design pour la légende */
.image-caption {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    z-index: 4;
    opacity: 0;
    transition: opacity 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    padding: 20px;
}

.image-caption::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80px;
    height: 80px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

.zoomable-image:hover .image-caption::before {
    transform: translate(-50%, -50%) scale(1);
    animation: pulse 2s infinite;
}

.image-caption h4 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 8px 0;
    opacity: 0;
    transition: opacity 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.1s;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    text-align: center !important;
    width: 100%;
    display: block;
}

.image-caption p {
    font-size: 0.9rem;
    font-weight: 400;
    margin: 0;
    opacity: 0;
    transition: opacity 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.2s;
    text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    text-align: center !important;
    width: 100%;
    line-height: 1.4;
    display: block;
}

.zoomable-image:hover .image-caption {
    opacity: 1;
}

.zoomable-image:hover .image-caption h4,
.zoomable-image:hover .image-caption p {
    opacity: 1;
}

/* Effet de particules dorées */
.zoomable-image .particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.zoomable-image .particles::before,
.zoomable-image .particles::after {
    content: '';
    position: absolute;
    width: 4px;
    height: 4px;
    background: var(--color-primary);
    border-radius: 50%;
    opacity: 0;
    transition: all 0.6s ease;
}

.zoomable-image .particles::before {
    top: 20%;
    left: 20%;
    animation-delay: 0.2s;
}

.zoomable-image .particles::after {
    bottom: 20%;
    right: 20%;
    animation-delay: 0.4s;
}

.zoomable-image:hover .particles::before,
.zoomable-image:hover .particles::after {
    opacity: 1;
    animation: sparkle 1.5s ease-in-out infinite;
}

/* Animations keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes expandLine {
    from {
        transform: scaleX(0);
    }
    to {
        transform: scaleX(1);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.1;
    }
}

@keyframes sparkle {
    0%, 100% {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
    50% {
        transform: translateY(-10px) scale(1.5);
        opacity: 0.7;
    }
}

/* Amélioration de l'espacement général */
.section-header {
    margin-bottom: 3rem;
}

.gallery-category:last-child {
    margin-bottom: 2rem;
}

/* Responsivité améliorée */
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-sm);
    }
    
    .zoomable-image {
        border-radius: 12px;
        height: 250px;
    }
    
    .zoomable-image:hover {
        transform: translateY(-8px) scale(1.01);
    }
    
    .image-caption h4 {
        font-size: 1.1rem;
    }
    
    .image-caption p {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-sm);
    }
    
    .zoomable-image {
        border-radius: 10px;
        height: 220px;
    }
    
    .zoomable-image:hover {
        transform: translateY(-5px) scale(1);
    }
    
    .image-caption h4 {
        font-size: 1rem;
    }
    
    .image-caption p {
        font-size: 0.8rem;
    }
}

/* Ajustement pour les conteneurs de section */
.gallery-section .container {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

/* Amélioration de la performance */
.zoomable-image {
    will-change: transform;
}

.zoomable-image img {
    will-change: transform, filter;
}
</style>

<?php
get_footer(); 