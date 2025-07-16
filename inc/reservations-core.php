<?php
/**
 * Le Margo - Fonctions de base pour les réservations (règles, cron, etc.)
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Ajouter les routes pour les annulations
 */
function le_margo_add_rewrite_rules() {
    add_rewrite_rule(
        'annuler-reservation/([0-9]+)/([^/]+)/?$',
        'index.php?pagename=annuler-reservation&id=$matches[1]&nonce=$matches[2]',
        'top'
    );
}
add_action('init', 'le_margo_add_rewrite_rules');

/**
 * Ajouter les variables de requête personnalisées
 */
function le_margo_query_vars($vars) {
    $vars[] = 'id';
    $vars[] = 'nonce';
    return $vars;
}
add_filter('query_vars', 'le_margo_query_vars');

/**
 * Planifier l'envoi des rappels
 */
function le_margo_schedule_reminders() {
    if (!wp_next_scheduled('le_margo_send_reminders')) {
        wp_schedule_event(strtotime('today 10:00:00'), 'daily', 'le_margo_send_reminders');
    }
}
add_action('wp', 'le_margo_schedule_reminders');

/**
 * Envoyer les rappels
 */
function le_margo_send_reminders() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // Récupérer les réservations du lendemain
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $reservations = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name 
        WHERE reservation_date = %s 
        AND status = 'confirmed' 
        AND reminder_sent = 0",
        $tomorrow
    ));
    
    foreach ($reservations as $reservation) {
        le_margo_send_reminder_email($reservation->id);
    }
}
add_action('le_margo_send_reminders', 'le_margo_send_reminders'); 