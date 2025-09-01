/**
 * JavaScript pour le formulaire de réservation - Version 2.1 (Simplifiée)
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const reservationForm = document.getElementById('reservation-form');
        if (!reservationForm) return;

        const timeSelect = document.getElementById('time');
        const dateInput = document.getElementById('date');
        const peopleSelect = document.getElementById('people');
        const submitButton = reservationForm.querySelector('button[type="submit"]');
        const timeAvailabilityDiv = document.querySelector('.time-availability');
        const customerPhone = document.getElementById('customer_phone');
        const customerEmail = document.getElementById('customer_email');
        const customerName = document.getElementById('customer_name');
        
        let selectedTimeSlot = null; // Mémoriser le créneau sélectionné

        // Désactiver le bouton de soumission au départ
        if (submitButton) {
            submitButton.disabled = true;
        }
        
        // --- Fonctions utilitaires ---
        function generateTimeSlots(startTime, endTime, interval = 15) {
            const slots = [];
            const start = new Date(`2000-01-01T${startTime}:00`);
            const end = new Date(`2000-01-01T${endTime}:00`);
            let current = new Date(start);
            while (current < end) {
                slots.push(current.toTimeString().slice(0, 5));
                current.setMinutes(current.getMinutes() + interval);
            }
            return slots;
        }

        function fetchReservations(date) {
            return new Promise((resolve) => {
                const apiUrl = `${le_margo_params.ajax_url}?action=le_margo_get_availability&date=${encodeURIComponent(date)}&_=${new Date().getTime()}`;
                fetch(apiUrl)
                    .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok.'))
                    .then(data => {
                        if (data.success && data.data) {
                            resolve(data.data);
                        } else {
                            resolve({ time_slots: {}, capacity_per_slot: 4 });
                        }
                    })
                    .catch(() => resolve({ time_slots: {}, capacity_per_slot: 4 }));
            });
        }

        // --- Logique du formulaire ---
        function updateAvailableTimes() {
            const selectedDateInput = dateInput.value;
            const selectedPeople = parseInt(peopleSelect.value) || 1;

            if (!selectedDateInput) {
                timeSelect.disabled = true;
                timeAvailabilityDiv.innerHTML = `<div class="info-state">${reservation_i18n.selectDate}</div>`;
                validateForm();
                return;
            }

            const dateParts = selectedDateInput.split('/');
            const selectedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
            const dateObj = new Date(selectedDate);
            const dayKey = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'][dateObj.getDay()];
            const daySchedule = (le_margo_params.daily_schedule || {})[dayKey];

            if (!daySchedule || !daySchedule.open) {
                timeSelect.disabled = true;
                timeAvailabilityDiv.innerHTML = `<div class="info-state">${reservation_i18n.restaurantClosed}</div>`;
                validateForm();
                return;
            }
            
            timeAvailabilityDiv.innerHTML = `<div class="loading-state">${reservation_i18n.checkingAvailability}</div>`;
            timeSelect.disabled = true;

            fetchReservations(selectedDate).then(reservationsForDate => {
                // CORRECTION : Utiliser les créneaux filtrés par le serveur
                const allSlots = reservationsForDate.available_slots.map(slot => slot.time);
                
                let availableTimesHtml = '';
                let hasAvailableSlots = false;
                
                timeSelect.innerHTML = `<option value="" disabled selected>${reservation_i18n.selectTime}</option>`;

                allSlots.forEach(timeSlot => {
                    const reservedSeats = reservationsForDate.time_slots[timeSlot] || 0;
                    const availableSeats = reservationsForDate.capacity_per_slot - reservedSeats;
                    const isSelectable = availableSeats >= selectedPeople;
                    
                    if (isSelectable) {
                        hasAvailableSlots = true;
                        const option = new Option(`${timeSlot} (${availableSeats} places)`, timeSlot);
                        timeSelect.appendChild(option);
                    }
                    
                    availableTimesHtml += `
                        <div class="time-slot ${isSelectable ? 'selectable' : 'not-selectable'}" data-time="${timeSlot}">
                            <span class="time-value">${timeSlot}</span>
                            <span class="availability-badge">${isSelectable ? `${availableSeats} ${reservation_i18n.available}` : reservation_i18n.full}</span>
                        </div>
                    `;
                });

                if (hasAvailableSlots) {
                    timeAvailabilityDiv.innerHTML = availableTimesHtml;
                    timeSelect.disabled = false;
                    
                    // Réappliquer la sélection mémorisée si elle est toujours valide
                    if (selectedTimeSlot && timeSelect.querySelector(`option[value="${selectedTimeSlot}"]`)) {
                        timeSelect.value = selectedTimeSlot;
                        const activeSlot = timeAvailabilityDiv.querySelector(`[data-time="${selectedTimeSlot}"]`);
                        if(activeSlot) activeSlot.classList.add('selected');
                    }
                } else {
                    const restaurantPhone = le_margo_params.restaurant_phone || '05 53 00 00 00';
                    timeAvailabilityDiv.innerHTML = `<div class="info-state">
                        <p>${reservation_i18n.noOnlineBookingForGroup.replace('%d', selectedPeople)}</p>
                        <p>${reservation_i18n.callUs.replace('%s', `<a href="tel:${restaurantPhone}" class="phone-link">${restaurantPhone}</a>`)}</p>
                    </div>`;
                    timeSelect.disabled = true;
                }
                
                validateForm();
            });
        }

        // Validation du numéro de téléphone
        function validatePhone(phone) {
            // Nettoyer le numéro en gardant seulement les chiffres et le +
            const cleanPhone = phone.replace(/[^\d+]/g, '');
            
            // Validation plus souple pour les numéros internationaux
            // Accepte : +33 6 12 34 56 78, +1 555 123 4567, 06 12 34 56 78, etc.
            const phoneRegex = /^(\+?\d{1,4}[\s-]?)?\(?\d{1,4}\)?[\s-]?\d{1,4}[\s-]?\d{1,4}[\s-]?\d{1,9}$/;
            
            // Vérifier que le numéro a au moins 8 chiffres (minimum international)
            const digitsOnly = cleanPhone.replace(/[^\d]/g, '');
            return phoneRegex.test(phone) && digitsOnly.length >= 8;
        }

        // Validation de l'email
        function validateEmail(email) {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }

        // Validation du nom
        function validateName(name) {
            return name.length >= 2;
        }

        // Fonction pour mettre à jour les messages d'erreur
        function showFieldError(field, message) {
            let errorDiv = field.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('field-error')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'field-error';
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
            errorDiv.textContent = message;
            field.classList.add('error');
        }

        function clearFieldError(field) {
            const errorDiv = field.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('field-error')) {
                errorDiv.remove();
            }
            field.classList.remove('error');
        }

        // Validation en temps réel du téléphone
        customerPhone.addEventListener('input', function() {
            if (validatePhone(this.value)) {
                clearFieldError(this);
            } else {
                showFieldError(this, reservation_i18n.invalidPhone);
            }
            validateForm();
        });

        // Validation en temps réel de l'email
        customerEmail.addEventListener('input', function() {
            if (validateEmail(this.value)) {
                clearFieldError(this);
            } else {
                showFieldError(this, reservation_i18n.invalidEmail);
            }
            validateForm();
        });

        // Validation en temps réel du nom
        customerName.addEventListener('input', function() {
            if (validateName(this.value)) {
                clearFieldError(this);
            } else {
                showFieldError(this, reservation_i18n.invalidName);
            }
            validateForm();
        });

        // Validation du formulaire
        function validateForm() {
            if (!submitButton) return;

            const requiredFields = reservationForm.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (field.type === 'checkbox') {
                    if (!field.checked) isValid = false;
                } else {
                    if (!field.value || field.disabled) isValid = false;

                    // Validations spécifiques
                    switch(field.id) {
                        case 'customer_phone':
                            if (!validatePhone(field.value)) isValid = false;
                            break;
                        case 'customer_email':
                            if (!validateEmail(field.value)) isValid = false;
                            break;
                        case 'customer_name':
                            if (!validateName(field.value)) isValid = false;
                            break;
                    }
                }
            });

            submitButton.disabled = !isValid;
        }

        // Ajouter du style pour les erreurs
        const style = document.createElement('style');
        style.textContent = `
            .field-error {
                color: #dc3545;
                font-size: 0.875em;
                margin-top: 4px;
            }
            input.error {
                border-color: #dc3545;
            }
            input.error:focus {
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            }
        `;
        document.head.appendChild(style);

        // --- Événements ---
        dateInput.addEventListener('change', updateAvailableTimes);
        peopleSelect.addEventListener('change', updateAvailableTimes);

        timeSelect.addEventListener('change', () => {
            selectedTimeSlot = timeSelect.value;
            // Mettre en surbrillance le créneau cliquable correspondant
            document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
            const activeSlot = timeAvailabilityDiv.querySelector(`[data-time="${selectedTimeSlot}"]`);
            if(activeSlot) activeSlot.classList.add('selected');
            validateForm();
        });

        // Rendre les créneaux horaires cliquables
        timeAvailabilityDiv.addEventListener('click', function(e) {
            const target = e.target.closest('.time-slot.selectable');
            if (target) {
                const time = target.dataset.time;
                timeSelect.value = time;
                // Déclencher l'événement 'change' pour que la validation se fasse
                timeSelect.dispatchEvent(new Event('change'));
            }
        });
        
        // Validation initiale
        reservationForm.addEventListener('input', validateForm);
        validateForm();
        
        // Initialisation de Flatpickr
        if (typeof flatpickr !== 'undefined') {
            const holidayString = le_margo_params.holiday_dates || '';
            const holidayDates = holidayString ? holidayString.split(',').map(d => d.trim()).filter(Boolean) : [];

            const maxDate = new Date();
            maxDate.setMonth(maxDate.getMonth() + 1);

            flatpickr(dateInput, {
                dateFormat: 'd/m/Y',
                minDate: 'today',
                maxDate: maxDate,
                locale: reservation_i18n.currentLocale,
                disable: [
                    function(date) {
                        // Règle 1: Jours de fermeture hebdomadaire
                        const dayKey = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'][date.getDay()];
                        const schedule = (le_margo_params.daily_schedule || {})[dayKey];
                        if (!schedule || !schedule.open) {
                            return true; // Désactiver si fermé
                        }

                        // Règle 2: Jours de vacances
                        const dateString = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                        if (holidayDates.includes(dateString)) {
                            return true; // Désactiver si c'est un jour de vacances
                        }

                        return false; // Garder la date activée
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    selectedTimeSlot = null;
                    timeSelect.value = '';
                    updateAvailableTimes();
                }
            });
        }
    });
})(); 