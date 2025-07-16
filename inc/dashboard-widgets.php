<?php
/**
 * Fonctions pour les widgets du tableau de bord des réservations
 *
 * @package Le_Margo
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajout des métaboxes sur le tableau de bord WordPress
 */
function le_margo_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'le_margo_todays_reservations',
        __('Réservations du jour', 'le-margo'),
        'le_margo_todays_reservations_widget'
    );
    
    wp_add_dashboard_widget(
        'le_margo_upcoming_reservations',
        __('Prochaines réservations', 'le-margo'),
        'le_margo_upcoming_reservations_widget'
    );
}
add_action('wp_dashboard_setup', 'le_margo_add_dashboard_widgets');

/**
 * Widget du tableau de bord pour les réservations du jour
 */
function le_margo_todays_reservations_widget() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    $today = date('Y-m-d');
    
    $reservations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE reservation_date = %s 
            AND status != 'cancelled' 
            ORDER BY reservation_time ASC",
            $today
        )
    );
    
    if (empty($reservations)) {
        echo '<p>' . esc_html__('Aucune réservation pour aujourd\'hui.', 'le-margo') . '</p>';
        return;
    }
    
    echo '<table class="widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . esc_html__('Heure', 'le-margo') . '</th>';
    echo '<th>' . esc_html__('Service', 'le-margo') . '</th>';
    echo '<th>' . esc_html__('Personnes', 'le-margo') . '</th>';
    echo '<th>' . esc_html__('Statut', 'le-margo') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($reservations as $reservation) {
        // Formater l'heure
        $time_obj = new DateTime($reservation->reservation_time);
        $formatted_time = $time_obj->format('H:i');
        
        // Type de repas
        $meal_type = $reservation->meal_type === 'lunch' ? __('Déjeuner', 'le-margo') : __('Dîner', 'le-margo');
        
        // Statut
        $status_label = $reservation->status === 'pending' ? __('En attente', 'le-margo') : __('Confirmé', 'le-margo');
        $status_style = $reservation->status === 'pending' ? 'color: #f39c12;' : 'color: #27ae60;';
        
        echo '<tr>';
        echo '<td>' . esc_html($formatted_time) . '</td>';
        echo '<td>' . esc_html($meal_type) . '</td>';
        echo '<td>' . esc_html($reservation->people) . '</td>';
        echo '<td><span style="' . esc_attr($status_style) . '">' . esc_html($status_label) . '</span></td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    echo '<p class="textright">';
    echo '<a href="' . esc_url(admin_url('admin.php?page=le-margo-reservations&date_filter=' . $today)) . '">' . esc_html__('Voir toutes les réservations d\'aujourd\'hui', 'le-margo') . '</a>';
    echo '</p>';
}

/**
 * Widget du tableau de bord pour les prochaines réservations
 */
function le_margo_upcoming_reservations_widget() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    $today = date('Y-m-d');
    
    $reservations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE (reservation_date > %s OR (reservation_date = %s AND reservation_time > %s)) 
            AND status != 'cancelled' 
            ORDER BY reservation_date ASC, reservation_time ASC
            LIMIT 5",
            $today, $today, date('H:i:s')
        )
    );
    
    if (empty($reservations)) {
        echo '<p>' . esc_html__('Aucune réservation à venir.', 'le-margo') . '</p>';
        return;
    }
    
    echo '<table class="widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . esc_html__('Date', 'le-margo') . '</th>';
    echo '<th>' . esc_html__('Heure', 'le-margo') . '</th>';
    echo '<th>' . esc_html__('Service', 'le-margo') . '</th>';
    echo '<th>' . esc_html__('Personnes', 'le-margo') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($reservations as $reservation) {
        // Formater la date
        $date_obj = new DateTime($reservation->reservation_date);
        $formatted_date = $date_obj->format('d/m/Y');
        
        // Formater l'heure
        $time_obj = new DateTime($reservation->reservation_time);
        $formatted_time = $time_obj->format('H:i');
        
        // Type de repas
        $meal_type = $reservation->meal_type === 'lunch' ? __('Déjeuner', 'le-margo') : __('Dîner', 'le-margo');
        
        echo '<tr>';
        echo '<td>' . esc_html($formatted_date) . '</td>';
        echo '<td>' . esc_html($formatted_time) . '</td>';
        echo '<td>' . esc_html($meal_type) . '</td>';
        echo '<td>' . esc_html($reservation->people) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    echo '<p class="textright">';
    echo '<a href="' . esc_url(admin_url('admin.php?page=le-margo-reservations')) . '">' . esc_html__('Voir toutes les réservations', 'le-margo') . '</a>';
    echo '</p>';
} 