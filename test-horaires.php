<?php
/**
 * Test du nouveau système d'horaires - Le Margo
 * Fichier temporaire pour vérifier le fonctionnement
 */

// Inclure WordPress
require_once('../../../wp-load.php');

// Vérifier que l'utilisateur est admin
if (!current_user_can('manage_options')) {
    wp_die('Accès refusé');
}

echo "<h1>Test du système d'horaires - Le Margo</h1>";

// Test 1: Vérifier les paramètres sauvegardés
echo "<h2>1. Paramètres sauvegardés</h2>";
$schedule = get_option('le_margo_daily_schedule', array());
echo "<pre>";
print_r($schedule);
echo "</pre>";

// Test 2: Générer des créneaux pour une date spécifique
echo "<h2>2. Créneaux générés pour aujourd'hui</h2>";
$today = date('Y-m-d');
$slots = le_margo_get_available_slots_for_date($today);
echo "<p>Date: $today</p>";
echo "<p>Nombre de créneaux: " . count($slots) . "</p>";
echo "<pre>";
print_r($slots);
echo "</pre>";

// Test 3: Test avec différentes dates
echo "<h2>3. Test avec différentes dates</h2>";
$test_dates = array(
    '2024-01-15', // Lundi
    '2024-01-16', // Mardi
    '2024-01-17', // Mercredi
    '2024-01-18', // Jeudi
    '2024-01-19', // Vendredi
    '2024-01-20', // Samedi
    '2024-01-21', // Dimanche
);

foreach ($test_dates as $date) {
    $day_name = date('l', strtotime($date));
    $slots = le_margo_get_available_slots_for_date($date);
    echo "<p><strong>$day_name ($date):</strong> " . count($slots) . " créneaux</p>";
    if (count($slots) > 0) {
        echo "<ul>";
        foreach (array_slice($slots, 0, 5) as $slot) {
            echo "<li>{$slot['time']} ({$slot['meal_type']})</li>";
        }
        if (count($slots) > 5) {
            echo "<li>... et " . (count($slots) - 5) . " autres</li>";
        }
        echo "</ul>";
    }
}

// Test 4: Fonction de génération de créneaux
echo "<h2>4. Test de génération de créneaux</h2>";
$test_ranges = array(
    array('start' => '12:00', 'end' => '14:00', 'interval' => 30),
    array('start' => '19:00', 'end' => '22:00', 'interval' => 30),
    array('start' => '10:00', 'end' => '11:00', 'interval' => 15),
);

foreach ($test_ranges as $range) {
    $slots = le_margo_generate_time_slots($range['start'], $range['end'], $range['interval']);
    echo "<p><strong>{$range['start']} - {$range['end']} (intervalle: {$range['interval']}min):</strong></p>";
    echo "<p>" . implode(', ', $slots) . "</p>";
}

// Test 5: Validation des données
echo "<h2>5. Test de validation</h2>";
$test_input = array(
    'monday' => array(
        'open' => '1',
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:00')
        ),
        'slot_interval' => '30'
    ),
    'tuesday' => array(
        'open' => '1',
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00')
        ),
        'slot_interval' => '45'
    )
);

$sanitized = le_margo_sanitize_daily_schedule($test_input);
echo "<p><strong>Données testées:</strong></p>";
echo "<pre>";
print_r($test_input);
echo "</pre>";
echo "<p><strong>Données sanitizées:</strong></p>";
echo "<pre>";
print_r($sanitized);
echo "</pre>";

echo "<h2>Test terminé</h2>";
echo "<p><a href='" . admin_url('admin.php?page=le-margo-reservation-settings') . "'>Retour aux paramètres</a></p>";
?> 