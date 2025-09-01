<?php
/**
 * Script de test pour déboguer les réservations
 */

// Inclure WordPress
require_once '../../../wp-config.php';

// Fonction pour déboguer les réservations
function debug_reservations() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    echo "<h2>Debug des réservations</h2>\n";
    
    // Vérifier que la table existe
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if (!$table_exists) {
        echo "<p style='color: red;'>ERREUR: La table $table_name n'existe pas!</p>\n";
        return;
    }
    
    echo "<p>Table $table_name existe ✓</p>\n";
    
    // Compter toutes les réservations
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "<p>Nombre total de réservations: $total_count</p>\n";
    
    // Réservations du 06/06/2024
    $date_06_06 = '2024-06-06';
    $reservations_06_06 = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE reservation_date = %s ORDER BY reservation_time",
        $date_06_06
    ));
    
    echo "<h3>Réservations du $date_06_06:</h3>\n";
    if (empty($reservations_06_06)) {
        echo "<p style='color: orange;'>Aucune réservation trouvée pour le $date_06_06</p>\n";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Date</th><th>Heure</th><th>Service</th><th>Personnes</th><th>Statut</th><th>Client</th><th>Téléphone</th></tr>\n";
        foreach ($reservations_06_06 as $res) {
            echo "<tr>";
            echo "<td>{$res->id}</td>";
            echo "<td>{$res->reservation_date}</td>";
            echo "<td>{$res->reservation_time}</td>";
            echo "<td>{$res->meal_type}</td>";
            echo "<td>{$res->people}</td>";
            echo "<td>{$res->status}</td>";
            echo "<td>{$res->customer_name}</td>";
            echo "<td>{$res->customer_phone}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    // Réservations récentes
    echo "<h3>Dernières réservations (10 plus récentes):</h3>\n";
    $recent_reservations = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10"
    );
    
    if (empty($recent_reservations)) {
        echo "<p>Aucune réservation trouvée</p>\n";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Date</th><th>Heure</th><th>Service</th><th>Personnes</th><th>Statut</th><th>Client</th><th>Créé le</th></tr>\n";
        foreach ($recent_reservations as $res) {
            echo "<tr>";
            echo "<td>{$res->id}</td>";
            echo "<td>{$res->reservation_date}</td>";
            echo "<td>{$res->reservation_time}</td>";
            echo "<td>{$res->meal_type}</td>";
            echo "<td>{$res->people}</td>";
            echo "<td>{$res->status}</td>";
            echo "<td>{$res->customer_name}</td>";
            echo "<td>{$res->created_at}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    // Tester l'API de disponibilité
    echo "<h3>Test API de disponibilité pour le $date_06_06:</h3>\n";
    
    // Simuler l'appel API
    $_GET['date'] = $date_06_06;
    ob_start();
    le_margo_get_availability_callback();
    $api_output = ob_get_clean();
    
    echo "<pre>$api_output</pre>\n";
}

// Exécuter le debug
debug_reservations();
?> 