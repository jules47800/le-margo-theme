<?php
/**
 * Page de statistiques avancées pour Le Margo
 */

/**
 * Ajouter le menu de statistiques avancées
 */
function le_margo_add_advanced_stats_menu() {
    add_submenu_page(
        'le-margo-customers',
        __('Statistiques Avancées', 'le-margo'),
        __('Statistiques Avancées', 'le-margo'),
        'manage_options',
        'le-margo-advanced-stats',
        'le_margo_advanced_stats_page'
    );
}
add_action('admin_menu', 'le_margo_add_advanced_stats_menu');

/**
 * Page de statistiques avancées
 */
function le_margo_advanced_stats_page() {
    // Déterminer la période d'analyse
    $default_period = 'last_30_days';
    $selected_period = isset($_GET['period']) ? sanitize_key($_GET['period']) : $default_period;
    
    // Dates personnalisées
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    // Récupérer les statistiques avancées en fonction de la période
    $advanced_stats = le_margo_get_advanced_restaurant_stats($selected_period, $start_date, $end_date);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Statistiques Avancées - Le Margo', 'le-margo'); ?></h1>
        
        <!-- Formulaire de sélection de période -->
        <form method="GET" action="">
            <input type="hidden" name="page" value="le-margo-advanced-stats">
            <select name="period" onchange="this.form.submit()">
                <option value="last_7_days" <?php selected($selected_period, 'last_7_days'); ?>><?php _e('7 derniers jours', 'le-margo'); ?></option>
                <option value="last_30_days" <?php selected($selected_period, 'last_30_days'); ?>><?php _e('30 derniers jours', 'le-margo'); ?></option>
                <option value="last_90_days" <?php selected($selected_period, 'last_90_days'); ?>><?php _e('90 derniers jours', 'le-margo'); ?></option>
                <option value="this_year" <?php selected($selected_period, 'this_year'); ?>><?php _e('Cette année', 'le-margo'); ?></option>
                <option value="custom" <?php selected($selected_period, 'custom'); ?>><?php _e('Période personnalisée', 'le-margo'); ?></option>
            </select>
            <span id="custom-date-range" style="<?php echo $selected_period === 'custom' ? '' : 'display:none;'; ?>">
                <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
            </span>
            <button type="submit" class="button"><?php _e('Filtrer', 'le-margo'); ?></button>
        </form>
        <script>
            document.querySelector('select[name="period"]').addEventListener('change', function() {
                const customRange = document.getElementById('custom-date-range');
                if (this.value === 'custom') {
                    customRange.style.display = 'inline-block';
                } else {
                    customRange.style.display = 'none';
                }
            });
        </script>

        <div class="advanced-stats-container">
            <style>
                .advanced-stats-container {
                    margin-top: 20px;
                }
                .stats-card {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
                    margin-bottom: 20px;
                    padding: 20px;
                }
                .stats-card h2 {
                    margin-top: 0;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #eee;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                }
                .stat-value {
                    font-size: 24px;
                    font-weight: bold;
                    margin: 10px 0;
                }
                .stat-label {
                    color: #777;
                    font-size: 14px;
                }
                .bar-chart-container, .line-chart-container {
                    height: 300px;
                    margin: 15px 0;
                }
                .bar-chart-container canvas, .line-chart-container canvas {
                    max-width: 100%;
                }
                .stats-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }
                .stats-table th, .stats-table td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #eee;
                }
                .stats-table th {
                    font-weight: 600;
                    color: #3c434a;
                }
                .small-chart {
                    height: 200px;
                }
                .highlight-value {
                    color: #e0a872;
                }
                .stats-summary {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }
                .summary-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                .summary-item:last-child {
                    border-bottom: none;
                }
                .summary-label {
                    font-weight: 500;
                    color: #666;
                }
                .summary-value {
                    font-weight: bold;
                    color: #e0a872;
                    font-size: 18px;
                }
            </style>
            
            <!-- 1. Résumé et KPI -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Indicateurs Clés de Performance', 'le-margo'); ?></h2>
                <div class="stats-grid">
                    <!-- Taux de rétention -->
                    <div>
                        <div class="stat-label"><?php echo esc_html__('Taux de rétention (30 jours)', 'le-margo'); ?></div>
                        <div class="stat-value highlight-value"><?php echo esc_html($advanced_stats['retention']['30_days']); ?>%</div>
                    </div>
                    
                    <!-- Taux de rétention à 90 jours -->
                    <div>
                        <div class="stat-label"><?php echo esc_html__('Taux de rétention (90 jours)', 'le-margo'); ?></div>
                        <div class="stat-value highlight-value"><?php echo esc_html($advanced_stats['retention']['90_days']); ?>%</div>
                    </div>
                    
                    <!-- Temps moyen entre les visites -->
                    <div>
                        <div class="stat-label"><?php echo esc_html__('Jours moyens entre visites', 'le-margo'); ?></div>
                        <div class="stat-value"><?php echo esc_html($advanced_stats['avg_days_between_visits']); ?></div>
                    </div>
                    
                    <!-- Taux d'occupation moyen -->
                    <?php
                    $avg_occupancy = 0;
                    $count = 0;
                    if (!empty($advanced_stats['occupancy_data'])) {
                        foreach ($advanced_stats['occupancy_data'] as $date => $data) {
                            $avg_occupancy += $data['overall'];
                            $count++;
                        }
                        $avg_occupancy = $count > 0 ? round($avg_occupancy / $count, 1) : 0;
                    }
                    ?>
                    <div>
                        <div class="stat-label"><?php echo esc_html__('Taux d\'occupation moyen', 'le-margo'); ?></div>
                        <div class="stat-value"><?php echo esc_html($avg_occupancy); ?>%</div>
                    </div>

                    <!-- Taux de No-Show -->
                    <div>
                        <div class="stat-label"><?php echo esc_html__('Taux de No-Show', 'le-margo'); ?></div>
                        <div class="stat-value" style="color: #dc3232;"><?php echo esc_html($advanced_stats['no_show_rate']); ?>%</div>
                        <small><?php echo sprintf(esc_html__('%d réservations non honorées', 'le-margo'), esc_html($advanced_stats['no_show_count'])); ?></small>
                    </div>
                </div>
            </div>
            
            <!-- 2. Analyse d'occupation -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Taux d\'Occupation (30 derniers jours)', 'le-margo'); ?></h2>
                
                <!-- Graphique d'occupation -->
                <div class="line-chart-container">
                    <canvas id="occupancy-chart"></canvas>
                </div>
                
                <!-- Tableau d'occupation -->
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Date', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Taux d\'occupation', 'le-margo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                                 if (!empty($advanced_stats['occupancy_data'])) {
                             $displayed = 0;
                             $total_occupancy = 0;
                             $days_with_data = 0;
                             
                             foreach ($advanced_stats['occupancy_data'] as $date => $data) {
                                 if ($displayed >= 7) break; // Limiter à 7 jours pour le tableau
                                 
                                 $formatted_date = date_i18n(get_option('date_format'), strtotime($date));
                                 $occupancy_rate = $data['overall'];
                                 
                                 // Calculer la moyenne pour les jours avec des données
                                 if ($occupancy_rate > 0) {
                                     $total_occupancy += $occupancy_rate;
                                     $days_with_data++;
                                 }
                                 
                                 // Définir la couleur selon le taux d'occupation
                                 $color_class = '';
                                 if ($occupancy_rate >= 80) {
                                     $color_class = 'style="color: #27ae60; font-weight: bold;"'; // Vert pour forte occupation
                                 } elseif ($occupancy_rate >= 50) {
                                     $color_class = 'style="color: #f39c12; font-weight: bold;"'; // Orange pour occupation moyenne
                                 } elseif ($occupancy_rate > 0) {
                                     $color_class = 'style="color: #e74c3c; font-weight: bold;"'; // Rouge pour faible occupation
                                 }
                                 
                                 echo '<tr>';
                                 echo '<td>' . esc_html($formatted_date) . '</td>';
                                 echo '<td ' . $color_class . '>' . esc_html($occupancy_rate) . '%</td>';
                                 echo '</tr>';
                                 
                                 $displayed++;
                             }
                             
                             // Afficher la moyenne des jours avec des données
                             if ($days_with_data > 0) {
                                 $avg_occupancy = round($total_occupancy / $days_with_data, 1);
                                 echo '<tr style="background-color: #f8f9fa; font-weight: bold;">';
                                 echo '<td>' . esc_html__('Moyenne (jours avec réservations)', 'le-margo') . '</td>';
                                 echo '<td style="color: #e0a872;">' . esc_html($avg_occupancy) . '%</td>';
                                 echo '</tr>';
                             }
                         } else {
                             echo '<tr><td colspan="2">' . esc_html__('Aucune donnée disponible', 'le-margo') . '</td></tr>';
                         }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 3. Répartition par jour de la semaine -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Répartition par Jour de la Semaine', 'le-margo'); ?></h2>
                
                <div class="bar-chart-container">
                    <canvas id="weekday-chart"></canvas>
                </div>
                
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Jour', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Réservations', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Taille moyenne', 'le-margo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $days_of_week = array(
                            __('Lundi', 'le-margo'),
                            __('Mardi', 'le-margo'),
                            __('Mercredi', 'le-margo'),
                            __('Jeudi', 'le-margo'),
                            __('Vendredi', 'le-margo'),
                            __('Samedi', 'le-margo'),
                            __('Dimanche', 'le-margo')
                        );
                        
                        if (!empty($advanced_stats['weekday_stats'])) {
                            foreach ($advanced_stats['weekday_stats'] as $day) {
                                $day_name = $days_of_week[$day->weekday];
                                $avg_size = round($day->avg_party_size, 1);
                                
                                echo '<tr>';
                                echo '<td>' . esc_html($day_name) . '</td>';
                                echo '<td>' . esc_html($day->reservation_count) . '</td>';
                                echo '<td>' . esc_html($avg_size) . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3">' . esc_html__('Aucune donnée disponible', 'le-margo') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 4. Source des Réservations -->
            <div class="stats-grid">
                <div class="stats-card">
                    <h2><?php echo esc_html__('Source des Réservations', 'le-margo'); ?></h2>
                    <div class="small-chart">
                        <canvas id="source-chart"></canvas>
                    </div>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Source', 'le-margo'); ?></th>
                                <th><?php echo esc_html__('Réservations', 'le-margo'); ?></th>
                                <th><?php echo esc_html__('Pourcentage', 'le-margo'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $source_names = [
                                'public' => __('Site Web', 'le-margo'),
                                'admin' => __('Manuelle (Admin)', 'le-margo')
                            ];
                            
                            $total_reservations = array_sum($advanced_stats['source_stats']);
                            
                            foreach ($advanced_stats['source_stats'] as $source => $count) {
                                $percentage = $total_reservations > 0 ? round(($count / $total_reservations) * 100, 1) : 0;
                                echo '<tr>';
                                echo '<td>' . esc_html($source_names[$source] ?? ucfirst($source)) . '</td>';
                                echo '<td>' . esc_html($count) . '</td>';
                                echo '<td>' . esc_html($percentage) . '%</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="stats-card">
                    <h2><?php echo esc_html__('Résumé des Réservations', 'le-margo'); ?></h2>
                    <div class="stats-summary">
                        <?php
                        $total_reservations = array_sum($advanced_stats['source_stats']);
                        $avg_occupancy = 0;
                        $count = 0;
                        if (!empty($advanced_stats['occupancy_data'])) {
                            foreach ($advanced_stats['occupancy_data'] as $date => $data) {
                                $avg_occupancy += $data['overall'];
                                $count++;
                            }
                            $avg_occupancy = $count > 0 ? round($avg_occupancy / $count, 1) : 0;
                        }
                        ?>
                        <div class="summary-item">
                            <div class="summary-label"><?php echo esc_html__('Total réservations', 'le-margo'); ?></div>
                            <div class="summary-value"><?php echo esc_html($total_reservations); ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label"><?php echo esc_html__('Taux d\'occupation moyen', 'le-margo'); ?></div>
                            <div class="summary-value"><?php echo esc_html($avg_occupancy); ?>%</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label"><?php echo esc_html__('Période analysée', 'le-margo'); ?></div>
                            <div class="summary-value"><?php echo esc_html($selected_period === 'custom' ? $start_date . ' - ' . $end_date : ucfirst(str_replace('_', ' ', $selected_period))); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 5. Taille des groupes -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Répartition par Taille de Groupe', 'le-margo'); ?></h2>
                
                <div class="small-chart">
                    <canvas id="group-size-chart"></canvas>
                </div>
                
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Nombre de personnes', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Réservations', 'le-margo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($advanced_stats['group_size_stats'])) {
                            foreach ($advanced_stats['group_size_stats'] as $group_size) {
                                echo '<tr>';
                                echo '<td>' . esc_html($group_size->group_size) . '</td>';
                                echo '<td>' . esc_html($group_size->count) . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="2">' . esc_html__('Aucune donnée disponible', 'le-margo') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 6. Évolution mensuelle -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Évolution Mensuelle des Réservations', 'le-margo'); ?></h2>
                
                <div class="line-chart-container">
                    <canvas id="monthly-chart"></canvas>
                </div>
                
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Mois', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Réservations', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Total convives', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Taille moyenne', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Annulations', 'le-margo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($advanced_stats['monthly_stats'])) {
                            $displayed = 0;
                            foreach ($advanced_stats['monthly_stats'] as $month_data) {
                                if ($displayed >= 6) break; // Limiter à 6 mois
                                
                                $month = $month_data->month;
                                $formatted_month = date_i18n('F Y', strtotime($month . '-01'));
                                $avg_size = round($month_data->avg_party_size, 1);
                                
                                $cancellation_data = isset($advanced_stats['cancellation_rates'][$month]) ? $advanced_stats['cancellation_rates'][$month] : null;
                                $cancellation_rate = $cancellation_data ? $cancellation_data['rate'] . '%' : '-';
                                
                                echo '<tr>';
                                echo '<td>' . esc_html($formatted_month) . '</td>';
                                echo '<td>' . esc_html($month_data->reservation_count) . '</td>';
                                echo '<td>' . esc_html($month_data->total_people) . '</td>';
                                echo '<td>' . esc_html($avg_size) . '</td>';
                                echo '<td>' . esc_html($cancellation_rate) . '</td>';
                                echo '</tr>';
                                
                                $displayed++;
                            }
                        } else {
                            echo '<tr><td colspan="5">' . esc_html__('Aucune donnée disponible', 'le-margo') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 7. Nouveaux vs Fidèles -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Nouveaux Clients vs Clients Fidèles', 'le-margo'); ?></h2>
                
                <div class="line-chart-container">
                    <canvas id="new-returning-chart"></canvas>
                </div>
                
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Date', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Nouveaux clients', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Clients fidèles', 'le-margo'); ?></th>
                            <th><?php echo esc_html__('Ratio fidélité', 'le-margo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($advanced_stats['new_vs_returning'])) {
                            $displayed = 0;
                            foreach ($advanced_stats['new_vs_returning'] as $day_data) {
                                if ($displayed >= 7) break; // Limiter à 7 jours
                                
                                $formatted_date = date_i18n(get_option('date_format'), strtotime($day_data->reservation_date));
                                $new = intval($day_data->new_customers);
                                $returning = intval($day_data->returning_customers);
                                $total = $new + $returning;
                                $ratio = $total > 0 ? round(($returning / $total) * 100, 1) . '%' : '-';
                                
                                echo '<tr>';
                                echo '<td>' . esc_html($formatted_date) . '</td>';
                                echo '<td>' . esc_html($new) . '</td>';
                                echo '<td>' . esc_html($returning) . '</td>';
                                echo '<td>' . esc_html($ratio) . '</td>';
                                echo '</tr>';
                                
                                $displayed++;
                            }
                        } else {
                            echo '<tr><td colspan="4">' . esc_html__('Aucune donnée disponible', 'le-margo') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 8. Distribution des visites clients -->
            <div class="stats-card">
                <h2><?php echo esc_html__('Distribution des Clients par Nombre de Visites', 'le-margo'); ?></h2>
                
                <div class="bar-chart-container">
                    <canvas id="visits-distribution-chart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Intégration de Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration des couleurs
            const primaryColor = '#e0a872';
            const secondaryColor = '#9e8e7e';
            const tertiaryColor = '#3a3c36';
            const lightColor = '#f5f0e9';
            
            // Charger les données depuis PHP
            const statsData = <?php echo json_encode($advanced_stats); ?>;
            
            // 1. Graphique d'occupation
            if (document.getElementById('occupancy-chart')) {
                const occupancyData = statsData.occupancy_data;
                const dates = [];
                const overallData = [];
                
                for (const [date, data] of Object.entries(occupancyData)) {
                    const formattedDate = new Date(date).toLocaleDateString();
                    dates.unshift(formattedDate);
                    overallData.unshift(data.overall);
                }
                
                                 new Chart(document.getElementById('occupancy-chart'), {
                     type: 'line',
                     data: {
                         labels: dates,
                         datasets: [
                             {
                                 label: '<?php echo esc_js(__('Taux d\'occupation', 'le-margo')); ?>',
                                 data: overallData,
                                 borderColor: primaryColor,
                                 backgroundColor: 'rgba(224, 168, 114, 0.2)',
                                 tension: 0.1,
                                 fill: true,
                                 pointBackgroundColor: function(context) {
                                     const value = context.parsed.y;
                                     if (value >= 80) return '#27ae60'; // Vert pour forte occupation
                                     if (value >= 50) return '#f39c12'; // Orange pour occupation moyenne
                                     if (value > 0) return '#e74c3c'; // Rouge pour faible occupation
                                     return '#95a5a6'; // Gris pour 0%
                                 },
                                 pointBorderColor: '#fff',
                                 pointBorderWidth: 2,
                                 pointRadius: 6
                             }
                         ]
                     },
                     options: {
                         responsive: true,
                         plugins: {
                             tooltip: {
                                 callbacks: {
                                     label: function(context) {
                                         const value = context.parsed.y;
                                         let status = '';
                                         if (value >= 80) status = ' (Forte occupation)';
                                         else if (value >= 50) status = ' (Occupation moyenne)';
                                         else if (value > 0) status = ' (Faible occupation)';
                                         else status = ' (Aucune réservation)';
                                         return context.dataset.label + ': ' + value + '%' + status;
                                     }
                                 }
                             }
                         },
                         scales: {
                             y: {
                                 min: 0,
                                 max: 100,
                                 ticks: {
                                     callback: function(value) {
                                         return value + '%';
                                     }
                                 },
                                 grid: {
                                     color: 'rgba(0,0,0,0.1)'
                                 }
                             },
                             x: {
                                 grid: {
                                     color: 'rgba(0,0,0,0.1)'
                                 }
                             }
                         }
                     }
                 });
            }
            
            // 2. Graphique par jour de la semaine
            if (document.getElementById('weekday-chart')) {
                const weekdayStats = statsData.weekday_stats;
                const days = [
                    '<?php echo esc_js(__('Lundi', 'le-margo')); ?>',
                    '<?php echo esc_js(__('Mardi', 'le-margo')); ?>',
                    '<?php echo esc_js(__('Mercredi', 'le-margo')); ?>',
                    '<?php echo esc_js(__('Jeudi', 'le-margo')); ?>',
                    '<?php echo esc_js(__('Vendredi', 'le-margo')); ?>',
                    '<?php echo esc_js(__('Samedi', 'le-margo')); ?>',
                    '<?php echo esc_js(__('Dimanche', 'le-margo')); ?>'
                ];
                const counts = Array(7).fill(0);
                
                if (weekdayStats) {
                    weekdayStats.forEach(day => {
                        counts[day.weekday] = day.reservation_count;
                    });
                }
                
                new Chart(document.getElementById('weekday-chart'), {
                    type: 'bar',
                    data: {
                        labels: days,
                        datasets: [{
                            label: '<?php echo esc_js(__('Nombre de réservations', 'le-margo')); ?>',
                            data: counts,
                            backgroundColor: primaryColor
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
            
            // 3. Graphique taille des groupes
            if (document.getElementById('group-size-chart')) {
                const groupSizeStats = statsData.group_size_stats;
                const sizes = [];
                const sizeData = [];
                
                if (groupSizeStats) {
                    groupSizeStats.forEach(size => {
                        sizes.push(size.group_size + ' <?php echo esc_js(__('pers.', 'le-margo')); ?>');
                        sizeData.push(size.count);
                    });
                }
                
                new Chart(document.getElementById('group-size-chart'), {
                    type: 'bar',
                    data: {
                        labels: sizes,
                        datasets: [{
                            label: '<?php echo esc_js(__('Nombre de réservations', 'le-margo')); ?>',
                            data: sizeData,
                            backgroundColor: primaryColor
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
            
            // 4. Graphique évolution mensuelle
            if (document.getElementById('monthly-chart')) {
                const monthlyStats = statsData.monthly_stats;
                const months = [];
                const reservationCounts = [];
                const peopleCounts = [];
                
                if (monthlyStats) {
                    // Inverser l'ordre pour avoir les mois chronologiquement
                    monthlyStats.slice().reverse().forEach(month => {
                        const date = new Date(month.month + '-01');
                        const formattedMonth = date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                        months.push(formattedMonth);
                        reservationCounts.push(month.reservation_count);
                        peopleCounts.push(month.total_people);
                    });
                }
                
                new Chart(document.getElementById('monthly-chart'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: '<?php echo esc_js(__('Réservations', 'le-margo')); ?>',
                                data: reservationCounts,
                                borderColor: primaryColor,
                                backgroundColor: 'rgba(224, 168, 114, 0.2)',
                                tension: 0.1,
                                yAxisID: 'y'
                            },
                            {
                                label: '<?php echo esc_js(__('Convives', 'le-margo')); ?>',
                                data: peopleCounts,
                                borderColor: secondaryColor,
                                backgroundColor: 'rgba(158, 142, 126, 0.2)',
                                tension: 0.1,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                type: 'linear',
                                position: 'left',
                                title: {
                                    display: true,
                                    text: '<?php echo esc_js(__('Réservations', 'le-margo')); ?>'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                type: 'linear',
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: '<?php echo esc_js(__('Convives', 'le-margo')); ?>'
                                }
                            }
                        }
                    }
                });
            }
            
            // 5. Graphique nouveaux vs fidèles
            if (document.getElementById('new-returning-chart')) {
                const newVsReturning = statsData.new_vs_returning;
                const dates = [];
                const newCustomers = [];
                const returningCustomers = [];
                
                if (newVsReturning) {
                    // Inverser l'ordre pour avoir les dates chronologiquement
                    newVsReturning.slice().reverse().forEach(day => {
                        const date = new Date(day.reservation_date);
                        const formattedDate = date.toLocaleDateString();
                        dates.push(formattedDate);
                        newCustomers.push(parseInt(day.new_customers));
                        returningCustomers.push(parseInt(day.returning_customers));
                    });
                }
                
                new Chart(document.getElementById('new-returning-chart'), {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: '<?php echo esc_js(__('Nouveaux clients', 'le-margo')); ?>',
                                data: newCustomers,
                                backgroundColor: secondaryColor
                            },
                            {
                                label: '<?php echo esc_js(__('Clients fidèles', 'le-margo')); ?>',
                                data: returningCustomers,
                                backgroundColor: primaryColor
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
            
            // 6. Distribution des visites
            if (document.getElementById('visits-distribution-chart')) {
                const visitDistribution = statsData.visit_distribution;
                const visits = [];
                const customerCounts = [];
                
                if (visitDistribution) {
                    visitDistribution.forEach(item => {
                        if (item.visits <= 10) { // Limiter l'affichage aux 10 premières visites pour la lisibilité
                            visits.push(item.visits + ' <?php echo esc_js(__('visite(s)', 'le-margo')); ?>');
                            customerCounts.push(item.customer_count);
                        }
                    });
                }
                
                new Chart(document.getElementById('visits-distribution-chart'), {
                    type: 'bar',
                    data: {
                        labels: visits,
                        datasets: [{
                            label: '<?php echo esc_js(__('Nombre de clients', 'le-margo')); ?>',
                            data: customerCounts,
                            backgroundColor: primaryColor
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Graphique Sources (Pie)
            var sourceCtx = document.getElementById('source-chart').getContext('2d');
            if (window.sourceChart instanceof Chart) {
                window.sourceChart.destroy();
            }
            window.sourceChart = new Chart(sourceCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Site Web', 'Admin'],
                    datasets: [{
                        data: [<?php echo $advanced_stats['source_stats']['public']; ?>, <?php echo $advanced_stats['source_stats']['admin']; ?>],
                        backgroundColor: ['#7f9c96', '#a9d1c9']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'bottom' }
                }
            });

        });
        </script>
    </div>
    <?php
} 