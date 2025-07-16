<?php
/**
 * Template pour la page "À propos"
 *
 * @package Le Margo
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- En-tête de page -->
    <section class="page-header" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/restaurant-exterieur-eymet.jpg'); ?>');">
        <div class="page-header-overlay"></div>
        <div class="container page-header-content">
            <h1 class="page-title"><?php echo esc_html__('À Propos', 'le-margo'); ?></h1>
            <p class="subtitle"><?php echo esc_html__('Découvrez l\'histoire et la philosophie derrière Le Margo', 'le-margo'); ?></p>
        </div>
    </section>

    <!-- Section Notre Histoire -->
    <section class="section about-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?php echo esc_html__('Notre histoire', 'le-margo'); ?></span>
                <h2 class="section-title"><?php echo esc_html__('Une aventure gastronomique', 'le-margo'); ?></h2>
            </div>
            <div class="about-grid">
                <div class="about-text">
                    <p><?php echo esc_html__('Le Margo est né de la passion de deux amis pour la gastronomie locale et les vins naturels. Situé au cœur d\'Eymet, notre restaurant a ouvert ses portes en 2020 avec une vision claire : proposer une cuisine authentique qui met en valeur les produits du terroir.', 'le-margo'); ?></p>
                    <p><?php echo esc_html__('Le nom "Le Margo" est un clin d\'œil à notre chat mascotte qui nous accompagne depuis le début de cette aventure et qui a su conquérir le cœur de notre clientèle.', 'le-margo'); ?></p>
                    <p><?php echo esc_html__('Au fil des années, nous avons développé des relations étroites avec les producteurs locaux, créant ainsi un écosystème durable qui soutient l\'économie locale tout en offrant à nos clients une expérience gastronomique exceptionnelle.', 'le-margo'); ?></p>
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/restaurant-interieur-ambiance.jpg'); ?>" alt="<?php echo esc_attr__('Intérieur du restaurant Le Margo', 'le-margo'); ?>">
                </div>
            </div>
        </div>
    </section>

    <!-- Section Notre Philosophie -->
    <section class="section philosophy-section" style="background-color: var(--color-beige-light);">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?php echo esc_html__('Notre philosophie', 'le-margo'); ?></span>
                <h2 class="section-title"><?php echo esc_html__('Cuisine locale, vins naturels', 'le-margo'); ?></h2>
            </div>
            <div class="philosophy-grid">
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-bg.jpg'); ?>" alt="<?php echo esc_attr__('Cuisine du Margo', 'le-margo'); ?>">
                </div>
                <div class="philosophy-text">
                    <h3><?php echo esc_html__('Les piliers de notre cuisine', 'le-margo'); ?></h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 15px; padding-left: 30px; position: relative;">
                            <span style="position: absolute; left: 0; top: 0;">🌱</span>
                            <strong><?php echo esc_html__('Produits locaux :', 'le-margo'); ?></strong> 
                            <?php echo esc_html__('Nous collaborons avec des producteurs situés dans un rayon de 50 km autour d\'Eymet.', 'le-margo'); ?>
                        </li>
                        <li style="margin-bottom: 15px; padding-left: 30px; position: relative;">
                            <span style="position: absolute; left: 0; top: 0;">🍷</span>
                            <strong><?php echo esc_html__('Vins naturels :', 'le-margo'); ?></strong> 
                            <?php echo esc_html__('Notre carte des vins met à l\'honneur des vignerons qui travaillent dans le respect de la nature.', 'le-margo'); ?>
                        </li>
                        <li style="margin-bottom: 15px; padding-left: 30px; position: relative;">
                            <span style="position: absolute; left: 0; top: 0;">🔄</span>
                            <strong><?php echo esc_html__('Carte saisonnière :', 'le-margo'); ?></strong> 
                            <?php echo esc_html__('Notre menu évolue au rythme des saisons pour garantir fraîcheur et créativité.', 'le-margo'); ?>
                        </li>
                        <li style="margin-bottom: 15px; padding-left: 30px; position: relative;">
                            <span style="position: absolute; left: 0; top: 0;">♻️</span>
                            <strong><?php echo esc_html__('Démarche éco-responsable :', 'le-margo'); ?></strong> 
                            <?php echo esc_html__('Nous limitons nos déchets et privilégions les emballages recyclables.', 'le-margo'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Notre Équipe -->
    <section class="section team-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?php echo esc_html__('L\'équipe', 'le-margo'); ?></span>
                <h2 class="section-title"><?php echo esc_html__('Les créateurs du Margo', 'le-margo'); ?></h2>
            </div>
            <div class="team-content">
                <div class="founders-presentation">
                    <div class="founders-image">
                        <div class="team-photo">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/founders.webp'); ?>" alt="<?php echo esc_attr__('Antoine Bursens et Floriane Valladon', 'le-margo'); ?>">
                        </div>
                    </div>
                    <div class="founders-info">
                        <div class="founders-names">
                            <h3><?php echo esc_html__('Antoine Bursens', 'le-margo'); ?> <span class="founders-separator">&</span> <?php echo esc_html__('Floriane Valladon', 'le-margo'); ?></h3>
                        </div>
                        <p class="founders-description">
                            <?php echo esc_html__('Passionnés de gastronomie et de vins naturels, Antoine et Floriane ont créé Le Margo avec une vision commune : offrir une expérience culinaire authentique qui célèbre les produits locaux et le terroir du Périgord. Leur complicité et leur amour pour la cuisine se retrouvent dans chaque détail du restaurant.', 'le-margo'); ?>
                        </p>
                        <p class="restaurant-name-origin">
                            <?php echo esc_html__('Le nom "Le Margo" est un clin d\'œil affectueux à Margo, la fille de Floriane, qui inspire chaque jour l\'esprit familial et chaleureux du restaurant.', 'le-margo'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Producteurs Locaux -->
    <section class="section partners-section" style="background-color: var(--color-beige-light);">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?php echo esc_html__('Nos partenaires', 'le-margo'); ?></span>
                <h2 class="section-title"><?php echo esc_html__('Les producteurs qui nous font confiance', 'le-margo'); ?></h2>
            </div>
            <div class="partners-content">
                <p style="text-align: center; max-width: 800px; margin: 0 auto 50px;"><?php echo esc_html__('Nous collaborons avec des producteurs locaux passionnés qui partagent notre engagement pour une agriculture durable et des produits de qualité exceptionnelle.', 'le-margo'); ?></p>
                
                <div class="partners-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 40px;">
                    <!-- Le Fournil Du Malromé -->
                    <div class="partner-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <h4 style="font-size: 1.3em; margin-bottom: 12px; color: var(--color-primary);"><?php echo esc_html__('Le Fournil Du Malromé', 'le-margo'); ?></h4>
                        <p style="font-style: italic; margin-bottom: 15px; color: var(--color-gray); font-weight: 600;"><?php echo esc_html__('Boulangerie artisanale - Saint-Jean-de-Duras', 'le-margo'); ?></p>
                        <p style="margin-bottom: 15px;"><?php echo esc_html__('Boulangerie artisanale réputée (4,8/5 étoiles) spécialisée dans les pains traditionnels et croissants d\'exception. Un savoir-faire reconnu par tous nos clients qui apprécient la qualité de leurs produits.', 'le-margo'); ?></p>
                        <p style="font-size: 0.9em; color: var(--color-gray);"><?php echo esc_html__('📍 Coulinet, Saint-Jean-de-Duras (47120)', 'le-margo'); ?></p>
                    </div>
                    
                    <!-- Corne d'Abondance -->
                    <div class="partner-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <h4 style="font-size: 1.3em; margin-bottom: 12px; color: var(--color-primary);"><?php echo esc_html__('Corne d\'Abondance', 'le-margo'); ?></h4>
                        <p style="font-style: italic; margin-bottom: 15px; color: var(--color-gray); font-weight: 600;"><?php echo esc_html__('Légumes bio - Geert Schoenmakers', 'le-margo'); ?></p>
                        <p style="margin-bottom: 15px;"><?php echo esc_html__('Producteur passionné cultivant des légumes biologiques de haute qualité. Geert met tout son savoir-faire au service d\'une agriculture respectueuse de l\'environnement et de la biodiversité.', 'le-margo'); ?></p>
                        <p style="font-size: 0.9em; color: var(--color-gray);"><?php echo esc_html__('📍 Serres-et-Montguyard (24500)', 'le-margo'); ?></p>
                    </div>
                    
                    <!-- La Boîte à Légumes -->
                    <div class="partner-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <h4 style="font-size: 1.3em; margin-bottom: 12px; color: var(--color-primary);"><?php echo esc_html__('La Boîte à Légumes', 'le-margo'); ?></h4>
                        <p style="font-style: italic; margin-bottom: 15px; color: var(--color-gray); font-weight: 600;"><?php echo esc_html__('Maraîchage local - Eymet', 'le-margo'); ?></p>
                        <p style="margin-bottom: 15px;"><?php echo esc_html__('Producteur local situé au cœur d\'Eymet, proposant des légumes frais de saison cultivés avec passion. Une proximité géographique qui garantit la fraîcheur et réduit notre empreinte carbone.', 'le-margo'); ?></p>
                        <p style="font-size: 0.9em; color: var(--color-gray);"><?php echo esc_html__('📍 Eymet (24500)', 'le-margo'); ?></p>
                    </div>
                    
                    <!-- Les Bouchées d'Eymet -->
                    <div class="partner-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <h4 style="font-size: 1.3em; margin-bottom: 12px; color: var(--color-primary);"><?php echo esc_html__('Les Bouchées d\'Eymet', 'le-margo'); ?></h4>
                        <p style="font-style: italic; margin-bottom: 15px; color: var(--color-gray); font-weight: 600;"><?php echo esc_html__('Boucherie artisanale - Eymet', 'le-margo'); ?></p>
                        <p style="margin-bottom: 15px;"><?php echo esc_html__('Boucherie artisanale de notre belle ville d\'Eymet, proposant des viandes de qualité sélectionnées avec soin. Un partenaire de proximité qui partage nos valeurs de qualité et d\'authenticité.', 'le-margo'); ?></p>
                        <p style="font-size: 0.9em; color: var(--color-gray);"><?php echo esc_html__('📍 Eymet (24500)', 'le-margo'); ?></p>
                    </div>
                    
                    <!-- Label Blonde De Falu -->
                    <div class="partner-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <h4 style="font-size: 1.3em; margin-bottom: 12px; color: var(--color-primary);">
                            <a href="https://labelblondedefalu.com/" target="_blank" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center;">
                                <?php echo esc_html__('Label Blonde De Falu', 'le-margo'); ?>
                                <span style="margin-left: 8px; font-size: 0.8em;">🔗</span>
                            </a>
                        </h4>
                        <p style="font-style: italic; margin-bottom: 15px; color: var(--color-gray); font-weight: 600;"><?php echo esc_html__('Élevage familial - Bœuf, veau, porc', 'le-margo'); ?></p>
                        <p style="margin-bottom: 15px;"><?php echo esc_html__('Ferme familiale des Quievy dédiée à l\'élevage de qualité avec un respect strict du bien-être animal. Ils proposent bœuf, veau, porc et charcuterie dans une démarche éthique et durable.', 'le-margo'); ?></p>
                        <p style="font-size: 0.9em; color: var(--color-gray);"><?php echo esc_html__('📍 Soumensac (47120)', 'le-margo'); ?></p>
                        <p style="margin-top: 12px;">
                            <a href="https://labelblondedefalu.com/" target="_blank" style="color: var(--color-primary); text-decoration: none; font-weight: 600; font-size: 0.9em;">
                                <?php echo esc_html__('→ Découvrir leurs produits', 'le-margo'); ?>
                            </a>
                        </p>
                    </div>
                    
                    <!-- Nos Vignerons -->
                    <div class="partner-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <h4 style="font-size: 1.3em; margin-bottom: 12px; color: var(--color-primary);"><?php echo esc_html__('Nos Vignerons Amis', 'le-margo'); ?></h4>
                        <p style="font-style: italic; margin-bottom: 15px; color: var(--color-gray); font-weight: 600;"><?php echo esc_html__('Vins naturels - Région', 'le-margo'); ?></p>
                        <p style="margin-bottom: 15px;"><?php echo esc_html__('Un collectif de vignerons passionnés qui travaillent leurs vignes dans le respect de la nature. Ces artisans du vin produisent des cuvées authentiques qui s\'accordent parfaitement avec notre cuisine du terroir.', 'le-margo'); ?></p>
                        <p style="font-size: 0.9em; color: var(--color-gray);"><?php echo esc_html__('📍 Vignobles locaux du Sud-Ouest', 'le-margo'); ?></p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 40px;">
                    <p style="font-style: italic; color: var(--color-gray); font-size: 1.1em;"><?php echo esc_html__('Ces partenariats locaux sont le cœur de notre démarche : privilégier le circuit court, soutenir l\'économie locale et vous offrir des produits d\'exception.', 'le-margo'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Galerie -->
    <section class="section gallery-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?php echo esc_html__('En images', 'le-margo'); ?></span>
                <h2 class="section-title"><?php echo esc_html__('L\'ambiance du Margo', 'le-margo'); ?></h2>
            </div>
            <div class="gallery-grid">
                <!-- Images du restaurant et des plats -->
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/terrasse-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Terrasse du restaurant', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/salle-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Salle du restaurant', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/bar-vins-naturels.jpg'); ?>" alt="<?php echo esc_attr__('Bar à vins naturels', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-gastronomique.jpg'); ?>" alt="<?php echo esc_attr__('Plat gastronomique', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/dessert-maison.jpg'); ?>" alt="<?php echo esc_attr__('Dessert fait maison', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/produits-locaux.jpg'); ?>" alt="<?php echo esc_attr__('Produits locaux', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/chef-cuisine-preparation.jpg'); ?>" alt="<?php echo esc_attr__('Chef en préparation', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/ambiance-soiree-restaurant.jpg'); ?>" alt="<?php echo esc_attr__('Ambiance de soirée', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/cave-vins-selection.jpg'); ?>" alt="<?php echo esc_attr__('Cave à vins', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/plat-signature-chef.jpg'); ?>" alt="<?php echo esc_attr__('Plat signature du chef', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/ingredients-frais-locaux.jpg'); ?>" alt="<?php echo esc_attr__('Ingrédients frais et locaux', 'le-margo'); ?>">
                </div>
                <div class="zoomable-image">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/table-dressage-elegant.jpg'); ?>" alt="<?php echo esc_attr__('Dressage de table élégant', 'le-margo'); ?>">
                </div>
            </div>
        </div>
    </section>

    <!-- Section Réservation -->
    <section class="section reservation-section">
        <div class="container">
            <div class="reservation-content" style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h2><?php echo esc_html__('Venez vivre l\'expérience Margo', 'le-margo'); ?></h2>
                <p style="margin-bottom: 30px;"><?php echo esc_html__('Nous serions ravis de vous accueillir et de vous faire découvrir notre cuisine locale et nos vins naturels dans une ambiance conviviale.', 'le-margo'); ?></p>
                <div class="reservation-buttons">
                    <a href="<?php echo esc_url(home_url('/reserver')); ?>" class="btn" style="min-width: 200px; font-weight: 600;"><?php echo esc_html__('Réserver une table', 'le-margo'); ?></a>
                </div>
            </div>
        </div>
    </section>

</main><!-- #main -->

<?php
get_footer(); 