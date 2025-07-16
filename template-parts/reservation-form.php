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
                <label for="date">Date</label>
                <input type="text" id="date" name="date" class="datepicker" placeholder="JJ/MM/AAAA" required readonly>
            </div>
            <div class="form-field">
                <label for="time">Horaire</label>
                <select id="time" name="time" required disabled>
                    <option value="">Choisissez d'abord une date</option>
                </select>
                <div class="time-availability"></div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-field">
                <label for="people">Personnes</label>
                <select id="people" name="people" required>
                    <option value="" disabled selected>Sélectionnez...</option>
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
                <label for="customer_name">Nom</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Nom et prénom" required>
            </div>
            <div class="form-field">
                <label for="customer_phone">Téléphone</label>
                <input type="tel" id="customer_phone" name="customer_phone" placeholder="Pour vous contacter" required>
            </div>
        </div>

        <div class="form-field full-width">
            <label for="customer_email">Email</label>
            <input type="email" id="customer_email" name="customer_email" placeholder="Pour confirmation" required>
        </div>

        <div class="form-field full-width">
            <div class="reservation-extra-info">
                <strong>Allergies ou végétarien ?</strong> — Merci de préciser toute allergie ou demande végétarienne dans les notes. Nous adaptons nos plats avec plaisir !
            </div>
            <label for="notes">Notes spéciales (optionnel)</label>
            <textarea id="notes" name="notes" placeholder="Allergies, occasion spéciale..."></textarea>
        </div>

        <div class="form-checkboxes">
            <label class="checkbox-label">
                <input type="checkbox" id="consent_data_processing" name="consent_data_processing" value="1" required>
                <span class="checkmark"></span>
                J'accepte que mes données personnelles soient collectées et traitées conformément à la politique de confidentialité. <span class="required">*</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" id="consent_data_storage" name="consent_data_storage" value="1" required>
                <span class="checkmark"></span>
                Je consens à ce que mes informations soient enregistrées pour le programme de fidélité du restaurant. <span class="required">*</span>
            </label>
            <p class="consent-info">Nous enregistrons votre historique de visites pour identifier nos clients fidèles (statut VIP après 5 visites).</p>
            <label class="checkbox-label">
                <input type="checkbox" id="accept_reminder" name="accept_reminder" value="1" checked>
                <span class="checkmark"></span>
                Recevoir un rappel par email 1h30 avant ma réservation
            </label>
            <label class="checkbox-label">
                <input type="checkbox" id="newsletter" name="newsletter" value="1">
                <span class="checkmark"></span>
                S'inscrire à notre newsletter pour recevoir nos actualités et offres spéciales
            </label>
        </div>

        <div class="form-group-hidden">
            <input type="text" name="reservation_hp" id="reservation_hp" value="">
        </div>

        <div class="gdpr-notice">
            <p>En soumettant ce formulaire, vous acceptez notre <a href="/politique-confidentialite" target="_blank">politique de confidentialité</a>. Vous pouvez à tout moment exercer vos droits RGPD via notre <a href="/suppression-donnees" target="_blank">formulaire de suppression de données</a>.</p>
        </div>

        <button type="submit" class="reserve-btn" disabled>Réservez votre table</button>
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