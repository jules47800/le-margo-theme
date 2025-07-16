<?php
/**
 * Configuration d'exemple d'horaires pour Le Margo
 */

// Charger WordPress
require_once('../../../wp-load.php');

echo '<h1>Configuration d\'exemple d\'horaires</h1>';

// Exemple d'horaires pour un restaurant
$exemple_horaires = array(
    'monday' => array(
        'open' => true,
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:00')
        ),
        'slot_interval' => 15
    ),
    'tuesday' => array(
        'open' => true,
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:00')
        ),
        'slot_interval' => 15
    ),
    'wednesday' => array(
        'open' => true,
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:00')
        ),
        'slot_interval' => 15
    ),
    'thursday' => array(
        'open' => true,
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:00')
        ),
        'slot_interval' => 15
    ),
    'friday' => array(
        'open' => true,
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:30')
        ),
        'slot_interval' => 15
    ),
    'saturday' => array(
        'open' => true,
        'time_ranges' => array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:30')
        ),
        'slot_interval' => 15
    ),
    'sunday' => array(
        'open' => false,
        'time_ranges' => array(),
        'slot_interval' => 15
    )
);

// Vérifier si on veut appliquer la configuration
if (isset($_POST['appliquer_horaires'])) {
    update_option('le_margo_daily_schedule', $exemple_horaires);
    echo '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;">
        ✅ Horaires d\'exemple appliqués avec succès !
    </div>';
}

// Afficher les horaires actuels
$horaires_actuels = get_option('le_margo_daily_schedule', array());

echo '<h2>Horaires actuels</h2>';
if (empty($horaires_actuels)) {
    echo '<p style="color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px;">
        ⚠️ Aucun horaire configuré. Le système ne peut pas générer de créneaux.
    </p>';
} else {
    echo '<pre>' . print_r($horaires_actuels, true) . '</pre>';
}

echo '<h2>Horaires d\'exemple à appliquer</h2>';
echo '<pre>' . print_r($exemple_horaires, true) . '</pre>';

echo '<h2>Résumé des horaires d\'exemple</h2>';
$jours = array(
    'monday' => 'Lundi',
    'tuesday' => 'Mardi', 
    'wednesday' => 'Mercredi',
    'thursday' => 'Jeudi',
    'friday' => 'Vendredi',
    'saturday' => 'Samedi',
    'sunday' => 'Dimanche'
);

echo '<ul>';
foreach ($jours as $key => $label) {
    if (isset($exemple_horaires[$key]) && $exemple_horaires[$key]['open']) {
        $ranges = $exemple_horaires[$key]['time_ranges'];
        $ranges_text = array();
        foreach ($ranges as $range) {
            $ranges_text[] = $range['start'] . ' - ' . $range['end'];
        }
        $horaire = implode(' / ', $ranges_text);
        echo '<li><strong>' . $label . '</strong> : ' . $horaire . '</li>';
    } else {
        echo '<li><strong>' . $label . '</strong> : Fermé</li>';
    }
}
echo '</ul>';

echo '<h2>Créneaux générés (exemple pour le lundi)</h2>';
$lundi_slots = le_margo_get_available_slots_for_date('2025-01-13'); // Lundi
echo '<p>Nombre de créneaux : ' . count($lundi_slots) . '</p>';
echo '<ul>';
foreach ($lundi_slots as $slot) {
    echo '<li>' . $slot['time'] . '</li>';
}
echo '</ul>';

echo '<form method="post" style="margin: 30px 0;">
    <button type="submit" name="appliquer_horaires" style="background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
        🚀 Appliquer ces horaires d\'exemple
    </button>
</form>';

echo '<h2>Instructions</h2>';
echo '<ol>
    <li>Cliquez sur "Appliquer ces horaires d\'exemple" pour configurer un exemple</li>
    <li>Testez la page de réservation frontend</li>
    <li>Personnalisez les horaires dans l\'admin WordPress si nécessaire</li>
    <li>Supprimez ce fichier après configuration</li>
</ol>';

echo '<p><strong>Note :</strong> Ces horaires sont un exemple. Vous pouvez les modifier dans l\'admin WordPress sous "Réglages > Horaires d\'ouverture".</p>';
?> 