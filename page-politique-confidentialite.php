<?php
/**
 * Template Name: Politique de Confidentialité
 * Template pour la page "Politique de Confidentialité"
 *
 * @package Le Margo
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><?php esc_html_e('Politique de Confidentialité', 'le-margo'); ?></h1>
        </div>
    </div>

    <div class="page-content">
        <div class="container">
            <div class="privacy-policy-content">
                <h2><?php esc_html_e('Protection de vos données personnelles', 'le-margo'); ?></h2>
                <p><?php esc_html_e('Le restaurant Le Margo s\'engage à protéger la confidentialité de vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD).', 'le-margo'); ?></p>

                <h3><?php esc_html_e('1. Données collectées', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Nous collectons les informations suivantes lorsque vous effectuez une réservation :', 'le-margo'); ?></p>
                <ul>
                    <li><?php esc_html_e('Nom et prénom', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Adresse email', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Numéro de téléphone', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Détails de la réservation (date, heure, nombre de personnes, demandes spéciales)', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Historique des visites', 'le-margo'); ?></li>
                </ul>

                <h3><?php esc_html_e('2. Utilisation des données', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Nous utilisons vos données pour :', 'le-margo'); ?></p>
                <ul>
                    <li><?php esc_html_e('Gérer votre réservation', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Vous envoyer des confirmations et rappels', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Identifier les clients fidèles et proposer un programme VIP (après 5 visites)', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Personnaliser votre expérience', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Vous contacter en cas de modification de votre réservation', 'le-margo'); ?></li>
                </ul>
                <p><?php esc_html_e('Si vous avez consenti à recevoir notre newsletter, nous utiliserons votre email pour vous envoyer des actualités et offres spéciales du restaurant.', 'le-margo'); ?></p>

                <h3><?php esc_html_e('3. Conservation des données', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Nous conservons vos données pendant une durée de 3 ans à compter de votre dernière réservation, afin de maintenir votre historique de visite et votre statut de fidélité.', 'le-margo'); ?></p>

                <h3><?php esc_html_e('4. Vos droits', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Conformément au RGPD, vous disposez des droits suivants concernant vos données personnelles :', 'le-margo'); ?></p>
                <ul>
                    <li><?php esc_html_e('Droit d\'accès à vos données', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Droit de rectification', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Droit à l\'effacement (« droit à l\'oubli »)', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Droit à la limitation du traitement', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Droit d\'opposition', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Droit à la portabilité des données', 'le-margo'); ?></li>
                </ul>

                <h3><?php esc_html_e('5. Exercer vos droits', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Pour exercer vos droits ou pour toute question relative à la protection de vos données, vous pouvez :', 'le-margo'); ?></p>
                <ul>
                    <li><?php esc_html_e('Utiliser notre formulaire de contact', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Nous envoyer un email à privacy@lemargo.fr', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Nous contacter par téléphone au +33 5 53 63 80 80', 'le-margo'); ?></li>
                    <li><?php esc_html_e('Nous écrire à l\'adresse : Restaurant Le Margo, 6 avenue du 6 juin 1944, Eymet 24500', 'le-margo'); ?></li>
                </ul>
                <p><?php esc_html_e('Nous nous efforcerons de répondre à votre demande dans un délai d\'un mois.', 'le-margo'); ?></p>

                <h3><?php esc_html_e('6. Sécurité des données', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données personnelles contre la perte, l\'accès non autorisé, la divulgation, l\'altération et la destruction.', 'le-margo'); ?></p>

                <h3><?php esc_html_e('7. Partage des données', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Nous ne partageons pas vos données personnelles avec des tiers, sauf lorsque cela est nécessaire pour l\'exécution de nos services ou lorsque nous sommes légalement tenus de le faire.', 'le-margo'); ?></p>

                <h3><?php esc_html_e('8. Modifications de notre politique de confidentialité', 'le-margo'); ?></h3>
                <p><?php esc_html_e('Nous pouvons mettre à jour cette politique de confidentialité de temps à autre. Toute modification sera publiée sur cette page avec une date de révision mise à jour.', 'le-margo'); ?></p>
                <p><?php esc_html_e('Dernière mise à jour : ', 'le-margo'); echo date_i18n('d/m/Y'); ?></p>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 