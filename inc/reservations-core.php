<?php
/**
 * Le Margo - Fonctions de base pour les réservations (règles, cron, etc.)
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Créer les tables nécessaires lors de l'activation du thème
 */
function le_margo_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Table des réservations
    $table_name = $wpdb->prefix . 'reservations';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        reservation_date date NOT NULL,
        reservation_time time NOT NULL,
        people int(11) NOT NULL,
        customer_name varchar(100) NOT NULL,
        customer_email varchar(100) NULL,
        customer_phone varchar(20) NULL,
        notes text NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        source varchar(20) NOT NULL DEFAULT 'public',
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        reminder_sent tinyint(1) NOT NULL DEFAULT 0,
        accept_reminder tinyint(1) NOT NULL DEFAULT 0,
        newsletter tinyint(1) NOT NULL DEFAULT 0,
        consent_data_processing tinyint(1) NOT NULL DEFAULT 0,
        consent_data_storage tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        KEY reservation_date (reservation_date),
        KEY status (status),
        KEY customer_email (customer_email)
    ) $charset_collate;";

    // Table des limites de taux
    $rate_limits_table = $wpdb->prefix . 'le_margo_rate_limits';
    $sql .= "CREATE TABLE IF NOT EXISTS $rate_limits_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        ip_address varchar(45) NOT NULL,
        attempt_count int(11) NOT NULL DEFAULT 1,
        last_attempt timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY ip_address (ip_address),
        KEY last_attempt (last_attempt)
    ) $charset_collate;";

    // Table des statistiques clients (définition unique et correcte)
    $stats_table = $wpdb->prefix . 'customer_stats';
    $sql .= "CREATE TABLE IF NOT EXISTS $stats_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        name varchar(100) NULL,
        visits int(11) NOT NULL DEFAULT 0,
        first_visit datetime NULL,
        last_visit datetime NULL,
        last_reservation_id bigint(20) NULL,
        is_vip tinyint(1) NOT NULL DEFAULT 0,
        consent_data_processing tinyint(1) NOT NULL DEFAULT 0,
        consent_data_storage tinyint(1) NOT NULL DEFAULT 0,
        accept_reminder tinyint(1) NOT NULL DEFAULT 0,
        newsletter tinyint(1) NOT NULL DEFAULT 0,
        consent_date datetime NULL,
        notes text NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email),
        KEY is_vip (is_vip)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(get_template_directory() . '/functions.php', 'le_margo_create_tables');

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

/**
 * Fonction pour réactiver le thème et recréer les tables si nécessaire
 */
function le_margo_maybe_recreate_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        le_margo_create_tables();
    }
}
add_action('after_switch_theme', 'le_margo_maybe_recreate_tables'); 

/**
 * Mettre à jour la structure de la base de données si nécessaire
 */
function le_margo_update_db_check() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // Vérifier si la colonne 'source' existe
    $column_exists = $wpdb->get_var($wpdb->prepare(
        "SHOW COLUMNS FROM `$table_name` LIKE %s", 
        'source'
    ));

    // Si la colonne n'existe pas, on l'ajoute
    if (empty($column_exists)) {
        $wpdb->query("ALTER TABLE `$table_name` ADD `source` VARCHAR(20) NOT NULL DEFAULT 'public' AFTER `status`");
    }
}
add_action('admin_init', 'le_margo_update_db_check'); 