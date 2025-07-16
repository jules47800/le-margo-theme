<?php
/**
 * Template Name: Suppression des Données
 * Template pour la page "Suppression des Données"
 *
 * @package Le Margo
 */

get_header();

// Traitement du formulaire
$form_submitted = false;
$success = false;
$error = false;
$error_message = '';

if (isset($_POST['delete_data_submitted']) && isset($_POST['delete_data_nonce'])) {
    if (wp_verify_nonce($_POST['delete_data_nonce'], 'delete_user_data')) {
        $form_submitted = true;
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
        if (empty($email)) {
            $error = true;
            $error_message = __('Veuillez fournir une adresse email valide.', 'le-margo');
        } else {
            // Rechercher le client dans la base de données
            global $wpdb;
            $customers_table = $wpdb->prefix . 'customer_stats';
            $reservations_table = $wpdb->prefix . 'reservations';
            
            $customer = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $customers_table WHERE email = %s",
                $email
            ));
            
            if (!$customer) {
                $error = true;
                $error_message = __('Aucun client trouvé avec cette adresse email.', 'le-margo');
            } else {
                // Supprimer les données
                $wpdb->delete($customers_table, array('email' => $email));
                
                // Anonymiser les réservations
                $wpdb->update(
                    $reservations_table,
                    array(
                        'customer_name' => __('Anonyme', 'le-margo'),
                        'customer_email' => 'deleted@example.com',
                        'customer_phone' => '0000000000',
                        'newsletter' => 0
                    ),
                    array('customer_email' => $email)
                );
                
                // Envoyer un email de confirmation
                $subject = __('Confirmation de suppression de vos données - Le Margo', 'le-margo');
                $message = sprintf(
                    __('Bonjour,

Nous vous confirmons que vos données personnelles ont été supprimées de notre base de données conformément au RGPD.

Les données suivantes ont été effacées :
- Votre nom et coordonnées
- Votre historique de visites
- Vos préférences

Vos anciennes réservations ont été anonymisées pour des raisons statistiques et de gestion.

Nous vous remercions d\'avoir fréquenté Le Margo et espérons vous revoir dans d\'autres circonstances.

Cordialement,
L\'équipe du Margo', 'le-margo'),
                    $customer->name
                );
                $headers = array('Content-Type: text/plain; charset=UTF-8');
                
                $mail_sent = wp_mail($email, $subject, $message, $headers);
                
                $success = true;
            }
        }
    } else {
        $error = true;
        $error_message = __('Erreur de sécurité. Veuillez réessayer.', 'le-margo');
    }
}
?>

<main id="primary" class="site-main">
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><?php esc_html_e('Demande de Suppression de Vos Données', 'le-margo'); ?></h1>
        </div>
    </div>

    <div class="page-content">
        <div class="container">
            <div class="data-deletion-content">
                <?php if ($form_submitted && $success) : ?>
                    <div class="success-message">
                        <p><?php esc_html_e('Votre demande de suppression de données a été traitée avec succès. Un email de confirmation vous a été envoyé.', 'le-margo'); ?></p>
                        <p><?php esc_html_e('Toutes vos informations personnelles et votre historique de visites ont été supprimés de notre base de données.', 'le-margo'); ?></p>
                        <p><a href="<?php echo esc_url(home_url()); ?>"><?php esc_html_e('Retour à l\'accueil', 'le-margo'); ?></a></p>
                    </div>
                <?php else : ?>
                    <?php if ($error) : ?>
                        <div class="error-message">
                            <p><?php echo esc_html($error_message); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="deletion-info">
                        <h2><?php esc_html_e('Exercez votre droit à l\'oubli', 'le-margo'); ?></h2>
                        <p><?php esc_html_e('Conformément au Règlement Général sur la Protection des Données (RGPD), vous pouvez demander la suppression de vos données personnelles de notre base de données.', 'le-margo'); ?></p>
                        <p><?php esc_html_e('Veuillez remplir le formulaire ci-dessous pour initier cette demande. Une fois votre demande traitée, vos informations personnelles et votre historique de visites seront définitivement supprimés.', 'le-margo'); ?></p>
                        
                        <div class="important-notice">
                            <h3><?php esc_html_e('Important', 'le-margo'); ?></h3>
                            <p><?php esc_html_e('Cette action est irréversible. Après suppression, nous ne pourrons plus récupérer votre historique de fidélité ni vos préférences.', 'le-margo'); ?></p>
                            <p><?php esc_html_e('Vos anciennes réservations seront anonymisées pour des raisons statistiques et de gestion.', 'le-margo'); ?></p>
                        </div>
                    </div>

                    <form method="post" class="deletion-form">
                        <?php wp_nonce_field('delete_user_data', 'delete_data_nonce'); ?>
                        <input type="hidden" name="delete_data_submitted" value="1">

                        <div class="form-group">
                            <label for="email"><?php esc_html_e('Votre email', 'le-margo'); ?></label>
                            <input type="email" id="email" name="email" required placeholder="<?php esc_attr_e('Entrez l\'email utilisé pour vos réservations', 'le-margo'); ?>">
                            <p class="field-help"><?php esc_html_e('Veuillez utiliser l\'adresse email avec laquelle vous avez effectué vos réservations.', 'le-margo'); ?></p>
                        </div>

                        <div class="form-group form-group-checkbox">
                            <input type="checkbox" id="confirm_deletion" required>
                            <label for="confirm_deletion"><?php esc_html_e('Je confirme vouloir supprimer définitivement mes données personnelles de la base de données du restaurant Le Margo.', 'le-margo'); ?></label>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="deletion-submit"><?php esc_html_e('Supprimer mes données', 'le-margo'); ?></button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer(); 