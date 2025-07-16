<?php
/**
 * Template pour la page "Découvrez Eymet" - Version épurée
 * Template Name: Découvrez Eymet
 *
 * @package Le Margo
 */

get_header();
?>

<main id="primary" class="site-main eymet-page">
    <!-- Section Héros -->
    <section class="eymet-hero" style="background-image: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/Eymet_Rouquette_village_(1).JPG'); ?>');">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1>Eymet, notre bastide médiévale</h1>
            <p>Le Margo est fier d'être au cœur de l'une des plus belles bastides du Périgord.</p>
        </div>
    </section>

    <div class="container main-content">
        <!-- Histoire -->
        <section class="content-section">
            <h2>750 ans d'histoire</h2>
            <div class="text-content">
                <p><strong>1270 - La Fondation</strong><br>
                La bastide d'Eymet est fondée par Alphonse de Poitiers. Son plan carré, typique, organise la vie autour d'une place centrale bordée d'arcades.</p>
                
                <p><strong>1337 - La Guerre de Cent Ans</strong><br>
                Située à la frontière, Eymet est une place forte convoitée, passant alternativement sous le contrôle des Français et des Anglais.</p>
                
                <p><strong>Aujourd'hui</strong><br>
                Eymet est une destination prisée, célèbre pour son marché, son héritage médiéval et son atmosphère accueillante.</p>
            </div>
        </section>

        <!-- Le Restaurant -->
        <section class="content-section restaurant-section">
            <h2>Le Margo, au cœur d'Eymet</h2>
            <div class="restaurant-content">
                <div class="text-content">
                    <p>Après une balade dans les ruelles pavées, retrouvez le calme et la gourmandise de notre restaurant. Idéalement situé, Le Margo vous accueille pour une pause gastronomique où les produits locaux du Périgord sont à l'honneur.</p>
                    <a href="<?php echo esc_url(home_url('/reserver')); ?>" class="btn-reserver">Réserver votre table</a>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
.eymet-page {
    line-height: 1.6;
}

.eymet-hero {
    position: relative;
    height: 60vh;
    min-height: 400px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    color: white;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.hero-content h1 {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    color: #fff;
}

.hero-content p {
    font-size: 1.4rem;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    
}

.main-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 4rem 20px;
}

.content-section {
    margin-bottom: 4rem;
}

.content-section:last-child {
    margin-bottom: 0;
}

.content-section h2 {
    font-size: 2.5rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.text-content p {
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.text-content p:last-child {
    margin-bottom: 0;
}

.btn-reserver {
    display: inline-block;
    background: #c0392b;
    color: white;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 1rem;
    transition: background 0.3s ease;
}

.btn-reserver:hover {
    background: #a93226;
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }

    .hero-content p {
        font-size: 1.2rem;
    }

    .main-content {
        padding: 3rem 20px;
    }

    .content-section h2 {
        font-size: 2rem;
    }
}
</style>

<?php
get_footer();
?> 