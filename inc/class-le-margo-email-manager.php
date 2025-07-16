<?php
/**
 * Gestionnaire d'emails pour Le Margo
 */

if (!defined('ABSPATH')) {
    exit;
}

class Le_Margo_Email_Manager {
    private static $instance = null;
    private $restaurant_name;
    private $restaurant_address;
    private $restaurant_phone;
    private $restaurant_url;
    private $restaurant_siret;

    private function __construct() {
        $this->restaurant_name = get_theme_mod('le_margo_restaurant_name', 'Le Margo');
        $this->restaurant_address = get_theme_mod('le_margo_restaurant_address', '6 avenue du 6 juin 1944, 24500 Eymet');
        $this->restaurant_phone = get_theme_mod('le_margo_restaurant_phone', '+33 5 53 63 80 80');
        $this->restaurant_url = home_url();
        $this->restaurant_siret = get_theme_mod('le_margo_restaurant_siret', '123 456 789 00012');
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Envoie un email avec le template Le Margo
     */
    public function send_email($to, $subject, $content, $headers = array()) {
        if (empty($headers)) {
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . $this->restaurant_name . ' <' . get_option('admin_email') . '>',
                'Reply-To: ' . get_option('admin_email')
            );
        }

        $message = $this->get_email_template($content);
        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Génère le template HTML pour les emails
     */
    private function get_email_template($content_html) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : get_template_directory_uri() . '/assets/images/logo.png';
        $bg_color = '#f5f1ea';
        $main_bg_color = '#ffffff';
        $text_color = '#333333';
        $accent_color = '#b5a692';
        $footer_text_color = '#666666';

        return '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . esc_html($this->restaurant_name) . '</title>
            <style>
                /* Style de base pour les clients qui supportent <style> */
                body {
                    margin: 0;
                    padding: 0;
                    background-color: ' . $bg_color . ';
                    font-family: Arial, sans-serif;
                }
                .email-container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: ' . $main_bg_color . ';
                }
                .content-cell {
                    padding: 30px;
                }
                 /* Style pour les détails de réservation */
                .reservation-details {
                    background-color: #f9f7f4;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 25px 0;
                    border: 1px solid #d8cfc0;
                }
                .detail-row {
                    margin-bottom: 12px;
                    font-size: 1rem;
                    line-height: 1.5;
                }
                .detail-label {
                    font-weight: 500;
                    color: ' . $accent_color . ';
                    min-width: 130px;
                    display: inline-block;
                }
                 /* Bouton d\'action */
                .call-to-action {
                    display: inline-block;
                    background-color: ' . $accent_color . ';
                    color: #ffffff !important;
                    text-decoration: none;
                    padding: 12px 28px;
                    border-radius: 25px;
                    font-weight: 500;
                    margin-top: 25px;
                    font-size: 1rem;
                    border: none;
                }
                @media screen and (max-width: 600px) {
                    .content-cell {
                        padding: 15px !important;
                    }
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: ' . $bg_color . '; font-family: Arial, sans-serif;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: ' . $bg_color . ';">
                <tr>
                    <td align="center">
                        <table class="email-container" width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: ' . $main_bg_color . '; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
                            <!-- Header -->
                            <tr>
                                <td align="center" style="background-color: #f5f1ea; padding: 30px 20px 20px 20px;">
                                    <a href="' . esc_url($this->restaurant_url) . '">
                                        <img src="' . esc_url($logo_url) . '" alt="' . esc_attr($this->restaurant_name) . '" style="max-width: 120px; margin-bottom: 10px;">
                                    </a>
                                </td>
                            </tr>
                            <!-- Body -->
                            <tr>
                                <td class="content-cell" style="padding: 40px; color: ' . $text_color . '; font-size: 16px; line-height: 1.6;">
                                    ' . $content_html . '
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td align="center" style="background-color: #f5f1ea; padding: 30px; border-top: 1px solid #d8cfc0; font-size: 14px; color: ' . $footer_text_color . ';">
                                    <p style="margin: 0 0 10px 0; font-weight: bold;">' . esc_html($this->restaurant_name) . '</p>
                                    <p style="margin: 0 0 10px 0;">' . esc_html($this->restaurant_address) . '</p>
                                    <p style="margin: 0 0 15px 0;">
                                        <a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $this->restaurant_phone)) . '" style="color: ' . $footer_text_color . '; text-decoration: none;">' . esc_html($this->restaurant_phone) . '</a>
                                        | <a href="' . esc_url($this->restaurant_url) . '" style="color: ' . $footer_text_color . '; text-decoration: none;">' . esc_html(str_replace(['http://', 'https://'], '', $this->restaurant_url)) . '</a>
                                    </p>
                                    <p style="font-size: 12px; color: #888; margin: 20px 0 0 0; line-height: 1.4;">
                                        ' . sprintf(__('SIRET : %s', 'le-margo'), esc_html($this->restaurant_siret)) . '<br>
                                        ' . __('Vous recevez cet email suite à une action sur notre site. Conformément à la loi Informatique et Libertés et au RGPD, vous disposez d\'un droit d\'accès et de rectification de vos données.', 'le-margo') . '
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
    }

    /**
     * Envoie un email de confirmation de réservation
     */
    public function send_reservation_confirmation($reservation) {
        error_log("=== ENVOI EMAIL DE CONFIRMATION ===");
        error_log("Réservation ID: {$reservation->id}");
        error_log("Client: {$reservation->customer_name} ({$reservation->customer_email})");
        
        // --- Email au client ---
        $client_content = sprintf(
            '<h2>%s</h2>
            <p>%s %s,</p>
            <p>%s</p>
            <div class="reservation-details">
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
            </div>
            <p>%s</p>
            <p>%s</p>
            <p class="legal-notice">%s</p>',
            __('Confirmation de votre réservation', 'le-margo'),
            __('Bonjour', 'le-margo'),
            esc_html($reservation->customer_name),
            __('Nous avons le plaisir de confirmer votre réservation au restaurant Le Margo :', 'le-margo'),
            __('Date :', 'le-margo'),
            date_i18n('l j F Y', strtotime($reservation->reservation_date)),
            __('Heure :', 'le-margo'),
            date_i18n('H:i', strtotime($reservation->reservation_time)),
            __('Nombre de personnes :', 'le-margo'),
            esc_html($reservation->people),
            __('Référence :', 'le-margo'),
            sprintf('RES-%06d', $reservation->id),
            __('Nous avons hâte de vous accueillir ! En cas d\'empêchement, merci de nous prévenir au moins 24 heures à l\'avance.', 'le-margo'),
            __('L\'équipe du Margo', 'le-margo'),
            __('Cette confirmation de réservation fait office de contrat entre vous et le restaurant. En cas de litige, elle pourra être utilisée comme preuve de réservation.', 'le-margo')
        );

        $client_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->restaurant_name . ' <contact@lemargo.fr>',
            'Reply-To: ' . $this->restaurant_name . ' <contact@lemargo.fr>'
        );
        
        error_log("Tentative d'envoi email client vers: {$reservation->customer_email}");
        $client_sent = $this->send_email(
            $reservation->customer_email,
            sprintf(__('Confirmation de votre réservation #%06d - Le Margo', 'le-margo'), $reservation->id),
            $client_content,
            $client_headers
        );
        error_log("Résultat email client: " . ($client_sent ? 'SUCCÈS' : 'ÉCHEC'));

        // --- Email à l'administrateur ---
        $admin_content = sprintf(
            '<h2>%s</h2>
            <div class="reservation-details">
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
            </div>
            <p><strong>%s :</strong></p>
            <p>%s</p>',
            sprintf(__('Nouvelle réservation #%06d', 'le-margo'), $reservation->id),
            __('Référence', 'le-margo'),
            sprintf('RES-%06d', $reservation->id),
            __('Client', 'le-margo'),
            esc_html($reservation->customer_name),
            __('Email', 'le-margo'),
            esc_html($reservation->customer_email),
            __('Téléphone', 'le-margo'),
            esc_html($reservation->customer_phone),
            __('Date', 'le-margo'),
            date_i18n('l j F Y', strtotime($reservation->reservation_date)),
            __('Heure', 'le-margo'),
            date_i18n('H:i', strtotime($reservation->reservation_time)),
            __('Service', 'le-margo'),
            $reservation->meal_type === 'lunch' ? __('Déjeuner', 'le-margo') : __('Dîner', 'le-margo'),
            __('Nombre de personnes', 'le-margo'),
            esc_html($reservation->people),
            __('Notes', 'le-margo'),
            nl2br(esc_html($reservation->notes))
        );

        $admin_users = get_users(array('role' => 'administrator'));
        $admin_emails = array();
        foreach ($admin_users as $admin) {
            $admin_emails[] = $admin->user_email;
        }
        
        $admin_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->restaurant_name . ' <contact@lemargo.fr>',
            'Reply-To: ' . $reservation->customer_name . ' <' . $reservation->customer_email . '>'
        );
        
        error_log("Emails admin trouvés: " . implode(', ', $admin_emails));
        error_log("Tentative d'envoi email admin vers: " . implode(', ', $admin_emails));

        $admin_sent = $this->send_email(
            $admin_emails,
            sprintf(__('Nouvelle réservation #%06d - %s personnes le %s', 'le-margo'),
                $reservation->id,
                $reservation->people,
                date_i18n('d/m/Y à H:i', strtotime($reservation->reservation_date . ' ' . $reservation->reservation_time))
            ),
            $admin_content,
            $admin_headers
        );
        error_log("Résultat email admin: " . ($admin_sent ? 'SUCCÈS' : 'ÉCHEC'));
        
        // L'email au client est le plus important
        // S'il est envoyé, on considère que c'est un succès global
        $final_result = $client_sent;
        error_log("Résultat final (basé sur l'email client): " . ($final_result ? 'SUCCÈS' : 'ÉCHEC'));
        error_log("=== FIN ENVOI EMAIL DE CONFIRMATION ===");
        
        return $final_result;
    }

    /**
     * Envoie un email de rappel de réservation
     */
    public function send_reminder_email($reservation) {
        $content = sprintf(
            '<h2>%s</h2>
            <p>%s %s,</p>
            <p>%s</p>
            <div class="reservation-details">
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
            </div>
            <p>%s</p>
            <p>%s</p>
            <p>%s</p>',
            __('Rappel de votre réservation', 'le-margo'),
            __('Bonjour', 'le-margo'),
            esc_html($reservation->customer_name),
            __('Nous vous rappelons votre réservation à venir au restaurant Le Margo :', 'le-margo'),
            __('Date :', 'le-margo'),
            date_i18n('l j F Y', strtotime($reservation->reservation_date)),
            __('Heure :', 'le-margo'),
            date_i18n('H:i', strtotime($reservation->reservation_time)),
            __('Nombre de personnes :', 'le-margo'),
            esc_html($reservation->people),
            __('Référence :', 'le-margo'),
            sprintf('RES-%06d', $reservation->id),
            __('En cas d\'empêchement, merci de nous prévenir au plus tôt par téléphone.', 'le-margo'),
            __('Nous avons hâte de vous accueillir !', 'le-margo'),
            __('L\'équipe du Margo', 'le-margo')
        );

        return $this->send_email(
            $reservation->customer_email,
            sprintf(__('Rappel : votre réservation au Margo - %s', 'le-margo'),
                date_i18n('l j F', strtotime($reservation->reservation_date))
            ),
            $content
        );
    }

    /**
     * Envoie un email d'annulation de réservation
     */
    public function send_cancellation_email($reservation) {
        $content = sprintf(
            '<h2>%s</h2>
            <p>%s %s,</p>
            <p>%s</p>
            <div class="reservation-details">
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
                <div class="detail-row"><span class="detail-label">%s</span> %s</div>
            </div>
            <p>%s</p>
            <p>%s</p>
            <p>%s</p>
            <p>%s</p>',
            __('Confirmation d\'annulation de réservation', 'le-margo'),
            __('Bonjour', 'le-margo'),
            esc_html($reservation->customer_name),
            __('Nous confirmons l\'annulation de votre réservation :', 'le-margo'),
            __('Date :', 'le-margo'),
            date_i18n('l j F Y', strtotime($reservation->reservation_date)),
            __('Heure :', 'le-margo'),
            date_i18n('H:i', strtotime($reservation->reservation_time)),
            __('Nombre de personnes :', 'le-margo'),
            esc_html($reservation->people),
            __('Référence :', 'le-margo'),
            sprintf('RES-%06d', $reservation->id),
            __('Si vous souhaitez effectuer une nouvelle réservation, n\'hésitez pas à nous contacter par téléphone ou à réserver directement sur notre site.', 'le-margo'),
            __('Nous espérons avoir le plaisir de vous accueillir prochainement au Margo.', 'le-margo'),
            __('Cordialement,', 'le-margo'),
            __('L\'équipe du Margo', 'le-margo')
        );

        return $this->send_email(
            $reservation->customer_email,
            sprintf(__('Annulation de votre réservation #%06d - Le Margo', 'le-margo'),
                $reservation->id
            ),
            $content
        );
    }
}

// Fonction utilitaire pour accéder au gestionnaire d'emails
if (!function_exists('le_margo_get_email_manager')) {
function le_margo_get_email_manager() {
    return Le_Margo_Email_Manager::get_instance();
    }
} 