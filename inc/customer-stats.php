<?php
/**
 * Gestion des statistiques clients pour Le Margo
 */

/**
 * Mettre à jour le nombre de visites d'un client
 */
function le_margo_update_customer_visits($customer_email, $reservation_id = null) {
    global $wpdb;
    $customers_table = $wpdb->prefix . 'customer_stats';
    $reservations_table = $wpdb->prefix . 'reservations';
    
    // Vérifier si le client existe déjà
    $existing_customer = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $customers_table WHERE email = %s",
        $customer_email
    ));
    
    // Récupérer les consentements RGPD depuis la réservation
    $consent_data_processing = 0;
    $consent_data_storage = 0;
    $customer_name = '';
    
    if ($reservation_id) {
        $reservation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $reservations_table WHERE id = %d",
            $reservation_id
        ));
        
        if ($reservation) {
            $consent_data_processing = isset($reservation->consent_data_processing) ? $reservation->consent_data_processing : 0;
            $consent_data_storage = isset($reservation->consent_data_storage) ? $reservation->consent_data_storage : 0;
            $customer_name = $reservation->customer_name;
        }
    }
    
    // Si pas de consentement au traitement des données, ne pas enregistrer les statistiques détaillées
    if (!$consent_data_processing) {
        return;
    }
    
    if ($existing_customer) {
        // Mettre à jour le nombre de visites et les consentements
        $wpdb->update(
            $customers_table,
            array(
                'visits' => $existing_customer->visits + 1,
                'last_visit' => current_time('mysql'),
                'last_reservation_id' => $reservation_id,
                'consent_data_processing' => $consent_data_processing,
                'consent_data_storage' => $consent_data_storage,
                'consent_date' => ($consent_data_processing || $consent_data_storage) ? current_time('mysql') : $existing_customer->consent_date
            ),
            array('email' => $customer_email)
        );
    } else {
        // Créer un nouveau client
        $wpdb->insert(
            $customers_table,
            array(
                'email' => $customer_email,
                'name' => $customer_name,
                'visits' => 1,
                'first_visit' => current_time('mysql'),
                'last_visit' => current_time('mysql'),
                'last_reservation_id' => $reservation_id,
                'consent_data_processing' => $consent_data_processing,
                'consent_data_storage' => $consent_data_storage,
                'consent_date' => ($consent_data_processing || $consent_data_storage) ? current_time('mysql') : null
            )
        );
    }
    
    // Vérifier si le client est devenu VIP (plus de 5 visites)
    $updated_customer = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $customers_table WHERE email = %s",
        $customer_email
    ));
    
    if ($updated_customer && $updated_customer->visits >= 5 && $updated_customer->is_vip == 0) {
        // Marquer comme VIP
        $wpdb->update(
            $customers_table,
            array('is_vip' => 1),
            array('email' => $customer_email)
        );
        
        // Envoyer un email de félicitations
        le_margo_send_vip_email($customer_email, $updated_customer->name);
    }
}

/**
 * Envoyer un email de félicitations au client qui devient VIP
 */
function le_margo_send_vip_email($email, $name) {
    $subject = __('Félicitations, vous êtes maintenant un client VIP du Margo !', 'le-margo');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $message = '<p>Bonjour ' . esc_html($name) . ',</p>';
    $message .= '<p>Toute l\'équipe du restaurant <strong>Le Margo</strong> vous remercie pour votre fidélité !</p>';
    $message .= '<p>Nous sommes ravis de vous compter parmi nos clients réguliers et nous vous accorderons une attention toute particulière lors de vos prochaines visites.</p>';
    $message .= '<p>À très bientôt,</p>';
    $message .= '<p>L\'équipe du Margo</p>';
    
    wp_mail($email, $subject, $message, $headers);
}

/**
 * Récupérer les statistiques globales des clients
 */
function le_margo_get_global_customer_stats() {
    global $wpdb;
    $customers_table = $wpdb->prefix . 'customer_stats';
    $reservations_table = $wpdb->prefix . 'reservations';
    
    // Nombre total de clients uniques
    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table");
    
    // Nombre de clients VIP
    $vip_customers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table WHERE is_vip = 1");
    
    // Nombre total de réservations
    $total_reservations = $wpdb->get_var("SELECT COUNT(*) FROM $reservations_table");
    
    // Client le plus fidèle
    $most_loyal_customer = $wpdb->get_row("SELECT * FROM $customers_table ORDER BY visits DESC LIMIT 1");
    
    // Moyenne de visites par client
    $avg_visits = $wpdb->get_var("SELECT AVG(visits) FROM $customers_table");
    
    // Taux de retour (clients avec plus d'une visite / total des clients)
    $returning_customers = $wpdb->get_var("SELECT COUNT(*) FROM $customers_table WHERE visits > 1");
    $return_rate = $total_customers > 0 ? ($returning_customers / $total_customers) * 100 : 0;
    
    // Nouveaux clients ce mois-ci
    $current_month_start = date('Y-m-01 00:00:00');
    $new_customers_this_month = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $customers_table WHERE first_visit >= %s",
        $current_month_start
    ));
    
    return array(
        'total_customers' => $total_customers,
        'vip_customers' => $vip_customers,
        'total_reservations' => $total_reservations,
        'most_loyal_customer' => $most_loyal_customer,
        'avg_visits' => round($avg_visits, 1),
        'return_rate' => round($return_rate, 1),
        'new_customers_this_month' => $new_customers_this_month
    );
}

/**
 * Récupérer la liste des clients VIP
 */
function le_margo_get_vip_customers($limit = 10) {
    global $wpdb;
    $customers_table = $wpdb->prefix . 'customer_stats';
    
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $customers_table 
        WHERE is_vip = 1 
        ORDER BY visits DESC, last_visit DESC 
        LIMIT %d",
        $limit
    ));
}

/**
 * Récupérer les clients récemment actifs
 */
function le_margo_get_recent_customers($limit = 10) {
    global $wpdb;
    $customers_table = $wpdb->prefix . 'customer_stats';
    
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $customers_table 
        ORDER BY last_visit DESC 
        LIMIT %d",
        $limit
    ));
}

/**
 * Récupérer des statistiques avancées pour le restaurant
 */
function le_margo_get_advanced_restaurant_stats($period = 'last_30_days', $custom_start = '', $custom_end = '') {
    global $wpdb;

    // Générer une clé de cache unique basée sur les paramètres
    $transient_key = 'le_margo_advanced_stats_' . md5($period . $custom_start . $custom_end);
    
    // Essayer de récupérer les données depuis le cache
    $cached_stats = get_transient($transient_key);
    if (false !== $cached_stats) {
        return $cached_stats;
    }

    $customers_table = $wpdb->prefix . 'customer_stats';
    $reservations_table = $wpdb->prefix . 'reservations';
    
    // Déterminer les dates de début et de fin en fonction de la période
    $end_date = date('Y-m-d');
    switch ($period) {
        case 'last_7_days':
            $start_date = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'last_90_days':
            $start_date = date('Y-m-d', strtotime('-90 days'));
            break;
        case 'this_year':
            $start_date = date('Y-01-01');
            break;
        case 'custom':
            $start_date = !empty($custom_start) ? $custom_start : date('Y-m-d', strtotime('-30 days'));
            $end_date = !empty($custom_end) ? $custom_end : date('Y-m-d');
            break;
        case 'last_30_days':
        default:
            $start_date = date('Y-m-d', strtotime('-30 days'));
            break;
    }
    
    // Statistiques d'occupation
    $total_seats = get_option('le_margo_restaurant_capacity', 50);

    // Clause WHERE pour la période
    $where_clause = $wpdb->prepare(
        "WHERE reservation_date BETWEEN %s AND %s",
        $start_date,
        $end_date
    );
    
    // Stats des réservations par jour de la semaine
    $weekday_stats = $wpdb->get_results(
        "SELECT 
            WEEKDAY(reservation_date) as weekday, 
            COUNT(*) as reservation_count,
            AVG(people) as avg_party_size
        FROM $reservations_table 
        $where_clause
        GROUP BY WEEKDAY(reservation_date)
        ORDER BY weekday ASC"
    );
    
    // Stats par service (déjeuner vs dîner)
    $service_stats = $wpdb->get_results(
        "SELECT 
            CASE 
                WHEN CAST(reservation_time AS TIME) < '18:00:00' THEN 'lunch'
                ELSE 'dinner'
            END as meal_type,
            COUNT(*) as reservation_count,
            SUM(people) as total_guests
        FROM $reservations_table 
        $where_clause
        GROUP BY meal_type"
    );
    
    // Taux d'occupation par jour
    $occupancy_data = le_margo_calculate_occupancy_data($start_date, $end_date);
    
    // Analyse des nouvelles réservations vs clients fidèles
    $customer_type_stats = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            r.reservation_date,
            SUM(CASE WHEN c.visits = 1 THEN 1 ELSE 0 END) as new_customers,
            SUM(CASE WHEN c.visits > 1 THEN 1 ELSE 0 END) as returning_customers
        FROM $reservations_table r
        JOIN $customers_table c ON r.customer_email = c.email
        WHERE r.reservation_date >= '$start_date'
        GROUP BY r.reservation_date
        ORDER BY r.reservation_date DESC"
    ));
    
    // Analyse des réservations mensuelles
    $monthly_stats = $wpdb->get_results(
        "SELECT 
            DATE_FORMAT(reservation_date, '%Y-%m') as month,
            COUNT(*) as reservation_count,
            SUM(people) as total_people,
            AVG(people) as avg_party_size
        FROM $reservations_table
        WHERE reservation_date >= '$start_date'
        GROUP BY DATE_FORMAT(reservation_date, '%Y-%m')
        ORDER BY month DESC"
    );
    
    // Analyse des annulations
    $cancellation_stats = $wpdb->get_results(
        "SELECT 
            DATE_FORMAT(reservation_date, '%Y-%m') as month,
            COUNT(*) as cancelled_count
        FROM $reservations_table
        WHERE status = 'cancelled' AND reservation_date >= '$start_date'
        GROUP BY DATE_FORMAT(reservation_date, '%Y-%m')
        ORDER BY month DESC"
    );
    
    // Créer un tableau des taux d'annulation par mois
    $cancellation_rates = array();
    foreach ($monthly_stats as $month_data) {
        $month = $month_data->month;
        $total_reservations = $month_data->reservation_count;
        
        $cancelled = 0;
        foreach ($cancellation_stats as $cancel_data) {
            if ($cancel_data->month === $month) {
                $cancelled = $cancel_data->cancelled_count;
                break;
            }
        }
        
        $cancellation_rates[$month] = array(
            'total' => $total_reservations,
            'cancelled' => $cancelled,
            'rate' => round(($cancelled / ($total_reservations + $cancelled)) * 100, 1)
        );
    }
    
    // Analyse des réservations par taille de groupe
    $group_size_stats = $wpdb->get_results(
        "SELECT 
            people as group_size,
            COUNT(*) as count
        FROM $reservations_table
        WHERE reservation_date >= '$start_date'
        GROUP BY people
        ORDER BY people ASC"
    );
    
    // Distribution des clients par nombre de visites
    $visit_distribution = $wpdb->get_results(
        "SELECT 
            visits,
            COUNT(*) as customer_count
        FROM $customers_table
        GROUP BY visits
        ORDER BY visits ASC"
    );
    
    // Calculer des métriques de rétention
    $retention_30days = $wpdb->get_var(
        "SELECT 
            COUNT(DISTINCT c.id) / (SELECT COUNT(*) FROM $customers_table) * 100
        FROM $customers_table c
        JOIN $reservations_table r ON c.email = r.customer_email
        WHERE 
            r.reservation_date >= DATE_SUB('$end_date', INTERVAL 30 DAY)
            AND c.first_visit < DATE_SUB('$end_date', INTERVAL 30 DAY)"
    );
    
    $retention_90days = $wpdb->get_var(
        "SELECT 
            COUNT(DISTINCT c.id) / (SELECT COUNT(*) FROM $customers_table) * 100
        FROM $customers_table c
        JOIN $reservations_table r ON c.email = r.customer_email
        WHERE 
            r.reservation_date >= DATE_SUB('$end_date', INTERVAL 90 DAY)
            AND c.first_visit < DATE_SUB('$end_date', INTERVAL 90 DAY)"
    );
    
    // Analyser le temps écoulé entre les réservations pour les clients fidèles
    $time_between_visits = $wpdb->get_var(
        "SELECT AVG(DATEDIFF(r2.reservation_date, r1.reservation_date))
        FROM $reservations_table r1
        JOIN $reservations_table r2 ON r1.customer_email = r2.customer_email
        WHERE 
            r1.id <> r2.id
            AND r1.reservation_date < r2.reservation_date
            AND r1.status <> 'cancelled'
            AND r2.status <> 'cancelled'
            AND r2.reservation_date = (
                SELECT MIN(r3.reservation_date)
                FROM $reservations_table r3
                WHERE 
                    r3.customer_email = r1.customer_email
                    AND r3.reservation_date > r1.reservation_date
                    AND r3.status <> 'cancelled'
            )"
    );
    
    $stats = array(
        'weekday_stats' => $weekday_stats,
        'service_stats' => $service_stats,
        'occupancy_data' => $occupancy_data,
        'new_vs_returning' => $customer_type_stats,
        'monthly_stats' => $monthly_stats,
        'cancellation_rates' => $cancellation_rates,
        'group_size_stats' => $group_size_stats,
        'visit_distribution' => $visit_distribution,
        'retention' => array(
            '30_days' => round($retention_30days, 1),
            '90_days' => round($retention_90days, 1)
        ),
        'avg_days_between_visits' => round($time_between_visits, 1),
        'no_show_rate' => 0, // This will be calculated below
        'no_show_count' => 0 // This will be calculated below
    );

    // NOUVEAU: Calcul du taux de no-show
    $no_show_stats = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            COUNT(CASE WHEN status = 'no-show' THEN 1 END) as no_show_count,
            COUNT(CASE WHEN status IN ('confirmed', 'completed', 'no-show') THEN 1 END) as total_relevant_reservations
        FROM {$reservations_table}
        WHERE reservation_date BETWEEN %s AND %s",
        $start_date,
        $end_date
    ));

    $stats['no_show_rate'] = 0;
    $stats['no_show_count'] = 0;
    if ($no_show_stats && $no_show_stats->total_relevant_reservations > 0) {
        $stats['no_show_count'] = (int)$no_show_stats->no_show_count;
        $stats['no_show_rate'] = round(($no_show_stats->no_show_count / $no_show_stats->total_relevant_reservations) * 100, 1);
    }
    
    // NOUVEAU: Analyse des sources de réservation
    $source_stats = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            source, 
            COUNT(*) as count 
        FROM {$reservations_table} 
        WHERE reservation_date BETWEEN %s AND %s AND status != 'cancelled'
        GROUP BY source",
        $start_date,
        $end_date
    ));

    $stats['source_stats'] = array(
        'public' => 0,
        'admin' => 0
    );

    if ($source_stats) {
        foreach ($source_stats as $source) {
            if (isset($stats['source_stats'][$source->source])) {
                $stats['source_stats'][$source->source] = (int)$source->count;
            }
        }
    }
    
    // Mettre les résultats en cache pendant 2 heures
    set_transient($transient_key, $stats, HOUR_IN_SECONDS);

    return $stats;
}

/**
 * Calcule dynamiquement le taux d'occupation pour une période donnée.
 */
function le_margo_calculate_occupancy_data($start_date, $end_date) {
    global $wpdb;
    $reservations_table = $wpdb->prefix . 'reservations';

    // Récupérer les données nécessaires en une seule fois
    $capacity_per_slot = get_option('le_margo_restaurant_capacity', 4);
    $daily_schedule = get_option('le_margo_daily_schedule');

    // Récupérer toutes les réservations confirmées pour la période
    $reservations = $wpdb->get_results($wpdb->prepare(
        "SELECT reservation_date, reservation_time, people 
         FROM $reservations_table 
         WHERE status = 'confirmed' AND reservation_date BETWEEN %s AND %s",
        $start_date, $end_date
    ));

    $daily_covers = [];
    foreach ($reservations as $res) {
        $date = $res->reservation_date;
        if (!isset($daily_covers[$date])) {
            $daily_covers[$date] = ['lunch' => 0, 'dinner' => 0, 'total' => 0];
        }
        $is_dinner = strtotime($res->reservation_time) >= strtotime('18:00');
        if ($is_dinner) {
            $daily_covers[$date]['dinner'] += $res->people;
        } else {
            $daily_covers[$date]['lunch'] += $res->people;
        }
        $daily_covers[$date]['total'] += $res->people;
    }

    $occupancy_data = [];
    $current_date = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);

    while ($current_date <= $end_date_obj) {
        $date_str = $current_date->format('Y-m-d');
        $day_key = strtolower($current_date->format('l')); // ex: 'monday'

        if (isset($daily_schedule[$day_key]) && $daily_schedule[$day_key]['open']) {
            $schedule = $daily_schedule[$day_key];
            $total_slots_lunch = 0;
            $total_slots_dinner = 0;

            foreach ($schedule['time_ranges'] as $range) {
                $start = new DateTime($range['start']);
                $end = new DateTime($range['end']);
                $interval = new DateInterval('PT' . $schedule['slot_interval'] . 'M');
                
                $is_dinner_range = $start->format('H') >= 18;

                $period = new DatePeriod($start, $interval, $end);
                $slot_count = iterator_count($period);

                if ($is_dinner_range) {
                    $total_slots_dinner += $slot_count;
                } else {
                    $total_slots_lunch += $slot_count;
                }
            }

            $capacity_lunch = $total_slots_lunch * $capacity_per_slot;
            $capacity_dinner = $total_slots_dinner * $capacity_per_slot;
            $total_capacity = $capacity_lunch + $capacity_dinner;

            $covers = isset($daily_covers[$date_str]) ? $daily_covers[$date_str] : ['lunch' => 0, 'dinner' => 0, 'total' => 0];

            $occupancy_data[$date_str] = [
                'lunch' => $capacity_lunch > 0 ? round(($covers['lunch'] / $capacity_lunch) * 100) : 0,
                'dinner' => $capacity_dinner > 0 ? round(($covers['dinner'] / $capacity_dinner) * 100) : 0,
                'overall' => $total_capacity > 0 ? round(($covers['total'] / $total_capacity) * 100) : 0,
            ];
        } else {
            // Jour fermé
            $occupancy_data[$date_str] = ['lunch' => 0, 'dinner' => 0, 'overall' => 0];
        }

        $current_date->modify('+1 day');
    }

    return $occupancy_data;
}

/**
 * Créer la table des statistiques clients
 */
function le_margo_create_customer_stats_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        name varchar(100) NOT NULL,
        visits int(11) NOT NULL DEFAULT 0,
        first_visit datetime NOT NULL,
        last_visit datetime NOT NULL,
        last_reservation_id bigint(20) NULL,
        is_vip tinyint(1) NOT NULL DEFAULT 0,
        consent_data_processing tinyint(1) NOT NULL DEFAULT 0,
        consent_data_storage tinyint(1) NOT NULL DEFAULT 0,
        consent_date datetime NULL,
        notes text NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email),
        KEY is_vip (is_vip)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'le_margo_create_customer_stats_table'); 