<?php
/**
 * Gestion des horaires d'ouverture affichés sur le site
 *
 * @package Le_Margo
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajoute la page de réglages dans le menu "Apparence"
 */
function le_margo_add_opening_hours_menu() {
    add_theme_page(
        __('Horaires d\'ouverture', 'le-margo'),
        __('Horaires d\'ouverture', 'le-margo'),
        'manage_options',
        'le-margo-opening-hours',
        'le_margo_opening_hours_page_html'
    );
}
add_action('admin_menu', 'le_margo_add_opening_hours_menu');

/**
 * Enregistre le paramètre pour les horaires
 */
function le_margo_register_opening_hours_settings() {
    register_setting('le_margo_opening_hours_settings', 'le_margo_opening_hours', [
        'type' => 'array',
        'default' => [],
        'sanitize_callback' => 'le_margo_sanitize_opening_hours'
    ]);
}
add_action('admin_init', 'le_margo_register_opening_hours_settings');

/**
 * Nettoie les données envoyées par le formulaire
 */
function le_margo_sanitize_opening_hours($input) {
    $output = [];
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    foreach ($days as $day) {
        if (isset($input[$day])) {
            $output[$day] = sanitize_text_field($input[$day]);
        }
    }
    return $output;
}

/**
 * Affiche le contenu HTML de la page de réglages
 */
function le_margo_opening_hours_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap le-margo-admin">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p><?php echo esc_html__('Indiquez ici les horaires d\'ouverture tels qu\'ils doivent apparaître sur le site. Vous pouvez utiliser du texte libre (ex: "12h-14h & 19h-22h" ou "Fermé").', 'le-margo'); ?></p>
        
        <div class="le-margo-admin-card">
            <form action="options.php" method="post">
                <?php
                settings_fields('le_margo_opening_hours_settings');
                $options = get_option('le_margo_opening_hours', []);
                $days = [
                    'monday'    => __('Lundi', 'le-margo'),
                    'tuesday'   => __('Mardi', 'le-margo'),
                    'wednesday' => __('Mercredi', 'le-margo'),
                    'thursday'  => __('Jeudi', 'le-margo'),
                    'friday'    => __('Vendredi', 'le-margo'),
                    'saturday'  => __('Samedi', 'le-margo'),
                    'sunday'    => __('Dimanche', 'le-margo'),
                ];
                ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <?php foreach ($days as $day_key => $day_label) : ?>
                            <tr>
                                <th scope="row">
                                    <label for="le_margo_opening_hours_<?php echo esc_attr($day_key); ?>"><?php echo esc_html($day_label); ?></label>
                                </th>
                                <td>
                                    <input type="text"
                                           id="le_margo_opening_hours_<?php echo esc_attr($day_key); ?>"
                                           name="le_margo_opening_hours[<?php echo esc_attr($day_key); ?>]"
                                           value="<?php echo esc_attr($options[$day_key] ?? ''); ?>"
                                           class="regular-text">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php submit_button(__('Enregistrer les horaires', 'le-margo')); ?>
            </form>
        </div>
    </div>
    <?php
} 