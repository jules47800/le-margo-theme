<?php
/**
 * Debug des réservations en base de données
 */

// Inclure WordPress
require_once('../../../wp-load.php');

// Vérifier que nous sommes connectés en tant qu'admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Accès refusé. Connectez-vous en tant qu\'administrateur.');
}

echo '<h1>Debug des réservations - Le Margo</h1>';
echo '<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { text-align: left; border: 1px solid #ddd; padding: 8px; }
th { background-color: #f5f5f5; }
.status-pending { background-color: #fff3cd; }
.status-confirmed { background-color: #d4edda; }
.status-cancelled { background-color: #f8d7da; }
</style>';

global $wpdb;
$table_name = $wpdb->prefix . 'reservations';

// 1. Afficher toutes les réservations récentes
echo '<h2>Toutes les réservations (7 derniers jours)</h2>';
$recent_reservations = $wpdb->get_results("
    SELECT id, reservation_date, reservation_time, meal_type, people, status, customer_name, created_at
    FROM $table_name 
    WHERE reservation_date >= CURDATE() - INTERVAL 7 DAY
    ORDER BY reservation_date DESC, reservation_time DESC
");

if ($recent_reservations) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Date</th><th>Heure</th><th>Service</th><th>Personnes</th><th>Statut</th><th>Client</th><th>Créé le</th></tr>';
    foreach ($recent_reservations as $res) {
        $status_class = 'status-' . $res->status;
        echo "<tr class='$status_class'>";
        echo "<td>{$res->id}</td>";
        echo "<td>{$res->reservation_date}</td>";
        echo "<td>{$res->reservation_time}</td>";
        echo "<td>{$res->meal_type}</td>";
        echo "<td>{$res->people}</td>";
        echo "<td>{$res->status}</td>";
        echo "<td>{$res->customer_name}</td>";
        echo "<td>{$res->created_at}</td>";
        echo "</tr>";
    }
    echo '</table>';
} else {
    echo '<p>Aucune réservation trouvée.</p>';
}

// 2. Test de la requête exacte utilisée par l'API
echo '<h2>Test de la requête API pour aujourd\'hui</h2>';
$today = date('Y-m-d');
echo "<p>Date testée: <strong>$today</strong></p>";

$api_results = $wpdb->get_results($wpdb->prepare(
    "SELECT reservation_time, meal_type, SUM(people) as total_people, COUNT(*) as reservation_count, GROUP_CONCAT(CONCAT('ID:', id, ' (', people, ' pers, ', status, ')') SEPARATOR ', ') as details
     FROM $table_name
     WHERE reservation_date = %s
     AND status != 'cancelled'
     GROUP BY reservation_time, meal_type",
    $today
));

echo '<h3>Résultats de la requête API</h3>';
if ($api_results) {
    echo '<table>';
    echo '<tr><th>Heure</th><th>Service</th><th>Total personnes</th><th>Nb réservations</th><th>Détails</th></tr>';
    foreach ($api_results as $res) {
        echo "<tr>";
        echo "<td>{$res->reservation_time}</td>";
        echo "<td>{$res->meal_type}</td>";
        echo "<td><strong>{$res->total_people}</strong></td>";
        echo "<td>{$res->reservation_count}</td>";
        echo "<td>{$res->details}</td>";
        echo "</tr>";
    }
    echo '</table>';
} else {
    echo '<p>Aucun résultat pour la requête API.</p>';
}

// 3. Test de la requête sans groupement pour voir toutes les réservations
echo '<h3>Toutes les réservations pour aujourd\'hui (sans groupement)</h3>';
$all_today = $wpdb->get_results($wpdb->prepare(
    "SELECT id, reservation_time, meal_type, people, status, customer_name
     FROM $table_name
     WHERE reservation_date = %s
     ORDER BY reservation_time, meal_type",
    $today
));

if ($all_today) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Heure</th><th>Service</th><th>Personnes</th><th>Statut</th><th>Client</th></tr>';
    foreach ($all_today as $res) {
        $status_class = 'status-' . $res->status;
        echo "<tr class='$status_class'>";
        echo "<td>{$res->id}</td>";
        echo "<td>{$res->reservation_time}</td>";
        echo "<td>{$res->meal_type}</td>";
        echo "<td>{$res->people}</td>";
        echo "<td>{$res->status}</td>";
        echo "<td>{$res->customer_name}</td>";
        echo "</tr>";
    }
    echo '</table>';
} else {
    echo '<p>Aucune réservation pour aujourd\'hui.</p>';
}

// 4. Simuler l'appel API
echo '<h2>Simulation de l\'appel API</h2>';
echo '<div id="api-test">Chargement...</div>';

?>

<script>
// Test AJAX direct
document.addEventListener('DOMContentLoaded', function() {
    const testDate = <?php echo json_encode($today); ?>;
    const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>?action=le_margo_get_availability&date=' + testDate;
    
    console.log('URL de test:', ajaxUrl);
    
    fetch(ajaxUrl)
        .then(response => {
            console.log('Statut réponse:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Réponse brute:', text);
            try {
                const data = JSON.parse(text);
                console.log('Données parsées:', data);
                
                const div = document.getElementById('api-test');
                if (data.success) {
                    div.innerHTML = '<h3>✅ API fonctionne</h3>' +
                        '<pre>' + JSON.stringify(data.data, null, 2) + '</pre>';
                } else {
                    div.innerHTML = '<h3>❌ Erreur API</h3>' +
                        '<p>' + (data.data ? data.data.message : 'Erreur inconnue') + '</p>' +
                        '<pre>' + text + '</pre>';
                }
            } catch (e) {
                document.getElementById('api-test').innerHTML = 
                    '<h3>❌ Erreur de parsing JSON</h3>' +
                    '<p>Erreur: ' + e.message + '</p>' +
                    '<pre>' + text + '</pre>';
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
            document.getElementById('api-test').innerHTML = 
                '<h3>❌ Erreur AJAX</h3>' +
                '<p>' + error.message + '</p>';
        });
});
</script> 