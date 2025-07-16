<?php
/**
 * Widget de tableau de bord pour les statistiques clients
 */

/**
 * Ajouter les widgets au tableau de bord
 */
function le_margo_add_customer_dashboard_widgets() {
    // Statistiques globales des clients
    wp_add_dashboard_widget(
        'le_margo_customer_stats_widget',        // ID unique
        __('Statistiques Clients - Le Margo', 'le-margo'),  // Titre
        'le_margo_customer_stats_widget_callback'       // Fonction de callback
    );
    
    // Liste des clients VIP
    wp_add_dashboard_widget(
        'le_margo_vip_customers_widget',        // ID unique
        __('Clients VIP - Le Margo', 'le-margo'),  // Titre
        'le_margo_vip_customers_widget_callback'       // Fonction de callback
    );
    
    // Derniers clients
    wp_add_dashboard_widget(
        'le_margo_recent_customers_widget',     // ID unique
        __('Clients Récents - Le Margo', 'le-margo'),  // Titre
        'le_margo_recent_customers_widget_callback'    // Fonction de callback
    );
}
add_action('wp_dashboard_setup', 'le_margo_add_customer_dashboard_widgets');

/**
 * Callback pour le widget de statistiques clients
 */
function le_margo_customer_stats_widget_callback() {
    // Récupérer les statistiques clients
    $stats = le_margo_get_global_customer_stats();
    
    // Styles du widget
    echo '<style>
        .customer-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        .customer-stat-card {
            background-color: #f9f9f9;
            border-left: 4px solid #9e8e7e;
            padding: 15px;
            border-radius: 4px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #3a3c36;
            margin: 5px 0;
        }
        .stat-label {
            color: #777;
            font-size: 13px;
        }
        .most-loyal {
            grid-column: span 2;
            background-color: #f5f0e9;
            border-left: 4px solid #e0a872;
        }
    </style>';
    
    // Afficher les statistiques
    echo '<div class="customer-stats-grid">';
    
    // Nombre total de clients
    echo '<div class="customer-stat-card">';
    echo '<div class="stat-value">' . esc_html($stats['total_customers']) . '</div>';
    echo '<div class="stat-label">' . __('Clients Total', 'le-margo') . '</div>';
    echo '</div>';
    
    // Clients VIP
    echo '<div class="customer-stat-card">';
    echo '<div class="stat-value">' . esc_html($stats['vip_customers']) . '</div>';
    echo '<div class="stat-label">' . __('Clients VIP', 'le-margo') . '</div>';
    echo '</div>';
    
    // Réservations totales
    echo '<div class="customer-stat-card">';
    echo '<div class="stat-value">' . esc_html($stats['total_reservations']) . '</div>';
    echo '<div class="stat-label">' . __('Réservations', 'le-margo') . '</div>';
    echo '</div>';
    
    // Nouveaux clients ce mois-ci
    echo '<div class="customer-stat-card">';
    echo '<div class="stat-value">' . esc_html($stats['new_customers_this_month']) . '</div>';
    echo '<div class="stat-label">' . __('Nouveaux clients (ce mois)', 'le-margo') . '</div>';
    echo '</div>';
    
    // Moyenne des visites
    echo '<div class="customer-stat-card">';
    echo '<div class="stat-value">' . esc_html($stats['avg_visits']) . '</div>';
    echo '<div class="stat-label">' . __('Moyenne des visites', 'le-margo') . '</div>';
    echo '</div>';
    
    // Taux de retour des clients
    echo '<div class="customer-stat-card">';
    echo '<div class="stat-value">' . esc_html($stats['return_rate']) . '%</div>';
    echo '<div class="stat-label">' . __('Taux de fidélité', 'le-margo') . '</div>';
    echo '</div>';
    
    // Client le plus fidèle
    if ($stats['most_loyal_customer']) {
        echo '<div class="customer-stat-card most-loyal">';
        echo '<div class="stat-label">' . __('Client le plus fidèle', 'le-margo') . '</div>';
        echo '<div class="stat-value">' . esc_html($stats['most_loyal_customer']->name) . '</div>';
        echo '<div class="stat-label">' . sprintf(
            __('%d visites - Dernière visite: %s', 'le-margo'),
            $stats['most_loyal_customer']->visits,
            date_i18n(get_option('date_format'), strtotime($stats['most_loyal_customer']->last_visit))
        ) . '</div>';
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Callback pour le widget des clients VIP
 */
function le_margo_vip_customers_widget_callback() {
    // Récupérer les clients VIP
    $vip_customers = le_margo_get_vip_customers(5);
    
    if (empty($vip_customers)) {
        echo '<p>' . __('Aucun client VIP pour le moment.', 'le-margo') . '</p>';
        return;
    }
    
    // Styles pour le tableau
    echo '<style>
        .vip-table {
            width: 100%;
            border-collapse: collapse;
        }
        .vip-table th, .vip-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .vip-table th {
            font-weight: 600;
            color: #3c434a;
        }
        .vip-badge {
            display: inline-block;
            background-color: #e0a872;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin-left: 5px;
        }
    </style>';
    
    // Tableau des clients VIP
    echo '<table class="vip-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Nom', 'le-margo') . '</th>';
    echo '<th>' . __('Email', 'le-margo') . '</th>';
    echo '<th>' . __('Visites', 'le-margo') . '</th>';
    echo '<th>' . __('Dernière visite', 'le-margo') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($vip_customers as $customer) {
        echo '<tr>';
        echo '<td>' . esc_html($customer->name) . ' <span class="vip-badge">VIP</span></td>';
        echo '<td>' . esc_html($customer->email) . '</td>';
        echo '<td>' . esc_html($customer->visits) . '</td>';
        echo '<td>' . date_i18n(get_option('date_format'), strtotime($customer->last_visit)) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    if (count($vip_customers) === 5) {
        echo '<p><a href="' . esc_url(admin_url('admin.php?page=le-margo-customers')) . '">' . __('Voir tous les clients VIP', 'le-margo') . ' &rarr;</a></p>';
    }
}

/**
 * Callback pour le widget des clients récents
 */
function le_margo_recent_customers_widget_callback() {
    // Récupérer les clients récents
    $recent_customers = le_margo_get_recent_customers(5);
    
    if (empty($recent_customers)) {
        echo '<p>' . __('Aucun client récent.', 'le-margo') . '</p>';
        return;
    }
    
    // Styles pour le tableau
    echo '<style>
        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-table th, .recent-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .recent-table th {
            font-weight: 600;
            color: #3c434a;
        }
    </style>';
    
    // Tableau des clients récents
    echo '<table class="recent-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Nom', 'le-margo') . '</th>';
    echo '<th>' . __('Email', 'le-margo') . '</th>';
    echo '<th>' . __('Visites', 'le-margo') . '</th>';
    echo '<th>' . __('Date', 'le-margo') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($recent_customers as $customer) {
        echo '<tr>';
        echo '<td>' . esc_html($customer->name);
        if ($customer->is_vip) {
            echo ' <span class="vip-badge">VIP</span>';
        }
        echo '</td>';
        echo '<td>' . esc_html($customer->email) . '</td>';
        echo '<td>' . esc_html($customer->visits) . '</td>';
        echo '<td>' . date_i18n(get_option('date_format'), strtotime($customer->last_visit)) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    if (count($recent_customers) === 5) {
        echo '<p><a href="' . esc_url(admin_url('admin.php?page=le-margo-customers')) . '">' . __('Voir tous les clients', 'le-margo') . ' &rarr;</a></p>';
    }
} 