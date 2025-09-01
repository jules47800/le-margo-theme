<?php
/**
 * Fonctions pour la gestion administrative des réservations
 *
 * @package Le_Margo
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

require_once get_template_directory() . '/inc/smtp-config.php';

/**
 * Ajoute une page d'administration pour les réservations
 */
function le_margo_add_admin_menu() {
    add_menu_page(
        __('Réservations', 'le-margo'),
        __('Réservations', 'le-margo'),
        'manage_options',
        'le-margo-reservations',
        'le_margo_reservations_page',
        'dashicons-calendar-alt',
        30
    );
    
    // Ajouter une sous-page pour les paramètres
    add_submenu_page(
        'le-margo-reservations',
        __('Paramètres de réservation', 'le-margo'),
        __('Paramètres', 'le-margo'),
        'manage_options',
        'le-margo-reservation-settings',
        'le_margo_reservation_settings_page'
    );
}
add_action('admin_menu', 'le_margo_add_admin_menu');

/**
 * Enregistrer les paramètres de réservation
 */
function le_margo_register_reservation_settings() {
    register_setting('le_margo_reservation_settings', 'le_margo_restaurant_capacity', [
        'type' => 'integer',
        'default' => 4,
        'sanitize_callback' => 'absint',
    ]);
    
    register_setting('le_margo_reservation_settings', 'le_margo_reminder_time', [
        'type' => 'integer',
        'default' => 90,
        'sanitize_callback' => 'absint',
    ]);

    register_setting('le_margo_reservation_settings', 'le_margo_table_hold_time', [
        'type' => 'integer',
        'default' => 15,
        'sanitize_callback' => 'absint',
    ]);
    
    // NOUVEAU : Périodes de fermeture (vacances)
    register_setting('le_margo_reservation_settings', 'le_margo_holiday_dates', [
        'type' => 'string',
        'default' => '',
        'sanitize_callback' => 'le_margo_sanitize_holiday_dates',
    ]);
    
    // NOUVEAU : Système d'horaires par jour avec plages multiples
    register_setting('le_margo_reservation_settings', 'le_margo_daily_schedule', [
        'type' => 'array',
        'default' => array(
            'monday' => array(
                'open' => false,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:00')
                ),
                'slot_interval' => 30
            ),
            'tuesday' => array(
                'open' => true,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:00')
                ),
                'slot_interval' => 30
            ),
            'wednesday' => array(
                'open' => true,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:00')
                ),
                'slot_interval' => 30
            ),
            'thursday' => array(
                'open' => true,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:00')
                ),
                'slot_interval' => 30
            ),
            'friday' => array(
                'open' => true,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:30')
                ),
                'slot_interval' => 30
            ),
            'saturday' => array(
                'open' => true,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:30')
                ),
                'slot_interval' => 30
            ),
            'sunday' => array(
                'open' => false,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:00')
                ),
                'slot_interval' => 30
            )
        ),
        'sanitize_callback' => 'le_margo_sanitize_daily_schedule',
    ]);
    
    // Anciens paramètres (pour compatibilité)
    register_setting('le_margo_reservation_settings', 'le_margo_lunch_times', [
        'type' => 'string',
        'default' => '10:00,10:30,11:00,11:30,12:00,12:30,13:00,13:30,14:00',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    register_setting('le_margo_reservation_settings', 'le_margo_dinner_times', [
        'type' => 'string',
        'default' => '19:00,19:30,20:00,20:30,21:00,21:30,22:00',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
}
add_action('admin_init', 'le_margo_register_reservation_settings');

/**
 * Sanitize le planning quotidien
 */
function le_margo_sanitize_daily_schedule($input) {
    $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    $output = array();
    
    foreach ($days as $day) {
        if (!isset($input[$day])) {
            $output[$day] = array(
                'open' => false,
                'time_ranges' => array(
                    array('start' => '12:00', 'end' => '14:00'),
                    array('start' => '19:00', 'end' => '22:00')
                ),
                'slot_interval' => 30
            );
            continue;
        }
        
        $day_data = $input[$day];
        
        $output[$day] = array(
            'open' => isset($day_data['open']) ? (bool)$day_data['open'] : false,
            'time_ranges' => array(),
            'slot_interval' => isset($day_data['slot_interval']) ? absint($day_data['slot_interval']) : 30
        );
        
        // Traitement des plages horaires
        if (isset($day_data['time_ranges']) && is_array($day_data['time_ranges'])) {
            foreach ($day_data['time_ranges'] as $range) {
                if (isset($range['start']) && isset($range['end'])) {
                    $start = sanitize_text_field($range['start']);
                    $end = sanitize_text_field($range['end']);
                    
                    // Validation des heures
                    if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $start) && 
                        preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $end)) {
                        $output[$day]['time_ranges'][] = array(
                            'start' => $start,
                            'end' => $end
                        );
                    }
                }
            }
        }
        
        // Si aucune plage valide, utiliser les valeurs par défaut
        if (empty($output[$day]['time_ranges'])) {
            $output[$day]['time_ranges'] = array(
                array('start' => '12:00', 'end' => '14:00'),
                array('start' => '19:00', 'end' => '22:00')
            );
        }
        
        // Validation de l'intervalle
        if (!in_array($output[$day]['slot_interval'], array(15, 30, 45, 60))) {
            $output[$day]['slot_interval'] = 30;
        }
    }
    
    return $output;
}

/**
 * Sanitize les dates de vacances
 */
function le_margo_sanitize_holiday_dates($input) {
    // S'attend à une chaîne de dates "YYYY-MM-DD", séparées par des virgules
    $dates = explode(',', $input);
    $sanitized_dates = [];
    foreach ($dates as $date_str) {
        $trimmed_date = trim($date_str);
        // Valider le format YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $trimmed_date)) {
            $sanitized_dates[] = $trimmed_date;
        }
    }
    return implode(',', $sanitized_dates);
}

/**
 * Page de paramètres de réservation
 */
function le_margo_reservation_settings_page() {
    ?>
    <div class="wrap le-margo-admin le-margo-reservations">
        <h1><span class="dashicons dashicons-calendar-alt"></span> <?php echo esc_html__('Paramètres de réservation', 'le-margo'); ?></h1>
        
        <div class="le-margo-admin-card">
            <form method="post" action="options.php">
                <?php settings_fields('le_margo_reservation_settings'); ?>
                
                <div class="form-section">
                    <h2><?php echo esc_html__('Capacité par créneau', 'le-margo'); ?></h2>
                    <p class="description"><?php echo esc_html__('Définissez le nombre maximum de couverts par créneau.', 'le-margo'); ?></p>
                    
                    <div class="form-field">
                        <label for="le_margo_restaurant_capacity"><?php echo esc_html__('Nombre de couverts par créneau', 'le-margo'); ?></label>
                        <input type="number" id="le_margo_restaurant_capacity" name="le_margo_restaurant_capacity" 
                               value="<?php echo esc_attr(get_option('le_margo_restaurant_capacity', 4)); ?>" min="1" max="20">
                        <p class="field-help"><?php echo esc_html__('Recommandé : 4-6 couverts par créneau pour un service optimal', 'le-margo'); ?></p>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><?php echo esc_html__('Paramètres des rappels par email', 'le-margo'); ?></h2>
                    <p class="description"><?php echo esc_html__('Configurez les rappels envoyés aux clients avant leur réservation.', 'le-margo'); ?></p>
                    
                    <div class="form-field">
                        <label for="le_margo_reminder_time"><?php echo esc_html__('Envoyer le rappel (minutes avant la réservation)', 'le-margo'); ?></label>
                        <input type="number" id="le_margo_reminder_time" name="le_margo_reminder_time" 
                               value="<?php echo esc_attr(get_option('le_margo_reminder_time', 90)); ?>" min="30" max="1440" step="30">
                        <p class="field-help"><?php echo esc_html__('Par défaut: 90 minutes (1h30)', 'le-margo'); ?></p>
                    </div>
                </div>

                <div class="form-section">
                    <h2><?php echo esc_html__('Politique de réservation', 'le-margo'); ?></h2>
                    <div class="form-field">
                        <label for="le_margo_table_hold_time"><?php echo esc_html__('Temps de maintien de la table (minutes)', 'le-margo'); ?></label>
                        <input type="number" id="le_margo_table_hold_time" name="le_margo_table_hold_time"
                               value="<?php echo esc_attr(get_option('le_margo_table_hold_time', 15)); ?>" min="5" max="60" step="5">
                        <p class="field-help"><?php echo esc_html__('Durée après laquelle une table non occupée peut être réattribuée. Par défaut : 15 minutes.', 'le-margo'); ?></p>
                    </div>
                </div>

                <div class="form-section">
                    <h2><?php echo esc_html__('Périodes de fermeture (Vacances)', 'le-margo'); ?></h2>
                    <p class="description"><?php echo esc_html__('Bloquez des dates ou des périodes pour les réservations. Idéal pour les vacances ou les fermetures exceptionnelles.', 'le-margo'); ?></p>
                    
                    <div class="form-field">
                        <label for="le_margo_holiday_dates_calendar"><?php echo esc_html__('Cliquez sur les dates pour les bloquer/débloquer', 'le-margo'); ?></label>
                        <!-- Ce div affichera le calendrier -->
                        <div id="le_margo_holiday_dates_calendar"></div>
                        <!-- Ce champ caché stockera les dates pour l'envoi du formulaire -->
                        <input type="hidden" id="le_margo_holiday_dates" name="le_margo_holiday_dates" value="<?php echo esc_attr(get_option('le_margo_holiday_dates', '')); ?>">
                        <p class="field-help"><?php echo esc_html__('Les jours sélectionnés en orange seront bloqués à la réservation.', 'le-margo'); ?></p>
                    </div>
                </div>
                
                <?php le_margo_daily_schedule_settings_section(); ?>
                
                <?php submit_button(__('Enregistrer les paramètres', 'le-margo'), 'primary', 'submit', true); ?>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Section des horaires quotidiens
 */
function le_margo_daily_schedule_settings_section() {
    $days = array(
        'monday'    => __('Lundi', 'le-margo'),
        'tuesday'   => __('Mardi', 'le-margo'),
        'wednesday' => __('Mercredi', 'le-margo'),
        'thursday'  => __('Jeudi', 'le-margo'),
        'friday'    => __('Vendredi', 'le-margo'),
        'saturday'  => __('Samedi', 'le-margo'),
        'sunday'    => __('Dimanche', 'le-margo'),
    );
    
    $schedule = get_option('le_margo_daily_schedule', array());
    
    echo '<div class="form-section daily-schedule-section">';
    echo '<h2>' . esc_html__('Planning hebdomadaire', 'le-margo') . '</h2>';
    echo '<p class="description">' . esc_html__('Configurez les horaires de réservation pour chaque jour de la semaine.', 'le-margo') . '</p>';
    
    echo '<div class="schedule-grid">';
    
    foreach ($days as $day_key => $day_label) {
        $day_data = isset($schedule[$day_key]) ? $schedule[$day_key] : array(
            'open' => false,
            'time_ranges' => array(
                array('start' => '12:00', 'end' => '14:00'),
                array('start' => '19:00', 'end' => '22:00')
            ),
            'slot_interval' => 30
        );
        
        $is_open = isset($day_data['open']) ? $day_data['open'] : false;
        $time_ranges = isset($day_data['time_ranges']) ? $day_data['time_ranges'] : array(
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '19:00', 'end' => '22:00')
        );
        $slot_interval = isset($day_data['slot_interval']) ? $day_data['slot_interval'] : 30;
        
        echo '<div class="schedule-day" data-day="' . esc_attr($day_key) . '">';
        echo '<div class="day-header">';
        echo '<h3>' . esc_html($day_label) . '</h3>';
        echo '<label class="day-toggle">';
        echo '<input type="checkbox" name="le_margo_daily_schedule[' . esc_attr($day_key) . '][open]" value="1" ' . checked($is_open, true, false) . '>';
        echo '<span class="toggle-slider"></span>';
        echo '<span class="toggle-label">' . esc_html__('Ouvert', 'le-margo') . '</span>';
        echo '</label>';
        echo '</div>';
        
        echo '<div class="day-schedule ' . ($is_open ? 'day-open' : 'day-closed') . '">';
        
        // Plages horaires
        echo '<div class="time-ranges-section">';
        echo '<h4>' . esc_html__('Plages horaires', 'le-margo') . '</h4>';
        echo '<div class="time-ranges-container" data-day="' . esc_attr($day_key) . '">';
        
        foreach ($time_ranges as $index => $range) {
            echo '<div class="time-range-row">';
            echo '<div class="time-inputs">';
            echo '<div class="time-input">';
            echo '<label>' . esc_html__('Début', 'le-margo') . '</label>';
            echo '<input type="time" name="le_margo_daily_schedule[' . esc_attr($day_key) . '][time_ranges][' . $index . '][start]" value="' . esc_attr($range['start']) . '">';
            echo '</div>';
            echo '<div class="time-input">';
            echo '<label>' . esc_html__('Fin', 'le-margo') . '</label>';
            echo '<input type="time" name="le_margo_daily_schedule[' . esc_attr($day_key) . '][time_ranges][' . $index . '][end]" value="' . esc_attr($range['end']) . '">';
            echo '</div>';
            echo '</div>';
            echo '<button type="button" class="remove-range" data-day="' . esc_attr($day_key) . '" data-index="' . $index . '">' . esc_html__('Supprimer', 'le-margo') . '</button>';
            echo '</div>';
        }
        
        echo '</div>'; // .time-ranges-container
        echo '<button type="button" class="add-range" data-day="' . esc_attr($day_key) . '">' . esc_html__('+ Ajouter une plage', 'le-margo') . '</button>';
        echo '</div>'; // .time-ranges-section
        
        // Intervalle des créneaux
        echo '<div class="slot-interval">';
        echo '<label>' . esc_html__('Intervalle des créneaux', 'le-margo') . '</label>';
        echo '<select name="le_margo_daily_schedule[' . esc_attr($day_key) . '][slot_interval]">';
        echo '<option value="15" ' . selected($slot_interval, 15, false) . '>15 minutes</option>';
        echo '<option value="30" ' . selected($slot_interval, 30, false) . '>30 minutes</option>';
        echo '<option value="45" ' . selected($slot_interval, 45, false) . '>45 minutes</option>';
        echo '<option value="60" ' . selected($slot_interval, 60, false) . '>1 heure</option>';
        echo '</select>';
        echo '</div>';
        
        // Aperçu des créneaux
        echo '<div class="slots-preview">';
        echo '<h5>' . esc_html__('Créneaux générés', 'le-margo') . '</h5>';
        echo '<div class="slots-list" id="slots-' . esc_attr($day_key) . '">';
        if ($is_open) {
            $all_slots = array();
            foreach ($time_ranges as $range) {
                $slots = le_margo_generate_time_slots($range['start'], $range['end'], $slot_interval);
                $all_slots = array_merge($all_slots, $slots);
            }
            echo '<span class="slot-count">' . count($all_slots) . ' créneaux</span>';
            echo '<div class="slots-example">';
            $example_slots = array_slice($all_slots, 0, 5);
            echo implode(', ', $example_slots);
            if (count($all_slots) > 5) {
                echo '...';
            }
            echo '</div>';
        } else {
            echo '<span class="day-closed-text">' . esc_html__('Jour fermé', 'le-margo') . '</span>';
        }
        echo '</div>';
        echo '</div>';
        
        echo '</div>'; // .day-schedule
        echo '</div>'; // .schedule-day
    }
    
    echo '</div>'; // .schedule-grid
    echo '</div>'; // .form-section
}

/**
 * Génère les créneaux horaires entre deux heures
 */
function le_margo_generate_time_slots($start_time, $end_time, $interval_minutes = 30) {
    $slots = array();
    
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = new DateInterval('PT' . $interval_minutes . 'M');
    
    $current = clone $start;
    
    while ($current < $end) {
        $slots[] = $current->format('H:i');
        $current->add($interval);
    }
    
    return $slots;
}

/**
 * Récupère les créneaux disponibles pour une date donnée
 */
function le_margo_get_available_slots_for_date($date) {
    $schedule = get_option('le_margo_daily_schedule', array());
    
    // Obtenir le jour de la semaine (0 = dimanche, 1 = lundi, etc.)
    $day_of_week = date('w', strtotime($date));
    $day_names = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
    $day_key = $day_names[$day_of_week];
    
    if (!isset($schedule[$day_key]) || !$schedule[$day_key]['open']) {
        return array();
    }
    
    $day_data = $schedule[$day_key];
    $slots = array();
    
    // Générer les créneaux pour toutes les plages horaires
    if (isset($day_data['time_ranges']) && is_array($day_data['time_ranges'])) {
        foreach ($day_data['time_ranges'] as $range) {
            if (!empty($range['start']) && !empty($range['end'])) {
                $time_slots = le_margo_generate_time_slots(
                    $range['start'], 
                    $range['end'], 
                    $day_data['slot_interval']
                );
                foreach ($time_slots as $slot) {
                    $slots[] = array(
                        'time' => $slot,
                        'meal_type' => 'general' // Plus de distinction déjeuner/dîner
                    );
                }
            }
        }
    }
    
    return $slots;
}

/**
 * Génère une URL d'action en préservant les paramètres de filtrage
 */
function le_margo_get_action_url($action, $reservation_id, $date_filter, $status_filter) {
    $params = array(
        'page' => 'le-margo-reservations',
        'action' => $action,
        'id' => $reservation_id
    );
    
    // Ajouter les paramètres de filtrage s'ils existent
    if (!empty($date_filter)) {
        $params['date_filter'] = $date_filter;
    }
    if (!empty($status_filter)) {
        $params['status_filter'] = $status_filter;
    }
    
    $url = add_query_arg($params, admin_url('admin.php'));
    return wp_nonce_url($url, 'reservation_action_' . $reservation_id);
}

/**
 * Affichage de la page d'administration des réservations
 */
function le_margo_reservations_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';

    // Traitement des actions
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = sanitize_text_field($_GET['action']);
        $id = intval($_GET['id']);
        
        // Vérifier le nonce pour la sécurité
        if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'reservation_action_' . $id)) {
            if ($action === 'confirm') {
                $wpdb->update(
                    $table_name,
                    array('status' => 'confirmed'),
                    array('id' => $id)
                );
                add_settings_error('le_margo_reservations', 'reservation_confirmed', __('Réservation confirmée avec succès.', 'le-margo'), 'success');
                
                // Envoyer un email de confirmation au client
                if (function_exists('le_margo_get_reservation_manager')) {
                    le_margo_get_reservation_manager()->send_confirmation_email($id);
                }
                
            } elseif ($action === 'cancel') {
                $wpdb->update(
                    $table_name,
                    array('status' => 'cancelled'),
                    array('id' => $id)
                );
                add_settings_error('le_margo_reservations', 'reservation_cancelled', __('Réservation annulée.', 'le-margo'), 'success');
                
                 // Envoyer notification d'annulation au client
                 if (function_exists('le_margo_get_email_manager')) {
                    $reservation = le_margo_get_reservation_manager()->get_reservation($id);
                    if ($reservation && function_exists('le_margo_get_email_manager')) {
                        le_margo_get_email_manager()->send_cancellation_email($reservation);
                    }
                }
                
            } elseif ($action === 'noshow') { // Action No-Show
                $wpdb->update(
                    $table_name,
                    array('status' => 'no-show'),
                    array('id' => $id)
                );
                add_settings_error('le_margo_reservations', 'reservation_noshow', __('Réservation marquée comme No-Show.', 'le-margo'), 'success');

            } elseif ($action === 'delete') {
                $wpdb->delete(
                    $table_name,
                    array('id' => $id)
                );
                add_settings_error('le_margo_reservations', 'reservation_deleted', __('Réservation supprimée définitivement.', 'le-margo'), 'success');
                
            } elseif ($action === 'send_confirmation') {
                $sent = false;
                if (function_exists('le_margo_get_reservation_manager')) {
                   $sent = le_margo_get_reservation_manager()->send_confirmation_email($id);
                }
                if ($sent) {
                    add_settings_error('le_margo_reservations', 'email_sent', __('Email de confirmation envoyé avec succès.', 'le-margo'), 'success');
                } else {
                    add_settings_error('le_margo_reservations', 'email_error', __('Erreur lors de l\'envoi de l\'email.', 'le-margo'), 'error');
                }
                
            } elseif ($action === 'send_reminder') {
                $sent = false;
                 if (function_exists('le_margo_get_reservation_manager')) {
                   $sent = le_margo_get_reservation_manager()->send_reminder_email($id);
                }
                if ($sent) {
                    add_settings_error('le_margo_reservations', 'reminder_sent', __('Rappel envoyé avec succès.', 'le-margo'), 'success');
                } else {
                    add_settings_error('le_margo_reservations', 'reminder_error', __('Erreur lors de l\'envoi du rappel.', 'le-margo'), 'error');
                }
            }
        }
    }

    // Traitement du formulaire rapide d'ajout de réservation
    if (isset($_POST['quick_add_reservation']) && check_admin_referer('quick_add_reservation')) {
        $reservation_manager = le_margo_get_reservation_manager();
        $date = sanitize_text_field($_POST['reservation_date']);
        $time = sanitize_text_field($_POST['reservation_time']);
        $people = intval($_POST['people']);
        
        // Pour l'admin, on ne vérifie pas la disponibilité pour permettre le surbooking.
        // On crée directement la réservation.
        if ($reservation_manager) {
            $customer_email = isset($_POST['customer_email']) && !empty($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : null;

            $data = array(
                'reservation_date' => $date,
                'reservation_time' => $time,
                'people' => $people,
                'customer_name' => sanitize_text_field($_POST['customer_name']),
                'customer_phone' => sanitize_text_field($_POST['customer_phone']),
                'customer_email' => $customer_email,
                'notes' => sanitize_textarea_field($_POST['notes']),
                'status' => 'confirmed',
                'source' => 'admin', // Ajout de la source
                'meal_type' => 'general',
                'confirmation_email_sent' => 0, // Initialiser à non envoyé
            );

            $reservation_id = $reservation_manager->create_reservation($data);

            if ($reservation_id) {
                add_settings_error('le_margo_reservations', 'reservation_added', __('Réservation ajoutée avec succès.', 'le-margo'), 'success');
                
                // Envoyer l'email de confirmation si un email est fourni
                if ($customer_email && function_exists('le_margo_get_email_manager')) {
                    $email_sent = $reservation_manager->send_confirmation_email($reservation_id);
                    if ($email_sent) {
                        add_settings_error('le_margo_reservations', 'email_sent', __('Email de confirmation envoyé.', 'le-margo'), 'success');
                    } else {
                        add_settings_error('le_margo_reservations', 'email_error', __("La réservation a été créée, mais l'email de confirmation n'a pas pu être envoyé.", 'le-margo'), 'warning');
                    }
                }

                if(function_exists('le_margo_update_customer_visits') && !empty($data['customer_email'])) {
                    le_margo_update_customer_visits($data['customer_email'], $reservation_id);
                }
            } else {
                $error_message = __('Erreur lors de l\'ajout de la réservation.', 'le-margo');
                if (!empty($reservation_manager->last_error)) {
                    $error_message .= ' ' . sprintf(__('Détail de l\'erreur : %s', 'le-margo'), $reservation_manager->last_error);
                }
                add_settings_error('le_margo_reservations', 'reservation_error', $error_message, 'error');
            }
        } else {
            add_settings_error('le_margo_reservations', 'manager_unavailable', __('Le gestionnaire de réservation est indisponible.', 'le-margo'), 'error');
        }
    }

    // -- NOUVELLE LOGIQUE DE FILTRAGE ET PAGINATION --

    // 1. Paramètres de pagination et de filtrage
    $items_per_page = 20;
    $today = date('Y-m-d');
    
    // Récupérer les filtres depuis l'URL
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $date_filter = isset($_GET['date_filter']) && !empty($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : '';
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

    // 2. Construire la clause WHERE pour les requêtes
    $where_clauses = array();
    if (!empty($search_query)) {
        $search_like = '%' . $wpdb->esc_like($search_query) . '%';
        $where_clauses[] = $wpdb->prepare('(customer_name LIKE %s OR customer_email LIKE %s OR customer_phone LIKE %s OR id = %d)', $search_like, $search_like, $search_like, intval($search_query));
    }
    if (!empty($date_filter)) {
        $where_clauses[] = $wpdb->prepare('reservation_date = %s', $date_filter);
    }
    if (!empty($status_filter)) {
        $where_clauses[] = $wpdb->prepare('status = %s', $status_filter);
    }

    $where_sql = "";
    if (!empty($where_clauses)) {
        $where_sql = " WHERE " . implode(' AND ', $where_clauses);
    }

    // L'ancien système de saut de page est supprimé car le nouveau tri affiche directement les réservations du jour en premier.
    
    // 4. Récupérer les données pour le tableau
    // Obtenir le nombre total d'éléments correspondant aux filtres pour la pagination
    $total_items_query = "SELECT COUNT(id) FROM $table_name" . $where_sql;
    $total_items = $wpdb->get_var($total_items_query);

    // Calculer l'offset et récupérer les réservations pour la page actuelle
    $offset = ($current_page - 1) * $items_per_page;
    $reservations_query = $wpdb->prepare(
        "SELECT * FROM $table_name" . $where_sql . " 
         ORDER BY 
            -- 1. Grouper par futur/passé. 0 pour futur/aujourd'hui, 1 pour passé.
            CASE WHEN reservation_date >= %s THEN 0 ELSE 1 END ASC,
            -- 2. Pour le futur, trier par date/heure ASC.
            CASE WHEN reservation_date >= %s THEN reservation_date END ASC,
            CASE WHEN reservation_date >= %s THEN reservation_time END ASC,
            -- 3. Pour le passé, trier par date/heure DESC (plus récent en premier).
            CASE WHEN reservation_date < %s THEN reservation_date END DESC,
            CASE WHEN reservation_date < %s THEN reservation_time END DESC
         LIMIT %d OFFSET %d",
        $today,
        $today,
        $today,
        $today,
        $today,
        $items_per_page,
        $offset
    );
    $reservations = $wpdb->get_results($reservations_query);
    
    // Calculer le nombre total de pages
    $total_pages = ceil($total_items / $items_per_page);

    // -- Calcul des statistiques simplifiées et pertinentes --
    $today_covers_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(people) FROM $table_name WHERE reservation_date = %s AND status = 'confirmed'", 
        $today
    ));
    $pending_reservations_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(id) FROM $table_name WHERE status = 'pending' AND reservation_date >= %s", 
        $today
    ));
    $next_week_covers_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(people) FROM $table_name WHERE status = 'confirmed' AND reservation_date BETWEEN %s AND %s",
        $today,
        date('Y-m-d', strtotime('+6 days'))
    ));
    
    settings_errors('le_margo_reservations');
    ?>
    <div class="wrap le-margo-reservations">
        <h1><?php echo esc_html__('Gestion des réservations', 'le-margo'); ?></h1>
        
        <div class="reservation-stats" id="dashboard-widgets">
            <a href="<?php echo esc_url(admin_url('admin.php?page=le-margo-reservations&date_filter=' . $today . '&status_filter=confirmed#reservations-list')); ?>" class="stat-box stat-box-clickable" data-has-items="<?php echo $today_covers_count > 0 ? 'true' : 'false'; ?>">
                <div class="stat-icon"><span class="dashicons dashicons-food"></span></div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('Couverts ce jour', 'le-margo'); ?></h3>
                    <p class="stat-number"><?php echo esc_html($today_covers_count); ?></p>
                    <p class="stat-label"><?php echo esc_html__('Confirmés', 'le-margo'); ?></p>
                </div>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=le-margo-reservations&status_filter=pending#reservations-list')); ?>" class="stat-box stat-box-clickable" data-has-items="<?php echo $pending_reservations_count > 0 ? 'true' : 'false'; ?>">
                <div class="stat-icon"><span class="dashicons dashicons-clock"></span></div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('En attente', 'le-margo'); ?></h3>
                    <p class="stat-number"><?php echo esc_html($pending_reservations_count); ?></p>
                    <p class="stat-label"><?php echo esc_html__('Aujourd\'hui & à venir', 'le-margo'); ?></p>
                </div>
            </a>
            <div class="stat-box">
                <div class="stat-icon"><span class="dashicons dashicons-chart-bar"></span></div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('Couverts (7 jours)', 'le-margo'); ?></h3>
                    <p class="stat-number"><?php echo esc_html($next_week_covers_count); ?></p>
                    <p class="stat-label"><?php echo esc_html__('Prévisionnel', 'le-margo'); ?></p>
                </div>
            </div>
        </div>
        
        <div id="quick-reservation">
            <?php 
            if (file_exists(dirname(__FILE__) . '/quick-reservation-form.php')) {
                require_once dirname(__FILE__) . '/quick-reservation-form.php';
                le_margo_quick_reservation_form();
            }
            ?>
        </div>

        <?php wp_enqueue_style('le-margo-admin-css'); ?>
        
        <div class="le-margo-admin-card" id="reservations-list">
            <h2 style="margin-top: 0; padding: 0 0 15px 0; border-bottom: 1px solid #e2e4e7;"><span class="dashicons dashicons-list-view" style="margin-right: 8px;"></span><?php echo esc_html__('Liste des réservations', 'le-margo'); ?></h2>
            <div class="filter-bar">
                <form method="get" action="" class="filter-form" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <input type="hidden" name="page" value="le-margo-reservations">
                    
                    <div class="filter-group filter-group-search" style="flex-grow: 1;">
                        <label for="reservation-search-input" class="screen-reader-text"><?php echo esc_html__('Rechercher', 'le-margo'); ?></label>
                        <input type="search" id="reservation-search-input" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="<?php echo esc_attr__('Rechercher par nom, email, tél, ID...', 'le-margo'); ?>" style="width: 100%;">
                    </div>
                    
                    <div class="filter-group">
                        <label for="date_filter" class="screen-reader-text"><?php echo esc_html__('Date :', 'le-margo'); ?></label>
                        <input type="date" id="date_filter" name="date_filter" value="<?php echo esc_attr($date_filter); ?>" class="filter-input">
                    </div>
                    
                    <div class="filter-group">
                        <label for="status_filter" class="screen-reader-text"><?php echo esc_html__('Statut :', 'le-margo'); ?></label>
                        <select name="status_filter" id="status_filter" class="filter-input">
                            <option value=""><?php echo esc_html__('Tous les statuts', 'le-margo'); ?></option>
                            <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php echo esc_html__('En attente', 'le-margo'); ?></option>
                            <option value="confirmed" <?php selected($status_filter, 'confirmed'); ?>><?php echo esc_html__('Confirmé', 'le-margo'); ?></option>
                            <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php echo esc_html__('Annulé', 'le-margo'); ?></option>
                            <option value="no-show" <?php selected($status_filter, 'no-show'); ?>><?php echo esc_html__('No-Show', 'le-margo'); ?></option>
                            <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php echo esc_html__('Terminée', 'le-margo'); ?></option>
                        </select>
                    </div>

                    <div class="filter-actions" style="display: flex; gap: 8px;">
                        <button type="submit" class="button button-primary filter-button"><span class="dashicons dashicons-filter"></span><?php echo esc_html__('Filtrer', 'le-margo'); ?></button>
                        <?php if ($is_filtered) : ?>
                            <a href="?page=le-margo-reservations" class="button reset-button"><span class="dashicons dashicons-dismiss"></span><?php echo esc_html__('Réinitialiser', 'le-margo'); ?></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="reservations-table-container">
                <table class="wp-list-table widefat fixed striped reservations-table">
                    <thead>
                        <tr>
                            <th class="column-id"><?php echo esc_html__('ID', 'le-margo'); ?></th>
                            <th class="column-date"><?php echo esc_html__('Date', 'le-margo'); ?></th>
                            <th class="column-time"><?php echo esc_html__('Heure', 'le-margo'); ?></th>
                            <th class="column-people"><?php echo esc_html__('Pers.', 'le-margo'); ?></th>
                            <th class="column-customer"><?php echo esc_html__('Client', 'le-margo'); ?></th>
                            <th class="column-status"><?php echo esc_html__('Statut', 'le-margo'); ?></th>
                            <th class="column-notes"><?php echo esc_html__('Notes', 'le-margo'); ?></th>
                            <th class="column-actions"><?php echo esc_html__('Actions', 'le-margo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)) : ?>
                            <tr><td colspan="8" class="no-results"><?php echo esc_html__('Aucune réservation trouvée.', 'le-margo'); ?></td></tr>
                        <?php else : ?>
                            <?php foreach ($reservations as $reservation) : ?>
                                <?php 
                                $date_obj = new DateTime($reservation->reservation_date);
                                $reservation_datetime = new DateTime($reservation->reservation_date . ' ' . $reservation->reservation_time);
                                $now = new DateTime();
                                $is_past = $reservation_datetime < $now;
                                
                                $status_labels = [
                                    'pending' => __('En attente', 'le-margo'),
                                    'confirmed' => __('Confirmé', 'le-margo'),
                                    'cancelled' => __('Annulé', 'le-margo'),
                                    'no-show' => __('No-Show', 'le-margo'),
                                    'completed' => __('Terminée', 'le-margo')
                                ];
                                $status_class = "status-" . $reservation->status;
                                $status_label = $status_labels[$reservation->status] ?? $reservation->status;
                                
                                $row_class = $reservation->reservation_date === $today ? 'today-reservation' : '';
                                if (isset($_GET['id']) && intval($_GET['id']) === $reservation->id) {
                                    $row_class .= ' reservation-action-executed';
                                }
                                if (isset($reservation->source) && $reservation->source === 'admin') {
                                    $row_class .= ' admin-reservation';
                                }
                                ?>
                                <tr class="<?php echo esc_attr($row_class); ?>">
                                    <td class="column-id" data-label="<?php echo esc_attr__('ID', 'le-margo'); ?>"><?php echo esc_html($reservation->id); ?></td>
                                    <td class="column-date" data-label="<?php echo esc_attr__('Date', 'le-margo'); ?>"><div class="date-info"><span class="date-display"><?php echo esc_html($date_obj->format('d/m/Y')); ?></span><span class="day-label"><?php echo esc_html($date_obj->format('D')); ?></span></div></td>
                                    <td class="column-time" data-label="<?php echo esc_attr__('Heure', 'le-margo'); ?>"><?php echo esc_html(date('H:i', strtotime($reservation->reservation_time))); ?></td>
                                    <td class="column-people" data-label="<?php echo esc_attr__('Personnes', 'le-margo'); ?>"><span class="people-count"><?php echo esc_html($reservation->people); ?></span></td>
                                    <td class="column-customer" data-label="<?php echo esc_attr__('Client', 'le-margo'); ?>">
                                        <div class="customer-info">
                                            <div class="customer-name"><?php echo esc_html($reservation->customer_name); ?></div>
                                            <?php if (!empty($reservation->customer_email)) : ?><a href="mailto:<?php echo esc_attr($reservation->customer_email); ?>" class="customer-email"><span class="dashicons dashicons-email"></span><?php echo esc_html($reservation->customer_email); ?></a><?php endif; ?>
                                            <?php if (!empty($reservation->customer_phone)) : ?><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $reservation->customer_phone)); ?>" class="customer-phone"><span class="dashicons dashicons-phone"></span><?php echo esc_html($reservation->customer_phone); ?></a><?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="column-status" data-label="<?php echo esc_attr__('Statut', 'le-margo'); ?>"><span class="status-badge <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_label); ?></span></td>
                                    <td class="column-notes" data-label="<?php echo esc_attr__('Notes', 'le-margo'); ?>">
                                        <?php if (!empty($reservation->notes)) : ?>
                                            <div class="notes-content">
                                                <span class="notes-icon dashicons dashicons-admin-comments"></span>
                                                <span class="notes-text"><?php echo esc_html($reservation->notes); ?></span>
                                            </div>
                                        <?php else : ?>
                                            <span class="no-notes">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="column-actions" data-label="<?php echo esc_attr__('Actions', 'le-margo'); ?>">
                                        <div class="action-buttons">
                                            <button type="button" class="button action-button edit-button" data-reservation-id="<?php echo esc_attr($reservation->id); ?>" title="<?php echo esc_attr__('Modifier', 'le-margo'); ?>"><span class="dashicons dashicons-edit"></span></button>
                                            <?php if ($reservation->status === 'pending') : ?>
                                                <a href="<?php echo le_margo_get_action_url('confirm', $reservation->id, $date_filter, $status_filter); ?>" class="button action-button confirm-button" title="<?php echo esc_attr__('Confirmer', 'le-margo'); ?>" data-touch="1"><span class="dashicons dashicons-yes"></span></a>
                                            <?php endif; ?>
                                             <?php if ($is_past && $reservation->status === 'confirmed') : ?>
                                                <a href="<?php echo le_margo_get_action_url('noshow', $reservation->id, $date_filter, $status_filter); ?>" class="button action-button noshow-button" title="<?php echo esc_attr__('Marquer comme No-Show', 'le-margo'); ?>" data-touch="1" onclick="return confirm('<?php echo esc_js(__('Marquer cette réservation comme non-présentée ?', 'le-margo')); ?>');"><span class="dashicons dashicons-marker" style="color:#a00;"></span></a>
                                            <?php endif; ?>
                                            <?php if ($reservation->status !== 'cancelled') : ?>
                                                <a href="<?php echo le_margo_get_action_url('cancel', $reservation->id, $date_filter, $status_filter); ?>" class="button action-button cancel-button" title="<?php echo esc_attr__('Annuler', 'le-margo'); ?>" data-touch="1" onclick="return confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir annuler cette réservation?', 'le-margo')); ?>');"><span class="dashicons dashicons-no"></span></a>
                                            <?php endif; ?>
                                            <a href="<?php echo le_margo_get_action_url('delete', $reservation->id, $date_filter, $status_filter); ?>" class="button action-button delete-button" title="<?php echo esc_attr__('Supprimer', 'le-margo'); ?>" data-touch="1" onclick="return confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir supprimer définitivement cette réservation?', 'le-margo')); ?>');"><span class="dashicons dashicons-trash"></span></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="tablenav bottom" style="padding: 15px 0 0 0;">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo esc_html($total_items); ?> réservations</span>
                    <?php
                    if ($total_pages > 1) {
                        $pagination_args = array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'total' => $total_pages,
                            'current' => $current_page,
                            'show_all' => false,
                            'end_size' => 1,
                            'mid_size' => 2,
                            'prev_next' => true,
                            'prev_text' => __('&laquo; Précédent'),
                            'next_text' => __('Suivant &raquo;'),
                            'type' => 'plain',
                        );
                        
                        // Conserver les paramètres de filtre dans les liens de pagination
                        $pagination_args['add_args'] = array();
                        if (!empty($search_query)) $pagination_args['add_args']['s'] = urlencode($search_query);
                        if (!empty($date_filter)) $pagination_args['add_args']['date_filter'] = $date_filter;
                        if (!empty($status_filter)) $pagination_args['add_args']['status_filter'] = $status_filter;
                        
                        echo paginate_links($pagination_args);
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="le-margo-admin-card" id="email-tests" style="margin-top: 30px;">
             <h2 style="margin-top: 0; padding: 0 0 15px 0; border-bottom: 1px solid #e2e4e7;"><span class="dashicons dashicons-admin-tools" style="margin-right: 8px;"></span><?php echo esc_html__('Diagnostic et tests', 'le-margo'); ?></h2>
             <div class="diagnostic-section">
                <h3><?php echo esc_html__('Test d\'envoi d\'emails', 'le-margo'); ?></h3>
                <p class="description"><?php echo esc_html__('Utilisez ce bouton pour tester la configuration SMTP et l\'envoi d\'emails.', 'le-margo'); ?></p>
                <button type="button" id="test-email-btn" class="button button-secondary"><span class="dashicons dashicons-email-alt"></span><?php echo esc_html__('Tester l\'envoi d\'emails', 'le-margo'); ?></button>
                <div id="test-email-result" style="margin-top: 15px;"></div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Fonction AJAX pour confirmer une réservation
 */
function le_margo_confirm_reservation_callback() {
    // Vérifier nonce
    check_ajax_referer('le_margo_confirm_reservation', 'security');
    
    // Vérifier les droits
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array(
            'message' => __('Vous n\'avez pas les autorisations nécessaires.', 'le-margo')
        ));
    }
    
    // Vérifier réservation ID
    if (!isset($_POST['reservation_id']) || !is_numeric($_POST['reservation_id'])) {
        wp_send_json_error(array(
            'message' => __('ID de réservation invalide.', 'le-margo')
        ));
    }
    
    $reservation_id = intval($_POST['reservation_id']);
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // Récupérer la réservation
    $reservation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $reservation_id
    ));
    
    if (!$reservation) {
        wp_send_json_error(array(
            'message' => __('Réservation introuvable.', 'le-margo')
        ));
    }
    
    // Mettre à jour le statut
    $result = $wpdb->update(
        $table_name,
        array('status' => 'confirmed'),
        array('id' => $reservation_id)
    );
    
    if ($result === false) {
        wp_send_json_error(array(
            'message' => __('Erreur lors de la confirmation de la réservation.', 'le-margo')
        ));
    }
    
    // Envoyer l'email de confirmation si pas déjà envoyé
    if (!$reservation->confirmation_email_sent) {
        $email_sent = le_margo_send_confirmation_email($reservation_id);
        
        if ($email_sent) {
            $wpdb->update(
                $table_name,
                array('confirmation_email_sent' => 1),
                array('id' => $reservation_id)
            );
        }
    }
    
    // Mettre à jour les statistiques client
    if (!empty($reservation->customer_email)) {
        le_margo_update_customer_visits($reservation->customer_email, $reservation_id);
    }
    
    wp_send_json_success(array(
        'message' => __('Réservation confirmée avec succès !', 'le-margo')
    ));
}
add_action('wp_ajax_le_margo_confirm_reservation', 'le_margo_confirm_reservation_callback');


/**
 * Répond à la requête AJAX pour obtenir les disponibilités.
 * C'est le point d'entrée pour le script de réservation côté client.
 */
function le_margo_get_availability_callback() {
    // Vérification de sécurité de base
    if (!isset($_GET['date'])) {
        wp_send_json_error(['message' => 'Date manquante.'], 400);
    }

    $date_str = sanitize_text_field($_GET['date']);
    
    // Validation simple du format de date YYYY-MM-DD
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_str)) {
        wp_send_json_error(['message' => 'Format de date invalide.'], 400);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // 1. Récupérer les créneaux théoriques pour ce jour
    $available_slots = le_margo_get_available_slots_for_date($date_str);
    
    // 2. Récupérer les réservations existantes pour ce jour (NOUVELLE LOGIQUE ROBUSTE)
    $raw_reservations = $wpdb->get_results($wpdb->prepare(
        "SELECT reservation_time, people 
         FROM $table_name 
         WHERE reservation_date = %s 
         AND status IN ('confirmed', 'pending')",
        $date_str
    ));
    
    $reservations_by_time = [];
    foreach ($raw_reservations as $res) {
        // On formate l'heure en PHP pour garantir le format H:i
        $time_key = date('H:i', strtotime($res->reservation_time));
        if (!isset($reservations_by_time[$time_key])) {
            $reservations_by_time[$time_key] = 0;
        }
        $reservations_by_time[$time_key] += (int)$res->people;
    }

    // 3. Récupérer la capacité du restaurant
    $capacity_per_slot = (int)get_option('le_margo_restaurant_capacity', 4);

    $response_data = [
        'available_slots' => $available_slots,
        'time_slots' => $reservations_by_time,
        'capacity_per_slot' => $capacity_per_slot,
    ];

    wp_send_json_success($response_data);
}
add_action('wp_ajax_le_margo_get_availability', 'le_margo_get_availability_callback');
add_action('wp_ajax_nopriv_le_margo_get_availability', 'le_margo_get_availability_callback');


/**
 * Récupérer une réservation (AJAX)
 */
function le_margo_get_reservation_ajax() {
    check_ajax_referer('le_margo_reservation_edit', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Accès refusé', 'le-margo')], 403);
    }
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        wp_send_json_error(['message' => __('ID invalide', 'le-margo')], 400);
    }
    $id = intval($_GET['id']);
    $reservation = le_margo_get_reservation_manager()->get_reservation($id);
    if (!$reservation) {
        wp_send_json_error(['message' => __('Introuvable', 'le-margo')], 404);
    }
    wp_send_json_success($reservation);
}
add_action('wp_ajax_le_margo_get_reservation', 'le_margo_get_reservation_ajax');

/**
 * Mettre à jour une réservation (AJAX)
 */
function le_margo_update_reservation_ajax() {
    check_ajax_referer('le_margo_reservation_edit', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Accès refusé', 'le-margo')], 403);
    }
    $required = ['id','reservation_date','reservation_time','people','customer_name'];
    foreach ($required as $key) {
        if (!isset($_POST[$key]) || $_POST[$key] === '') {
            wp_send_json_error(['message' => __('Champs manquants', 'le-margo')], 400);
        }
    }

    $id = intval($_POST['id']);
    $data = array(
        'reservation_date' => sanitize_text_field($_POST['reservation_date']),
        'reservation_time' => sanitize_text_field($_POST['reservation_time']),
        'people' => absint($_POST['people']),
        'customer_name' => sanitize_text_field($_POST['customer_name']),
        'customer_email' => isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : null,
        'customer_phone' => isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : null,
        'notes' => isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '',
    );

    $updated = le_margo_get_reservation_manager()->update_reservation($id, $data);
    if ($updated === false) {
        wp_send_json_error(['message' => __('Échec de la mise à jour', 'le-margo')], 500);
    }
    wp_send_json_success(['message' => __('Réservation mise à jour', 'le-margo')]);
}
add_action('wp_ajax_le_margo_update_reservation', 'le_margo_update_reservation_ajax');