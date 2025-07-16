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
    // Traitement des actions
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
    }
    
    // Définir l'onglet actif
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'tous';
    ?>
    
    <div class="wrap">
        <h1><?php echo esc_html__('Clients du Restaurant Le Margo', 'le-margo'); ?></h1>
        
        <!-- Statistiques en haut de page -->
        <?php le_margo_display_customer_stats_header(); ?>
        
        <!-- Navigation par onglets -->
        <nav class="nav-tab-wrapper wp-clearfix">
            <a href="?page=le-margo-customers&tab=tous" class="nav-tab <?php echo $current_tab === 'tous' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Tous les clients', 'le-margo'); ?>
            </a>
            <a href="?page=le-margo-customers&tab=vip" class="nav-tab <?php echo $current_tab === 'vip' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Clients VIP', 'le-margo'); ?>
            </a>
            <a href="?page=le-margo-customers&tab=recents" class="nav-tab <?php echo $current_tab === 'recents' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('Visites récentes', 'le-margo'); ?>
            </a>
            <a href="?page=le-margo-customers&tab=rgpd" class="nav-tab <?php echo $current_tab === 'rgpd' ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html__('RGPD', 'le-margo'); ?>
            </a>
        </nav>
        
        <div class="tab-content">
            <?php
            switch ($current_tab) {
                case 'vip':
                    le_margo_display_vip_customers_table();
                    break;
                case 'recents':
                    le_margo_display_recent_customers_table();
                    break;
                case 'rgpd':
                    le_margo_display_gdpr_customers_table();
                    break;
                default:
                    le_margo_display_all_customers_table();
                    break;
            }
            ?>
        </div>
    </div>
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
    
    // Pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Récupérer le nombre total de clients
    $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    
    // Récupérer les clients pour cette page
    $customers = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY last_visit DESC LIMIT %d OFFSET %d",
        $per_page, $offset
    ));
    
    // Calculer le nombre total de pages
    $total_pages = ceil($total_customers / $per_page);
    
    ?>
    <div class="tablenav top">
        <div class="tablenav-pages">
            <?php if ($total_pages > 1) : ?>
                <span class="displaying-num">
                    <?php printf(_n('%s client', '%s clients', $total_customers, 'le-margo'), number_format_i18n($total_customers)); ?>
                </span>
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
                        <td><?php echo esc_html($customer->name); ?></td>
                        <td><?php echo esc_html($customer->email); ?></td>
                        <td><?php echo esc_html($customer->visits); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($customer->first_visit)); ?></td>
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