<?php
/**
 * Template pour le formulaire de réservation - Version 2.0 aligné sur l'ancien style
 * Nouveau système d'horaires par jour sans distinction déjeuner/dîner
 *
 * @package Le Margo
 */
?>

<div class="reservation-form-container">
    <?php if (isset($_GET['reservation_success']) && $_GET['reservation_success'] == '1'): ?>
        <div class="reservation-response success" style="display:block;">
            <h3><?php echo esc_html__('Réservation envoyée avec succès !', 'le-margo'); ?></h3>
            <p><?php echo esc_html__('Nous avons bien reçu votre demande de réservation. Vous allez recevoir un email de confirmation dans quelques instants.', 'le-margo'); ?></p>
            <p><?php echo esc_html__('Nous vous contacterons rapidement pour confirmer votre réservation.', 'le-margo'); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['reservation_error']) && !empty($_GET['reservation_error'])): ?>
        <div class="reservation-response error" style="display:block;">
            <?php echo wp_kses_post(urldecode($_GET['reservation_error'])); ?>
        </div>
    <?php endif; ?>

    <form id="reservation-form" class="clean-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="send_reservation">
        <?php wp_nonce_field('send_reservation_nonce', 'reservation_nonce'); ?>

        <div class="form-row">
            <div class="form-field">
                <label for="date"><?php _e('Date', 'le-margo'); ?></label>
                <input type="text" id="date" name="date" class="datepicker" placeholder="<?php _e('JJ/MM/AAAA', 'le-margo'); ?>" required readonly>
            </div>
            <div class="form-field">
                <label for="time"><?php _e('Horaire', 'le-margo'); ?></label>
                <select id="time" name="time" required disabled>
                    <option value=""><?php _e('Choisissez d\'abord une date', 'le-margo'); ?></option>
                </select>
                <div class="time-availability"></div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-field">
                <label for="people"><?php _e('Personnes', 'le-margo'); ?></label>
                <select id="people" name="people" required>
                    <option value="" disabled selected><?php _e('Sélectionnez...', 'le-margo'); ?></option>
                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i > 1 ? esc_html__('personnes', 'le-margo') : esc_html__('personne', 'le-margo'); ?></option>
                    <?php endfor; ?>
                    <option value="more"><?php echo esc_html__('Plus de 10 personnes', 'le-margo'); ?></option>
                </select>
                <div class="capacity-info"></div>
                <div id="group-cta" style="display:none;margin-top:10px;background:#fff3cd;color:#856404;padding:10px 14px;border-radius:6px;font-size:0.98em;">
                    <strong><?php echo esc_html__('Pour les groupes de 4 personnes ou plus, merci de nous appeler pour garantir la meilleure expérience !', 'le-margo'); ?></strong><br>
                    <a href="tel:+33602556315" class="btn" style="margin-top:7px;min-width:160px;display:inline-block;">📞 <?php echo esc_html__('Appeler le restaurant', 'le-margo'); ?></a>
                </div>
            </div>
            <div class="form-field">
                <!-- Vide pour alignement -->
            </div>
        </div>

        <div class="form-row">
            <div class="form-field">
                <label for="customer_name"><?php _e('Nom', 'le-margo'); ?></label>
                <input type="text" id="customer_name" name="customer_name" placeholder="<?php _e('Nom et prénom', 'le-margo'); ?>" required>
            </div>
            <div class="form-field">
                <label for="customer_phone"><?php _e('Téléphone', 'le-margo'); ?></label>
                <input type="tel" id="customer_phone" name="customer_phone" placeholder="<?php _e('Pour vous contacter', 'le-margo'); ?>" required>
            </div>
        </div>

        <div class="form-field full-width">
            <label for="customer_email"><?php _e('Email', 'le-margo'); ?></label>
            <input type="email" id="customer_email" name="customer_email" placeholder="<?php _e('Pour confirmation', 'le-margo'); ?>" required>
        </div>

        <div class="form-field full-width">
            <div class="reservation-extra-info">
                <strong><?php _e('Allergies ou végétarien ?', 'le-margo'); ?></strong> — <?php _e('Merci de préciser toute allergie ou demande végétarienne dans les notes. Nous adaptons nos plats avec plaisir !', 'le-margo'); ?>
            </div>
            <label for="notes"><?php _e('Notes spéciales (optionnel)', 'le-margo'); ?></label>
            <textarea id="notes" name="notes" placeholder="<?php _e('Allergies, occasion spéciale...', 'le-margo'); ?>"></textarea>
        </div>

        <div class="form-section-divider"></div>

        <div class="form-checkboxes rgpd-section">
            <h4 class="rgpd-title"><?php _e('Préférences & Consentement', 'le-margo'); ?></h4>
            
            <label class="checkbox-label" for="accept_reminder">
                <input type="checkbox" id="accept_reminder" name="accept_reminder" value="1" checked>
                <span class="checkmark"></span>
                <?php _e('Recevoir un rappel par email avant ma réservation', 'le-margo'); ?>
            </label>
            
            <label class="checkbox-label" for="newsletter">
                <input type="checkbox" id="newsletter" name="newsletter" value="1">
                <span class="checkmark"></span>
                <?php _e('S\'inscrire à notre newsletter pour nos actualités et offres', 'le-margo'); ?>
            </label>
            
            <div class="form-section-divider-small"></div>

            <label class="checkbox-label" for="consent_data_processing">
                <input type="checkbox" id="consent_data_processing" name="consent_data_processing" value="1" required>
                <span class="checkmark"></span>
                <?php 
                $privacy_policy_url = get_privacy_policy_url();
                printf(
                    wp_kses_post(__('J\'ai lu et j\'accepte la <a href="%s" target="_blank">politique de confidentialité</a> du site.*', 'le-margo')),
                    esc_url($privacy_policy_url)
                );
                ?>
            </label>
        </div>

        <div class="form-group-hidden">
            <input type="text" name="reservation_hp" id="reservation_hp" value="">
        </div>

        <div class="gdpr-notice">
            <p><?php printf(
                wp_kses_post(__('En soumettant ce formulaire, vous acceptez notre <a href="%1$s" target="_blank">politique de confidentialité</a>. Vous pouvez à tout moment exercer vos droits RGPD via notre <a href="%2$s" target="_blank">formulaire de suppression de données</a>.', 'le-margo')),
                home_url('/politique-confidentialite'),
                home_url('/suppression-donnees')
            ); ?></p>
        </div>

        <button type="submit" class="reserve-btn" disabled><?php _e('Réservez votre table', 'le-margo'); ?></button>
        <div class="reservation-response" style="display: none;"></div>
    </form>
</div>

<script>
// Ce script peut être déplacé dans un fichier JS externe si nécessaire.
document.addEventListener('DOMContentLoaded', function() {
    const timeAvailabilityDiv = document.querySelector('.time-availability');
    const timeSelect = document.getElementById('time');

    if (timeAvailabilityDiv && timeSelect) {
        timeAvailabilityDiv.addEventListener('click', function(e) {
            const target = e.target.closest('.time-slot.selectable');
            if (target) {
                const time = target.dataset.time;
                
                // Mettre à jour la valeur du select
                timeSelect.value = time;
                
                // Déclencher l'événement 'change' pour la validation
                timeSelect.dispatchEvent(new Event('change'));
                
                // Mettre à jour la classe 'selected'
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.classList.remove('selected');
                });
                target.classList.add('selected');
            }
        });
    }
});
</script> 