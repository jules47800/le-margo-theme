<?php
/**
 * Page d'administration des clients pour Le Margo
 */

/**
 * Ajouter le menu d'administration
 */
function le_margo_add_customers_menu() {
    add_menu_page(
        __('Clients du Margo', 'le-margo'),
        __('Clients', 'le-margo'),
        'manage_options',
        'le-margo-customers',
        'le_margo_customers_page',
        'dashicons-groups',
        25
    );
}
add_action('admin_menu', 'le_margo_add_customers_menu');

/**
 * Page d'administration des clients
 */
function le_margo_customers_page() {
    // Gérer l'action de resynchronisation
    if (isset($_GET['action']) && $_GET['action'] === 'resync_stats' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'resync_customer_stats')) {
        if (function_exists('le_margo_resync_customer_stats')) {
            le_margo_resync_customer_stats();
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Les statistiques des clients ont été synchronisées avec succès.', 'le-margo') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Erreur : la fonction de synchronisation est introuvable.', 'le-margo') . '</p></div>';
        }
    }

    // Gérer les actions sur un client
    if (isset($_GET['action']) && isset($_GET['customer_id']) && is_numeric($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        
        // Action pour marquer/démarquer comme VIP
        if ($_GET['action'] === 'toggle_vip' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'toggle_vip_' . $customer_id)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'customer_stats';
            
            $customer = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $customer_id
            ));
            
            if ($customer) {
                $wpdb->update(
                    $table_name,
                    array('is_vip' => $customer->is_vip ? 0 : 1),
                    array('id' => $customer_id)
                );
                
                echo '<div class="notice notice-success is-dismissible"><p>';
                if (!$customer->is_vip) {
                    echo esc_html(sprintf(__('Client %s marqué comme VIP.', 'le-margo'), $customer->name));
                } else {
                    echo esc_html(sprintf(__('Client %s n\'est plus VIP.', 'le-margo'), $customer->name));
                }
                echo '</p></div>';
            }
        }
        
        // Action pour ajouter une visite manuelle
        if ($_GET['action'] === 'add_visit' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'add_visit_' . $customer_id)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'customer_stats';
            
            $customer = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $customer_id
            ));
            
            if ($customer) {
                $wpdb->update(
                    $table_name,
                    array(
                        'visits' => $customer->visits + 1,
                        'last_visit' => current_time('mysql')
                    ),
                    array('id' => $customer_id)
                );
                
                // Si le client atteint 5 visites, vérifier le statut VIP
                if ($customer->visits + 1 >= 5 && !$customer->is_vip) {
                    $wpdb->update(
                        $table_name,
                        array('is_vip' => 1),
                        array('id' => $customer_id)
                    );
                    
                    echo '<div class="notice notice-success is-dismissible"><p>';
                    echo esc_html(sprintf(__('Le client %s a atteint 5 visites et est maintenant VIP !', 'le-margo'), $customer->name));
                    echo '</p></div>';
                } else {
                    echo '<div class="notice notice-success is-dismissible"><p>';
                    echo esc_html(sprintf(__('Visite ajoutée pour %s.', 'le-margo'), $customer->name));
                    echo '</p></div>';
                }
            }
        }
        
        // Action pour voir le détail d'un client
        if ($_GET['action'] === 'view_detail') {
            le_margo_display_customer_detail($customer_id);
            return;
        }
        
        // Action pour éditer un client
        if ($_GET['action'] === 'edit_customer') {
            le_margo_display_customer_edit_form($customer_id);
            return;
        }
        
        // Action pour sauvegarder les modifications d'un client
        if ($_GET['action'] === 'save_customer' && isset($_POST['customer_data'])) {
            le_margo_save_customer_data($customer_id);
            return;
        }
    }

    // Déterminer l'onglet actif
    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'tous';
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Gestion des Clients - Le Margo', 'le-margo'); ?></h1>
        
        <!-- Onglets de navigation -->
        <nav class="nav-tab-wrapper">
            <a href="?page=le-margo-customers&tab=tous" class="nav-tab <?php echo $active_tab === 'tous' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Tous les clients', 'le-margo'); ?>
            </a>
            <a href="?page=le-margo-customers&tab=vip" class="nav-tab <?php echo $active_tab === 'vip' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Clients VIP', 'le-margo'); ?>
            </a>
            <a href="?page=le-margo-customers&tab=recent" class="nav-tab <?php echo $active_tab === 'recent' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Clients récents', 'le-margo'); ?>
            </a>
            <a href="?page=le-margo-customers&tab=gdpr" class="nav-tab <?php echo $active_tab === 'gdpr' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Consentements GDPR', 'le-margo'); ?>
            </a>
        </nav>
        
        <!-- Statistiques en en-tête -->
        <?php le_margo_display_customer_stats_header(); ?>
        
        <!-- Contenu selon l'onglet -->
        <?php
        switch ($active_tab) {
            case 'vip':
                le_margo_display_vip_customers_table();
                break;
            case 'recent':
                le_margo_display_recent_customers_table();
                break;
            case 'gdpr':
                le_margo_display_gdpr_customers_table();
                break;
            default:
                le_margo_display_all_customers_table();
                break;
        }
        ?>
    </div>
    
    <style>
        .customer-filters {
            background: white;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .customer-filters form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .customer-filters input,
        .customer-filters select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .customer-filters .button {
            margin: 0;
        }
        .customer-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .customer-actions .button {
            font-size: 11px;
            padding: 2px 8px;
            height: auto;
        }
        .vip-status {
            background: #e0a872;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .regular-status {
            background: #95a5a6;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .customer-detail-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .customer-detail-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .customer-detail-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .customer-detail-close:hover {
            color: #000;
        }
        .customer-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .customer-detail-section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
        }
        .customer-detail-section h3 {
            margin-top: 0;
            color: #e0a872;
        }
        .customer-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .customer-detail-row:last-child {
            border-bottom: none;
        }
        .customer-detail-label {
            font-weight: bold;
            color: #666;
        }
        .customer-detail-value {
            color: #333;
        }
        .customer-edit-form {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 20px 0;
        }
        .customer-edit-form .form-table th {
            width: 200px;
        }
        .customer-edit-form input[type="text"],
        .customer-edit-form input[type="email"],
        .customer-edit-form textarea {
            width: 100%;
            max-width: 400px;
        }
        .customer-edit-form .button-primary {
            margin-top: 15px;
        }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des modales de détail client
        const detailLinks = document.querySelectorAll('.customer-detail-link');
        detailLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const customerId = this.getAttribute('data-customer-id');
                showCustomerDetail(customerId);
            });
        });
        
        // Fermeture des modales
        const closeButtons = document.querySelectorAll('.customer-detail-close');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.customer-detail-modal').style.display = 'none';
            });
        });
        
        // Fermer la modale en cliquant à l'extérieur
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('customer-detail-modal')) {
                e.target.style.display = 'none';
            }
        });
    });
    
    function showCustomerDetail(customerId) {
        // Charger les détails du client via AJAX
        fetch(ajaxurl + '?action=le_margo_get_customer_detail&customer_id=' + customerId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('customer-detail-content').innerHTML = data.data;
                    document.getElementById('customer-detail-modal').style.display = 'block';
                }
            });
    }
    </script>
    <?php
}

/**
 * Afficher les statistiques en haut de la page clients
 */
function le_margo_display_customer_stats_header() {
    $stats = le_margo_get_global_customer_stats();
    ?>
    <div class="customer-stats-header">
        <style>
            .customer-stats-header {
                display: flex;
                flex-wrap: wrap;
                margin: 20px 0;
                gap: 20px;
            }
            .stat-box {
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px 20px;
                text-align: center;
                flex: 1;
                min-width: 120px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .stat-number {
                font-size: 28px;
                font-weight: bold;
                color: #3a3c36;
                margin: 5px 0;
            }
            .stat-title {
                color: #777;
                font-size: 14px;
            }
            .vip-stat {
                background-color: #f5f0e9;
                border-color: #e0a872;
            }
        </style>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo esc_html($stats['total_customers']); ?></div>
            <div class="stat-title"><?php echo esc_html__('Clients Total', 'le-margo'); ?></div>
        </div>
        
        <div class="stat-box vip-stat">
            <div class="stat-number"><?php echo esc_html($stats['vip_customers']); ?></div>
            <div class="stat-title"><?php echo esc_html__('Clients VIP', 'le-margo'); ?></div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo esc_html($stats['total_reservations']); ?></div>
            <div class="stat-title"><?php echo esc_html__('Réservations', 'le-margo'); ?></div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo esc_html($stats['avg_visits']); ?></div>
            <div class="stat-title"><?php echo esc_html__('Moy. Visites', 'le-margo'); ?></div>
        </div>
        
        <div class="stat-box">
            <div class="stat-number"><?php echo esc_html($stats['return_rate']); ?>%</div>
            <div class="stat-title"><?php echo esc_html__('Taux de Fidélité', 'le-margo'); ?></div>
        </div>
    </div>
    <?php
}

/**
 * Afficher le tableau de tous les clients
 */
function le_margo_display_all_customers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    // Filtres
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    $visits_filter = isset($_GET['visits']) ? sanitize_text_field($_GET['visits']) : '';
    $date_filter = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : '';
    
    // Construire la requête avec filtres
    $where_conditions = array();
    $where_values = array();
    
    if (!empty($search)) {
        $where_conditions[] = "(name LIKE %s OR email LIKE %s)";
        $where_values[] = '%' . $wpdb->esc_like($search) . '%';
        $where_values[] = '%' . $wpdb->esc_like($search) . '%';
    }
    
    if (!empty($status_filter)) {
        if ($status_filter === 'vip') {
            $where_conditions[] = "is_vip = 1";
        } elseif ($status_filter === 'regular') {
            $where_conditions[] = "is_vip = 0";
        }
    }
    
    if (!empty($visits_filter)) {
        if ($visits_filter === '1-2') {
            $where_conditions[] = "visits BETWEEN 1 AND 2";
        } elseif ($visits_filter === '3-4') {
            $where_conditions[] = "visits BETWEEN 3 AND 4";
        } elseif ($visits_filter === '5+') {
            $where_conditions[] = "visits >= 5";
        }
    }
    
    if (!empty($date_filter)) {
        if ($date_filter === 'last_week') {
            $where_conditions[] = "last_visit >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($date_filter === 'last_month') {
            $where_conditions[] = "last_visit >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } elseif ($date_filter === 'last_3months') {
            $where_conditions[] = "last_visit >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
        }
    }
    
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Récupérer le nombre total de clients avec filtres
    $count_query = "SELECT COUNT(*) FROM $table_name $where_clause";
    if (!empty($where_values)) {
        $total_customers = $wpdb->get_var($wpdb->prepare($count_query, $where_values));
    } else {
        $total_customers = $wpdb->get_var($count_query);
    }
    
    // Récupérer les clients pour cette page avec filtres
    $query = "SELECT * FROM $table_name $where_clause ORDER BY last_visit DESC LIMIT %d OFFSET %d";
    $query_values = array_merge($where_values, array($per_page, $offset));
    
    if (!empty($query_values)) {
        $customers = $wpdb->get_results($wpdb->prepare($query, $query_values));
    } else {
        $customers = $wpdb->get_results($wpdb->prepare($query, $per_page, $offset));
    }
    
    // Calculer le nombre total de pages
    $total_pages = ceil($total_customers / $per_page);
    
    ?>
    
    <!-- Filtres de recherche -->
    <div class="customer-filters">
        <form method="GET" action="">
            <input type="hidden" name="page" value="le-margo-customers">
            <input type="hidden" name="tab" value="tous">
            
            <input type="text" name="search" value="<?php echo esc_attr($search); ?>" placeholder="<?php echo esc_attr__('Rechercher par nom ou email...', 'le-margo'); ?>">
            
            <select name="status">
                <option value=""><?php echo esc_html__('Tous les statuts', 'le-margo'); ?></option>
                <option value="vip" <?php selected($status_filter, 'vip'); ?>><?php echo esc_html__('VIP uniquement', 'le-margo'); ?></option>
                <option value="regular" <?php selected($status_filter, 'regular'); ?>><?php echo esc_html__('Réguliers uniquement', 'le-margo'); ?></option>
            </select>
            
            <select name="visits">
                <option value=""><?php echo esc_html__('Toutes les visites', 'le-margo'); ?></option>
                <option value="1-2" <?php selected($visits_filter, '1-2'); ?>><?php echo esc_html__('1-2 visites', 'le-margo'); ?></option>
                <option value="3-4" <?php selected($visits_filter, '3-4'); ?>><?php echo esc_html__('3-4 visites', 'le-margo'); ?></option>
                <option value="5+" <?php selected($visits_filter, '5+'); ?>><?php echo esc_html__('5+ visites', 'le-margo'); ?></option>
            </select>
            
            <select name="date_filter">
                <option value=""><?php echo esc_html__('Toutes les dates', 'le-margo'); ?></option>
                <option value="last_week" <?php selected($date_filter, 'last_week'); ?>><?php echo esc_html__('Dernière semaine', 'le-margo'); ?></option>
                <option value="last_month" <?php selected($date_filter, 'last_month'); ?>><?php echo esc_html__('Dernier mois', 'le-margo'); ?></option>
                <option value="last_3months" <?php selected($date_filter, 'last_3months'); ?>><?php echo esc_html__('Derniers 3 mois', 'le-margo'); ?></option>
            </select>
            
            <button type="submit" class="button"><?php echo esc_html__('Filtrer', 'le-margo'); ?></button>
            <a href="?page=le-margo-customers&tab=tous" class="button"><?php echo esc_html__('Réinitialiser', 'le-margo'); ?></a>
        </form>
    </div>
    
    <div class="tablenav top">
        <div class="tablenav-pages">
            <span class="displaying-num">
                <?php printf(_n('%s client trouvé', '%s clients trouvés', $total_customers, 'le-margo'), number_format_i18n($total_customers)); ?>
            </span>
            <?php if ($total_pages > 1) : ?>
                <span class="pagination-links">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                    ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php echo esc_html__('Nom', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Email', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Visites', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Première visite', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Dernière visite', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Statut', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Consentements', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Actions', 'le-margo'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)) : ?>
                <tr>
                    <td colspan="7"><?php echo esc_html__('Aucun client trouvé.', 'le-margo'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($customers as $customer) : ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($customer->name); ?></strong>
                            <?php if ($customer->visits >= 5) : ?>
                                <span class="dashicons dashicons-star-filled" style="color: #e0a872;" title="<?php echo esc_attr__('Client fidèle', 'le-margo'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="mailto:<?php echo esc_attr($customer->email); ?>"><?php echo esc_html($customer->email); ?></a>
                        </td>
                        <td>
                            <strong><?php echo esc_html($customer->visits); ?></strong>
                            <?php if ($customer->visits >= 5) : ?>
                                <br><small style="color: #e0a872;"><?php echo esc_html__('VIP éligible', 'le-margo'); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($customer->first_visit)); ?></td>
                        <td>
                            <?php echo date_i18n(get_option('date_format'), strtotime($customer->last_visit)); ?>
                            <?php 
                            $days_since_last_visit = floor((time() - strtotime($customer->last_visit)) / (60 * 60 * 24));
                            if ($days_since_last_visit > 30) : ?>
                                <br><small style="color: #dc3232;"><?php echo sprintf(esc_html__('Il y a %d jours', 'le-margo'), $days_since_last_visit); ?></small>
                            <?php elseif ($days_since_last_visit > 7) : ?>
                                <br><small style="color: #f39c12;"><?php echo sprintf(esc_html__('Il y a %d jours', 'le-margo'), $days_since_last_visit); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($customer->is_vip) : ?>
                                <span class="vip-status"><?php echo esc_html__('VIP', 'le-margo'); ?></span>
                            <?php else : ?>
                                <span class="regular-status"><?php echo esc_html__('Régulier', 'le-margo'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($customer->consent_data_processing) : ?>
                                <span class="dashicons dashicons-yes" style="color: #27ae60;" title="<?php echo esc_attr__('Traitement des données accepté', 'le-margo'); ?>"></span>
                            <?php else : ?>
                                <span class="dashicons dashicons-no" style="color: #dc3232;" title="<?php echo esc_attr__('Traitement des données refusé', 'le-margo'); ?>"></span>
                            <?php endif; ?>
                            
                            <?php if ($customer->newsletter) : ?>
                                <span class="dashicons dashicons-email-alt" style="color: #3498db;" title="<?php echo esc_attr__('Newsletter acceptée', 'le-margo'); ?>"></span>
                            <?php endif; ?>
                            
                            <?php if ($customer->accept_reminder) : ?>
                                <span class="dashicons dashicons-bell" style="color: #f39c12;" title="<?php echo esc_attr__('Rappels acceptés', 'le-margo'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td class="customer-actions">
                            <?php 
                            $view_detail_url = add_query_arg(
                                array(
                                    'action' => 'view_detail',
                                    'customer_id' => $customer->id,
                                    'tab' => 'tous',
                                )
                            );
                            
                            $edit_url = add_query_arg(
                                array(
                                    'action' => 'edit_customer',
                                    'customer_id' => $customer->id,
                                    'tab' => 'tous',
                                )
                            );
                            
                            $toggle_vip_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'toggle_vip',
                                        'customer_id' => $customer->id,
                                        'tab' => 'tous',
                                    )
                                ),
                                'toggle_vip_' . $customer->id
                            );
                            
                            $add_visit_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'add_visit',
                                        'customer_id' => $customer->id,
                                        'tab' => 'tous',
                                    )
                                ),
                                'add_visit_' . $customer->id
                            );
                            ?>
                            <a href="<?php echo esc_url($view_detail_url); ?>" class="button button-small" title="<?php echo esc_attr__('Voir les détails', 'le-margo'); ?>">
                                <span class="dashicons dashicons-visibility"></span>
                            </a>
                            <a href="<?php echo esc_url($edit_url); ?>" class="button button-small" title="<?php echo esc_attr__('Modifier', 'le-margo'); ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </a>
                            <a href="<?php echo esc_url($toggle_vip_url); ?>" class="button button-small" title="<?php echo $customer->is_vip ? esc_attr__('Retirer VIP', 'le-margo') : esc_attr__('Marquer VIP', 'le-margo'); ?>">
                                <?php echo $customer->is_vip ? esc_html__('VIP', 'le-margo') : esc_html__('VIP', 'le-margo'); ?>
                            </a>
                            <a href="<?php echo esc_url($add_visit_url); ?>" class="button button-small" title="<?php echo esc_attr__('Ajouter une visite', 'le-margo'); ?>">
                                <span class="dashicons dashicons-plus"></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Afficher le tableau des clients VIP
 */
function le_margo_display_vip_customers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    // Récupérer les clients VIP
    $vip_customers = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE is_vip = 1 ORDER BY visits DESC, last_visit DESC"
    );
    
    ?>
    <style>
        .vip-status {
            display: inline-block;
            background-color: #e0a872;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .regular-status {
            display: inline-block;
            background-color: #ddd;
            color: #666;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php echo esc_html__('Nom', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Email', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Visites', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Première visite', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Dernière visite', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Actions', 'le-margo'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vip_customers)) : ?>
                <tr>
                    <td colspan="6"><?php echo esc_html__('Aucun client VIP trouvé.', 'le-margo'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($vip_customers as $customer) : ?>
                    <tr>
                        <td><?php echo esc_html($customer->name); ?></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td><?php echo esc_html($customer->visits); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($customer->first_visit)); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($customer->last_visit)); ?></td>
                        <td>
                            <?php 
                            $toggle_vip_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'toggle_vip',
                                        'customer_id' => $customer->id,
                                        'tab' => 'vip',
                                    )
                                ),
                                'toggle_vip_' . $customer->id
                            );
                            
                            $add_visit_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'add_visit',
                                        'customer_id' => $customer->id,
                                        'tab' => 'vip',
                                    )
                                ),
                                'add_visit_' . $customer->id
                            );
                            ?>
                            <a href="<?php echo esc_url($toggle_vip_url); ?>" class="button button-small">
                                <?php echo esc_html__('Retirer VIP', 'le-margo'); ?>
                            </a>
                            <a href="<?php echo esc_url($add_visit_url); ?>" class="button button-small">
                                <?php echo esc_html__('Ajouter visite', 'le-margo'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Afficher le tableau des clients avec visite récente
 */
function le_margo_display_recent_customers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    // Récupérer les clients avec visite récente (dernier mois)
    $one_month_ago = date('Y-m-d', strtotime('-1 month'));
    $recent_customers = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE last_visit >= %s ORDER BY last_visit DESC",
        $one_month_ago
    ));
    
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php echo esc_html__('Nom', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Email', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Visites', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Dernière visite', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Statut', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Actions', 'le-margo'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recent_customers)) : ?>
                <tr>
                    <td colspan="6"><?php echo esc_html__('Aucune visite récente trouvée.', 'le-margo'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($recent_customers as $customer) : ?>
                    <tr>
                        <td><?php echo esc_html($customer->name); ?></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td><?php echo esc_html($customer->visits); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($customer->last_visit)); ?></td>
                        <td>
                            <?php if ($customer->is_vip) : ?>
                                <span class="vip-status"><?php echo esc_html__('VIP', 'le-margo'); ?></span>
                            <?php else : ?>
                                <span class="regular-status"><?php echo esc_html__('Régulier', 'le-margo'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $toggle_vip_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'toggle_vip',
                                        'customer_id' => $customer->id,
                                        'tab' => 'recents',
                                    )
                                ),
                                'toggle_vip_' . $customer->id
                            );
                            
                            $add_visit_url = wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'add_visit',
                                        'customer_id' => $customer->id,
                                        'tab' => 'recents',
                                    )
                                ),
                                'add_visit_' . $customer->id
                            );
                            ?>
                            <a href="<?php echo esc_url($toggle_vip_url); ?>" class="button button-small">
                                <?php echo $customer->is_vip ? esc_html__('Retirer VIP', 'le-margo') : esc_html__('Marquer VIP', 'le-margo'); ?>
                            </a>
                            <a href="<?php echo esc_url($add_visit_url); ?>" class="button button-small">
                                <?php echo esc_html__('Ajouter visite', 'le-margo'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Afficher le tableau des clients avec consentement RGPD
 */
function le_margo_display_gdpr_customers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    // Traitement des révocations de consentement
    if (isset($_GET['action']) && $_GET['action'] === 'revoke_consent' && isset($_GET['customer_id']) && is_numeric($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
        
        // Vérifier le nonce
        if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'revoke_consent_' . $customer_id)) {
            $wpdb->update(
                $table_name,
                array(
                    'consent_data_processing' => 0,
                    'consent_data_storage' => 0,
                    'consent_date' => null
                ),
                array('id' => $customer_id)
            );
            
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo esc_html__('Consentements RGPD révoqués avec succès.', 'le-margo');
            echo '</p></div>';
        }
    }
    
    // Pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Statistiques RGPD
    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $consented_processing = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE consent_data_processing = 1");
    $consented_storage = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE consent_data_storage = 1");
    $consent_processing_rate = $total_customers > 0 ? round(($consented_processing / $total_customers) * 100, 1) : 0;
    $consent_storage_rate = $total_customers > 0 ? round(($consented_storage / $total_customers) * 100, 1) : 0;
    
    ?>
    <div class="rgpd-stats">
        <style>
            .rgpd-stats {
                display: flex;
                gap: 20px;
                margin: 20px 0;
            }
            .rgpd-stat-box {
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px 20px;
                text-align: center;
                flex: 1;
                min-width: 100px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .rgpd-stat-number {
                font-size: 24px;
                font-weight: bold;
                color: #3a3c36;
                margin: 5px 0;
            }
            .rgpd-stat-title {
                color: #777;
                font-size: 14px;
            }
        </style>
        
        <div class="rgpd-stat-box">
            <div class="rgpd-stat-number"><?php echo esc_html($total_customers); ?></div>
            <div class="rgpd-stat-title"><?php echo esc_html__('Clients Total', 'le-margo'); ?></div>
        </div>
        
        <div class="rgpd-stat-box">
            <div class="rgpd-stat-number"><?php echo esc_html($consented_processing); ?></div>
            <div class="rgpd-stat-title"><?php echo esc_html__('Consentement Traitement', 'le-margo'); ?></div>
            <div class="rgpd-stat-rate"><?php echo esc_html($consent_processing_rate); ?>%</div>
        </div>
        
        <div class="rgpd-stat-box">
            <div class="rgpd-stat-number"><?php echo esc_html($consented_storage); ?></div>
            <div class="rgpd-stat-title"><?php echo esc_html__('Consentement Stockage', 'le-margo'); ?></div>
            <div class="rgpd-stat-rate"><?php echo esc_html($consent_storage_rate); ?>%</div>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col"><?php echo esc_html__('Nom', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Email', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Consentement Traitement', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Consentement Stockage', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Date de consentement', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Visites', 'le-margo'); ?></th>
                <th scope="col"><?php echo esc_html__('Actions', 'le-margo'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $customers = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY consent_date DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ));
            
            if ($customers) {
                foreach ($customers as $customer) {
                    ?>
                    <tr>
                        <td><?php echo esc_html($customer->name); ?></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td>
                            <span class="consent-status <?php echo $customer->consent_data_processing ? 'consent-yes' : 'consent-no'; ?>">
                                <?php echo $customer->consent_data_processing ? esc_html__('Oui', 'le-margo') : esc_html__('Non', 'le-margo'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="consent-status <?php echo $customer->consent_data_storage ? 'consent-yes' : 'consent-no'; ?>">
                                <?php echo $customer->consent_data_storage ? esc_html__('Oui', 'le-margo') : esc_html__('Non', 'le-margo'); ?>
                            </span>
                        </td>
                        <td><?php echo $customer->consent_date ? esc_html(date_i18n(get_option('date_format'), strtotime($customer->consent_date))) : '-'; ?></td>
                        <td><?php echo esc_html($customer->visits); ?></td>
                        <td>
                            <?php if ($customer->consent_data_processing || $customer->consent_data_storage) : ?>
                                <?php 
                                $revoke_url = wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'revoke_consent',
                                            'customer_id' => $customer->id,
                                            'tab' => 'rgpd',
                                        )
                                    ),
                                    'revoke_consent_' . $customer->id
                                );
                                ?>
                                <a href="<?php echo esc_url($revoke_url); ?>" class="button button-small" onclick="return confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir révoquer les consentements RGPD de ce client?', 'le-margo')); ?>');">
                                    <?php echo esc_html__('Révoquer consentements', 'le-margo'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="7"><?php echo esc_html__('Aucun client trouvé.', 'le-margo'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
} 

/**
 * Afficher le détail d'un client
 */
function le_margo_display_customer_detail($customer_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    $customer = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $customer_id
    ));
    
    if (!$customer) {
        echo '<div class="notice notice-error"><p>' . esc_html__('Client introuvable.', 'le-margo') . '</p></div>';
        return;
    }
    
    // Récupérer l'historique des réservations
    $reservations_table = $wpdb->prefix . 'reservations';
    $reservations = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $reservations_table WHERE email = %s ORDER BY reservation_date DESC, reservation_time DESC LIMIT 10",
        $customer->email
    ));
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Détail du Client', 'le-margo'); ?></h1>
        
        <a href="?page=le-margo-customers&tab=tous" class="button">&larr; <?php echo esc_html__('Retour à la liste', 'le-margo'); ?></a>
        
        <div class="customer-detail-grid">
            <!-- Informations générales -->
            <div class="customer-detail-section">
                <h3><?php echo esc_html__('Informations Générales', 'le-margo'); ?></h3>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Nom :', 'le-margo'); ?></span>
                    <span class="customer-detail-value"><?php echo esc_html($customer->name); ?></span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Email :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <a href="mailto:<?php echo esc_attr($customer->email); ?>"><?php echo esc_html($customer->email); ?></a>
                    </span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Statut :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <?php if ($customer->is_vip) : ?>
                            <span class="vip-status"><?php echo esc_html__('VIP', 'le-margo'); ?></span>
                        <?php else : ?>
                            <span class="regular-status"><?php echo esc_html__('Régulier', 'le-margo'); ?></span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Nombre de visites :', 'le-margo'); ?></span>
                    <span class="customer-detail-value"><?php echo esc_html($customer->visits); ?></span>
                </div>
            </div>
            
            <!-- Dates importantes -->
            <div class="customer-detail-section">
                <h3><?php echo esc_html__('Dates Importantes', 'le-margo'); ?></h3>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Première visite :', 'le-margo'); ?></span>
                    <span class="customer-detail-value"><?php echo date_i18n(get_option('date_format'), strtotime($customer->first_visit)); ?></span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Dernière visite :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <?php echo date_i18n(get_option('date_format'), strtotime($customer->last_visit)); ?>
                        <?php 
                        $days_since_last_visit = floor((time() - strtotime($customer->last_visit)) / (60 * 60 * 24));
                        if ($days_since_last_visit > 0) : ?>
                            <br><small>(<?php echo sprintf(esc_html__('Il y a %d jours', 'le-margo'), $days_since_last_visit); ?>)</small>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Client depuis :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <?php 
                        $days_as_customer = floor((time() - strtotime($customer->first_visit)) / (60 * 60 * 24));
                        echo sprintf(esc_html__('%d jours', 'le-margo'), $days_as_customer);
                        ?>
                    </span>
                </div>
            </div>
            
            <!-- Consentements GDPR -->
            <div class="customer-detail-section">
                <h3><?php echo esc_html__('Consentements GDPR', 'le-margo'); ?></h3>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Traitement des données :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <?php if ($customer->consent_data_processing) : ?>
                            <span class="dashicons dashicons-yes" style="color: #27ae60;"></span> <?php echo esc_html__('Accepté', 'le-margo'); ?>
                        <?php else : ?>
                            <span class="dashicons dashicons-no" style="color: #dc3232;"></span> <?php echo esc_html__('Refusé', 'le-margo'); ?>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Newsletter :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <?php if ($customer->newsletter) : ?>
                            <span class="dashicons dashicons-yes" style="color: #27ae60;"></span> <?php echo esc_html__('Acceptée', 'le-margo'); ?>
                        <?php else : ?>
                            <span class="dashicons dashicons-no" style="color: #dc3232;"></span> <?php echo esc_html__('Refusée', 'le-margo'); ?>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="customer-detail-row">
                    <span class="customer-detail-label"><?php echo esc_html__('Rappels :', 'le-margo'); ?></span>
                    <span class="customer-detail-value">
                        <?php if ($customer->accept_reminder) : ?>
                            <span class="dashicons dashicons-yes" style="color: #27ae60;"></span> <?php echo esc_html__('Acceptés', 'le-margo'); ?>
                        <?php else : ?>
                            <span class="dashicons dashicons-no" style="color: #dc3232;"></span> <?php echo esc_html__('Refusés', 'le-margo'); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <!-- Actions rapides -->
            <div class="customer-detail-section">
                <h3><?php echo esc_html__('Actions Rapides', 'le-margo'); ?></h3>
                
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php 
                    $edit_url = add_query_arg(
                        array(
                            'action' => 'edit_customer',
                            'customer_id' => $customer->id,
                            'tab' => 'tous',
                        )
                    );
                    
                    $toggle_vip_url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'toggle_vip',
                                'customer_id' => $customer->id,
                                'tab' => 'tous',
                            )
                        ),
                        'toggle_vip_' . $customer->id
                    );
                    
                    $add_visit_url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'add_visit',
                                'customer_id' => $customer->id,
                                'tab' => 'tous',
                            )
                        ),
                        'add_visit_' . $customer->id
                    );
                    ?>
                    
                    <a href="<?php echo esc_url($edit_url); ?>" class="button">
                        <span class="dashicons dashicons-edit"></span> <?php echo esc_html__('Modifier', 'le-margo'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url($toggle_vip_url); ?>" class="button">
                        <?php echo $customer->is_vip ? esc_html__('Retirer VIP', 'le-margo') : esc_html__('Marquer VIP', 'le-margo'); ?>
                    </a>
                    
                    <a href="<?php echo esc_url($add_visit_url); ?>" class="button">
                        <span class="dashicons dashicons-plus"></span> <?php echo esc_html__('Ajouter visite', 'le-margo'); ?>
                    </a>
                    
                    <a href="mailto:<?php echo esc_attr($customer->email); ?>" class="button">
                        <span class="dashicons dashicons-email-alt"></span> <?php echo esc_html__('Envoyer email', 'le-margo'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Historique des réservations -->
        <?php if (!empty($reservations)) : ?>
        <div class="customer-detail-section" style="grid-column: 1 / -1; margin-top: 20px;">
            <h3><?php echo esc_html__('Dernières Réservations', 'le-margo'); ?></h3>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Date', 'le-margo'); ?></th>
                        <th><?php echo esc_html__('Heure', 'le-margo'); ?></th>
                        <th><?php echo esc_html__('Personnes', 'le-margo'); ?></th>
                        <th><?php echo esc_html__('Statut', 'le-margo'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation) : ?>
                        <tr>
                            <td><?php echo date_i18n(get_option('date_format'), strtotime($reservation->reservation_date)); ?></td>
                            <td><?php echo date_i18n('H:i', strtotime($reservation->reservation_time)); ?></td>
                            <td><?php echo esc_html($reservation->people); ?></td>
                            <td>
                                <?php if ($reservation->status === 'confirmed') : ?>
                                    <span style="color: #27ae60;"><?php echo esc_html__('Confirmée', 'le-margo'); ?></span>
                                <?php elseif ($reservation->status === 'pending') : ?>
                                    <span style="color: #f39c12;"><?php echo esc_html__('En attente', 'le-margo'); ?></span>
                                <?php else : ?>
                                    <span style="color: #dc3232;"><?php echo esc_html__('Annulée', 'le-margo'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Afficher le formulaire d'édition d'un client
 */
function le_margo_display_customer_edit_form($customer_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    $customer = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $customer_id
    ));
    
    if (!$customer) {
        echo '<div class="notice notice-error"><p>' . esc_html__('Client introuvable.', 'le-margo') . '</p></div>';
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Modifier le Client', 'le-margo'); ?></h1>
        
        <a href="?page=le-margo-customers&tab=tous" class="button">&larr; <?php echo esc_html__('Retour à la liste', 'le-margo'); ?></a>
        
        <div class="customer-edit-form">
            <form method="POST" action="<?php echo esc_url(add_query_arg(array('action' => 'save_customer', 'customer_id' => $customer_id))); ?>">
                <?php wp_nonce_field('save_customer_' . $customer_id); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="customer_name"><?php echo esc_html__('Nom', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="customer_name" name="customer_data[name]" value="<?php echo esc_attr($customer->name); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_email"><?php echo esc_html__('Email', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="customer_email" name="customer_data[email]" value="<?php echo esc_attr($customer->email); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_visits"><?php echo esc_html__('Nombre de visites', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="customer_visits" name="customer_data[visits]" value="<?php echo esc_attr($customer->visits); ?>" min="0" class="small-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_is_vip"><?php echo esc_html__('Statut VIP', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="customer_is_vip" name="customer_data[is_vip]" value="1" <?php checked($customer->is_vip, 1); ?>>
                                <?php echo esc_html__('Marquer comme VIP', 'le-margo'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_consent_data_processing"><?php echo esc_html__('Consentement traitement des données', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="customer_consent_data_processing" name="customer_data[consent_data_processing]" value="1" <?php checked($customer->consent_data_processing, 1); ?>>
                                <?php echo esc_html__('Accepté', 'le-margo'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_newsletter"><?php echo esc_html__('Newsletter', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="customer_newsletter" name="customer_data[newsletter]" value="1" <?php checked($customer->newsletter, 1); ?>>
                                <?php echo esc_html__('Acceptée', 'le-margo'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_accept_reminder"><?php echo esc_html__('Rappels', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="customer_accept_reminder" name="customer_data[accept_reminder]" value="1" <?php checked($customer->accept_reminder, 1); ?>>
                                <?php echo esc_html__('Acceptés', 'le-margo'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="customer_notes"><?php echo esc_html__('Notes', 'le-margo'); ?></label>
                        </th>
                        <td>
                            <textarea id="customer_notes" name="customer_data[notes]" rows="4" cols="50"><?php echo esc_textarea($customer->notes); ?></textarea>
                            <p class="description"><?php echo esc_html__('Notes privées sur ce client (optionnel)', 'le-margo'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Enregistrer les modifications', 'le-margo'); ?>">
                    <a href="?page=le-margo-customers&tab=tous" class="button"><?php echo esc_html__('Annuler', 'le-margo'); ?></a>
                </p>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Sauvegarder les données d'un client
 */
function le_margo_save_customer_data($customer_id) {
    if (!isset($_POST['customer_data']) || !wp_verify_nonce($_POST['_wpnonce'], 'save_customer_' . $customer_id)) {
        wp_die(__('Erreur de sécurité.', 'le-margo'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'customer_stats';
    
    $customer_data = $_POST['customer_data'];
    
    // Nettoyer et valider les données
    $data = array(
        'name' => sanitize_text_field($customer_data['name']),
        'email' => sanitize_email($customer_data['email']),
        'visits' => intval($customer_data['visits']),
        'is_vip' => isset($customer_data['is_vip']) ? 1 : 0,
        'consent_data_processing' => isset($customer_data['consent_data_processing']) ? 1 : 0,
        'newsletter' => isset($customer_data['newsletter']) ? 1 : 0,
        'accept_reminder' => isset($customer_data['accept_reminder']) ? 1 : 0,
        'notes' => sanitize_textarea_field($customer_data['notes'] ?? ''),
    );
    
    // Mettre à jour la base de données
    $result = $wpdb->update(
        $table_name,
        $data,
        array('id' => $customer_id)
    );
    
    if ($result !== false) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Client modifié avec succès.', 'le-margo') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Erreur lors de la modification du client.', 'le-margo') . '</p></div>';
    }
    
    // Rediriger vers la liste
    echo '<script>setTimeout(function() { window.location.href = "?page=le-margo-customers&tab=tous"; }, 2000);</script>';
} 