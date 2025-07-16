<?php
/**
 * Classe utilitaire pour Le Margo
 */

if (!defined('ABSPATH')) {
    exit;
}

class Le_Margo_Utils {
    private static $instance = null;

    private function __construct() {}

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Charge un template partiel
     */
    public static function get_template_part($template, $data = array()) {
        $template_path = get_template_directory() . '/template-parts/' . $template . '.php';
        if (file_exists($template_path)) {
            extract($data);
            include $template_path;
        }
    }

    /**
     * Formate une date selon le format spécifié
     */
    public static function format_date($date, $format = 'd/m/Y') {
        return date_i18n($format, strtotime($date));
    }

    /**
     * Fonction utilitaire pour formater les créneaux horaires
     */
    public static function format_time_slots($times_str) {
        return array_map(function($time) {
            return array('value' => trim($time), 'label' => trim($time));
        }, explode(',', $times_str));
    }

    /**
     * Nettoie et valide les données de réservation
     */
    public static function sanitize_reservation_data($data) {
        // Conversion sécurisée de la date
        $date_input = sanitize_text_field($data['date']);
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);
        
        if ($date_obj === false) {
            // Si le format d/m/Y échoue, essayer d'autres formats
            $date_obj = DateTime::createFromFormat('Y-m-d', $date_input);
            if ($date_obj === false) {
                // Si aucun format ne fonctionne, lever une exception
                throw new Exception('Format de date invalide. Format attendu : JJ/MM/AAAA');
            }
        }
        
        return array(
            'meal_type' => 'general', // Plus de distinction déjeuner/dîner
            'reservation_time' => sanitize_text_field($data['time']),
            'people' => intval($data['people']),
            'reservation_date' => $date_obj->format('Y-m-d'),
            'customer_name' => sanitize_text_field($data['customer_name']),
            'customer_email' => sanitize_email($data['customer_email']),
            'customer_phone' => sanitize_text_field($data['customer_phone']),
            'notes' => sanitize_textarea_field($data['notes']),
            'accept_reminder' => isset($data['accept_reminder']) ? 1 : 0,
            'newsletter' => isset($data['newsletter']) ? 1 : 0,
            'consent_data_processing' => isset($data['consent_data_processing']) ? 1 : 0,
            'consent_data_storage' => isset($data['consent_data_storage']) ? 1 : 0
        );
    }
}

/**
 * Gestionnaire d'erreurs
 */
class Le_Margo_Error_Handler {
    public static function handle_error($message, $redirect_url = null) {
        error_log($message);
        if ($redirect_url) {
            wp_redirect(add_query_arg('reservation_error', urlencode($message), $redirect_url));
            exit;
        }
        return false;
    }

    public static function handle_ajax_error($message) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => false, 'message' => $message));
        exit;
    }
}

/**
 * Gestionnaire de redirection
 */
class Le_Margo_Redirect_Handler {
    public static function redirect($url, $is_ajax = false) {
        if ($is_ajax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('success' => true, 'redirect' => $url));
            exit;
        } else {
            wp_safe_redirect($url);
            exit;
        }
    }
}

// Fonction utilitaire pour accéder aux utilitaires
if (!function_exists('le_margo_get_utils')) {
function le_margo_get_utils() {
    return Le_Margo_Utils::get_instance();
    }
} 