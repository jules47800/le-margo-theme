<?php
/**
 * Gestion des notifications et des annulations de réservation
 */

/**
 * Envoyer un email de rappel
 */
function le_margo_send_reminder_email($reservation_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // Récupérer les informations de la réservation
    $reservation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $reservation_id
    ));
    
    if (!$reservation || $reservation->reminder_sent) {
        return false;
    }
    
    $email_manager = Le_Margo_Email_Manager::get_instance();
    
    // Préparer le message
    $subject = __('Rappel de votre réservation - Le Margo', 'le-margo');
    
    $content = '<h2>' . __('Rappel de votre réservation', 'le-margo') . '</h2>';
    $content .= '<p>Bonjour ' . esc_html($reservation->customer_name) . ',</p>';
    $content .= '<p>Rappel de votre réservation au restaurant <strong>Le Margo</strong> :</p>';
    $content .= '<div class="reservation-details">'
        . '<div class="detail-row"><span class="detail-label">' . __('Date :', 'le-margo') . '</span> ' . esc_html(date_i18n('l j F Y', strtotime($reservation->reservation_date))) . '</div>'
        . '<div class="detail-row"><span class="detail-label">' . __('Heure :', 'le-margo') . '</span> ' . esc_html(date_i18n('H:i', strtotime($reservation->reservation_time))) . '</div>'
        . '<div class="detail-row"><span class="detail-label">' . __('Nombre de personnes :', 'le-margo') . '</span> ' . intval($reservation->people) . '</div>'
        . '</div>';
    $content .= '<p>Pour toute modification ou annulation, veuillez nous contacter directement.</p>';
    $content .= '<p>Nous avons hâte de vous accueillir !</p>';
    $content .= '<p>L\'équipe du Margo</p>';
    
    $sent = $email_manager->send_email($reservation->customer_email, $subject, $content);
    
    if ($sent) {
        // Marquer le rappel comme envoyé
        $wpdb->update(
            $table_name,
            array('reminder_sent' => 1),
            array('id' => $reservation_id)
        );
        return true;
    }
    
    return false;
}

/**
 * Annuler une réservation
 */
function le_margo_cancel_reservation($reservation_id, $nonce) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    
    // Vérifier le nonce
    if (!wp_verify_nonce($nonce, 'cancel_reservation_' . $reservation_id)) {
        return new WP_Error('invalid_nonce', 'Lien d\'annulation invalide');
    }
    
    // Récupérer la réservation
    $reservation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $reservation_id
    ));
    
    if (!$reservation) {
        return new WP_Error('not_found', 'Réservation non trouvée');
    }
    
    // Vérifier si la réservation peut être annulée (24h avant)
    $reservation_time = strtotime($reservation->reservation_date . ' ' . $reservation->reservation_time);
    if (time() > ($reservation_time - 24 * 3600)) {
        return new WP_Error('too_late', 'L\'annulation n\'est plus possible (moins de 24h avant)');
    }
    
    // Mettre à jour le statut
    $result = $wpdb->update(
        $table_name,
        array('status' => 'cancelled'),
        array('id' => $reservation_id)
    );
    
    if ($result === false) {
        return new WP_Error('db_error', 'Erreur lors de l\'annulation');
    }
    
    $email_manager = Le_Margo_Email_Manager::get_instance();

    // Envoyer un email de confirmation d'annulation
    $subject = __('Confirmation d\'annulation - Le Margo', 'le-margo');
    
    $content = '<h2>' . __('Votre réservation a été annulée', 'le-margo') . '</h2>';
    $content .= '<p>Bonjour ' . esc_html($reservation->customer_name) . ',</p>';
    $content .= '<p>Votre réservation du ' . esc_html(date_i18n('l j F Y', strtotime($reservation->reservation_date))) . ' à ' . esc_html(date_i18n('H:i', strtotime($reservation->reservation_time))) . ' pour ' . intval($reservation->people) . ' personnes a bien été annulée.</p>';
    $content .= '<p>Nous espérons vous accueillir prochainement.</p>';
    $content .= '<p>L\'équipe du Margo</p>';
    
    $email_manager->send_email($reservation->customer_email, $subject, $content);
    
    // Vérifier la liste d'attente
    le_margo_check_waiting_list($reservation->reservation_date, $reservation->reservation_time);
    
    return true;
}

/**
 * Ajouter à la liste d'attente
 */
function le_margo_add_to_waiting_list($reservation_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations';
    $waiting_list_table = $wpdb->prefix . 'reservations_waiting_list';
    
    // Récupérer la réservation
    $reservation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $reservation_id
    ));
    
    if (!$reservation) {
        return false;
    }
    
    // Ajouter à la liste d'attente
    $result = $wpdb->insert(
        $waiting_list_table,
        array(
            'reservation_id' => $reservation_id,
            'date_added' => current_time('mysql'),
            'status' => 'waiting'
        )
    );
    
    if ($result === false) {
        return false;
    }
    
    $email_manager = Le_Margo_Email_Manager::get_instance();

    // Envoyer un email au client
    $subject = __('Liste d\'attente - Le Margo', 'le-margo');
    
    $content = '<h2>' . __('Votre demande est sur liste d\'attente', 'le-margo') . '</h2>';
    $content .= '<p>Bonjour ' . esc_html($reservation->customer_name) . ',</p>';
    $content .= '<p>Votre demande de réservation du ' . esc_html(date_i18n('l j F Y', strtotime($reservation->reservation_date))) . ' à ' . esc_html(date_i18n('H:i', strtotime($reservation->reservation_time))) . ' pour ' . intval($reservation->people) . ' personnes a été placée sur liste d\'attente.</p>';
    $content .= '<p>Nous vous contacterons dès qu\'une place se libère.</p>';
    $content .= '<p>L\'équipe du Margo</p>';
    
    $email_manager->send_email($reservation->customer_email, $subject, $content);
    
    return true;
}

/**
 * Vérifier la liste d'attente après une annulation
 */
function le_margo_check_waiting_list($date, $time) {
    global $wpdb;
    $waiting_list_table = $wpdb->prefix . 'reservations_waiting_list';
    $reservations_table = $wpdb->prefix . 'reservations';
    
    // Récupérer la première réservation en attente
    $waiting_reservation = $wpdb->get_row($wpdb->prepare(
        "SELECT w.*, r.* FROM $waiting_list_table w 
        JOIN $reservations_table r ON w.reservation_id = r.id 
        WHERE r.reservation_date = %s 
        AND r.reservation_time = %s 
        AND w.status = 'waiting' 
        ORDER BY w.date_added ASC 
        LIMIT 1",
        $date,
        $time
    ));
    
    if ($waiting_reservation) {
        // Mettre à jour le statut de la réservation
        $wpdb->update(
            $reservations_table,
            array('status' => 'confirmed'),
            array('id' => $waiting_reservation->reservation_id)
        );
        
        // Mettre à jour la liste d'attente
        $wpdb->update(
            $waiting_list_table,
            array('status' => 'confirmed'),
            array('id' => $waiting_reservation->id)
        );
        
        $email_manager = Le_Margo_Email_Manager::get_instance();

        // Envoyer un email au client
        $subject = __('Réservation confirmée - Le Margo', 'le-margo');
        
        $content = '<h2>' . __('Votre réservation est confirmée', 'le-margo') . '</h2>';
        $content .= '<p>Bonjour ' . esc_html($waiting_reservation->customer_name) . ',</p>';
        $content .= '<p>Une place s\'est libérée ! Votre réservation du ' . esc_html(date_i18n('l j F Y', strtotime($waiting_reservation->reservation_date))) . ' à ' . esc_html(date_i18n('H:i', strtotime($waiting_reservation->reservation_time))) . ' pour ' . intval($waiting_reservation->people) . ' personnes est maintenant confirmée.</p>';
        $content .= '<p>Nous avons hâte de vous accueillir !</p>';
        $content .= '<p>L\'équipe du Margo</p>';
        
        $email_manager->send_email($waiting_reservation->customer_email, $subject, $content);
    }
}

/**
 * Créer la table de liste d'attente lors de l'activation du thème
 */
function le_margo_create_waiting_list_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations_waiting_list';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        reservation_id bigint(20) NOT NULL,
        date_added datetime NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'waiting',
        PRIMARY KEY  (id),
        KEY reservation_id (reservation_id),
        KEY status (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'le_margo_create_waiting_list_table'); 