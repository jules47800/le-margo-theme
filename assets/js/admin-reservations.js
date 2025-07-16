/**
 * JavaScript pour l'interface d'administration des réservations - Le Margo
 * Gestion des plages horaires multiples et interface dynamique
 */

(function($) {
    'use strict';

    // Variables globales
    let rangeCounter = {};

    /**
     * Initialisation du script
     */
    function init() {
        setupToggleSwitches();
        setupTimeRanges();
        setupFormValidation();
        // setupRealTimePreview(); // Commenté pour permettre le surbooking depuis l'admin
        initStatWidgets();
        initEmailTest();
        initSmoothScroll();
        handleActionScrolling();
    }

    /**
     * Configuration des switches d'ouverture/fermeture
     */
    function setupToggleSwitches() {
        $('.day-toggle input[type="checkbox"]').on('change', function() {
            const daySchedule = $(this).closest('.schedule-day').find('.day-schedule');
            const isOpen = $(this).is(':checked');
            
            if (isOpen) {
                daySchedule.removeClass('day-closed').addClass('day-open');
            } else {
                daySchedule.removeClass('day-open').addClass('day-closed');
            }
            
            updateSlotsPreview($(this).closest('.schedule-day'));
        });
    }

    /**
     * Configuration des plages horaires
     */
    function setupTimeRanges() {
        // Initialiser le compteur pour chaque jour
        $('.schedule-day').each(function() {
            const dayKey = $(this).data('day');
            rangeCounter[dayKey] = $(this).find('.time-range-row').length;
        });

        // Gestion de l'ajout de plages
        $(document).on('click', '.add-range', function() {
            const dayKey = $(this).data('day');
            addTimeRange(dayKey);
        });

        // Gestion de la suppression de plages
        $(document).on('click', '.remove-range', function(e) {
            e.preventDefault();
            const rangeRow = $(this).closest('.time-range-row');
            const dayKey = $(this).data('day');

            rangeRow.fadeOut(300, function() {
                $(this).remove();
                reindexTimeRanges(dayKey);
                
                const dayElement = $(`.schedule-day[data-day="${dayKey}"]`);
                updateSlotsPreview(dayElement);
            });
        });

        // Mise à jour en temps réel des créneaux
        $(document).on('change', 'input[type="time"], select[name*="slot_interval"]', function() {
            const dayElement = $(this).closest('.schedule-day');
            updateSlotsPreview(dayElement);
        });
    }

    /**
     * Ajouter une nouvelle plage horaire
     */
    function addTimeRange(dayKey) {
        const container = $(`.time-ranges-container[data-day="${dayKey}"]`);
        const newIndex = rangeCounter[dayKey]++;
        
        const newRange = `
            <div class="time-range-row" data-index="${newIndex}">
                <div class="time-inputs">
                    <div class="time-input">
                        <label>Début</label>
                        <input type="time" name="le_margo_daily_schedule[${dayKey}][time_ranges][${newIndex}][start]" value="12:00">
                    </div>
                    <div class="time-input">
                        <label>Fin</label>
                        <input type="time" name="le_margo_daily_schedule[${dayKey}][time_ranges][${newIndex}][end]" value="14:00">
                    </div>
                </div>
                <button type="button" class="remove-range" data-day="${dayKey}" data-index="${newIndex}">Supprimer</button>
            </div>
        `;
        
        container.append(newRange);
        
        // Mettre à jour l'aperçu des créneaux
        const dayElement = container.closest('.schedule-day');
        updateSlotsPreview(dayElement);
        
        // Animation d'apparition
        container.find('.time-range-row:last').hide().fadeIn(300);
    }

    /**
     * Supprimer une plage horaire
     */
    /*
    function removeTimeRange(dayKey, index) {
        const rangeRow = $(`.time-range-row[data-index="${index}"]`);
        
        // Animation de suppression
        rangeRow.fadeOut(300, function() {
            $(this).remove();
            
            // Réindexer les plages restantes
            reindexTimeRanges(dayKey);
            
            // Mettre à jour l'aperçu des créneaux
            const dayElement = rangeRow.closest('.schedule-day');
            updateSlotsPreview(dayElement);
        });
    }
    */

    /**
     * Réindexer les plages horaires après suppression
     */
    function reindexTimeRanges(dayKey) {
        const container = $(`.time-ranges-container[data-day="${dayKey}"]`);
        let newIndex = 0;
        
        container.find('.time-range-row').each(function() {
            $(this).attr('data-index', newIndex);
            $(this).find('input[name*="[start]"]').attr('name', `le_margo_daily_schedule[${dayKey}][time_ranges][${newIndex}][start]`);
            $(this).find('input[name*="[end]"]').attr('name', `le_margo_daily_schedule[${dayKey}][time_ranges][${newIndex}][end]`);
            $(this).find('.remove-range').attr('data-index', newIndex);
            newIndex++;
        });
        
        rangeCounter[dayKey] = newIndex;
    }

    /**
     * Mettre à jour l'aperçu des créneaux en temps réel
     */
    function updateSlotsPreview(dayElement) {
        const isOpen = dayElement.find('.day-toggle input[type="checkbox"]').is(':checked');
        const slotsList = dayElement.find('.slots-list');
        
        if (!isOpen) {
            slotsList.html('<span class="day-closed-text">Jour fermé</span>');
            return;
        }
        
        // Collecter les plages horaires
        const timeRanges = [];
        dayElement.find('.time-range-row').each(function() {
            const start = $(this).find('input[name*="[start]"]').val();
            const end = $(this).find('input[name*="[end]"]').val();
            if (start && end) {
                timeRanges.push({ start, end });
            }
        });
        
        // Collecter l'intervalle
        const interval = parseInt(dayElement.find('select[name*="slot_interval"]').val()) || 30;
        
        // Générer les créneaux
        const allSlots = [];
        timeRanges.forEach(range => {
            const slots = generateTimeSlots(range.start, range.end, interval);
            allSlots.push(...slots);
        });
        
        // Trier les créneaux
        allSlots.sort();
        
        // Afficher l'aperçu
        if (allSlots.length > 0) {
            const exampleSlots = allSlots.slice(0, 5);
            const preview = `
                <span class="slot-count">${allSlots.length} créneaux</span>
                <div class="slots-example">
                    ${exampleSlots.join(', ')}${allSlots.length > 5 ? '...' : ''}
                </div>
            `;
            slotsList.html(preview);
        } else {
            slotsList.html('<span class="day-closed-text">Aucun créneau configuré</span>');
        }
    }

    /**
     * Générer les créneaux horaires (version JavaScript)
     */
    function generateTimeSlots(startTime, endTime, intervalMinutes) {
        const slots = [];
        const start = new Date(`2000-01-01T${startTime}:00`);
        const end = new Date(`2000-01-01T${endTime}:00`);
        
        let current = new Date(start);
        
        while (current < end) {
            slots.push(current.toTimeString().slice(0, 5));
            current.setMinutes(current.getMinutes() + intervalMinutes);
        }
        
        return slots;
    }

    /**
     * Configuration de la validation des formulaires
     */
    function setupFormValidation() {
        $('form').on('submit', function(e) {
            let isValid = true;
            const errors = [];
            
            // Vérifier que chaque jour ouvert a au moins une plage horaire
            $('.schedule-day').each(function() {
                const isOpen = $(this).find('.day-toggle input[type="checkbox"]').is(':checked');
                const hasRanges = $(this).find('.time-range-row').length > 0;
                
                if (isOpen && !hasRanges) {
                    const dayName = $(this).find('.day-header h3').text();
                    errors.push(`${dayName} : Veuillez ajouter au moins une plage horaire.`);
                    isValid = false;
                }
            });
            
            // Vérifier la cohérence des heures
            $('.time-range-row').each(function() {
                const start = $(this).find('input[name*="[start]"]').val();
                const end = $(this).find('input[name*="[end]"]').val();
                
                if (start && end && start >= end) {
                    errors.push('L\'heure de fin doit être postérieure à l\'heure de début.');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Erreurs de validation :\n' + errors.join('\n'));
            }
        });
    }

    /**
     * Configuration de l'aperçu en temps réel
     */
    function setupRealTimePreview() {
        // Mise à jour automatique lors des changements
        $(document).on('input change', '.time-range-row input, .slot-interval select', function() {
            const dayElement = $(this).closest('.schedule-day');
            updateSlotsPreview(dayElement);
        });
    }

    // ========================================
    // FONCTIONS EXISTANTES (conservées)
    // ========================================

    // Gestion des widgets statistiques cliquables
    function initStatWidgets() {
        $('.stat-box-clickable').on('click', function(e) {
            var $this = $(this);
            var hasItems = $this.attr('data-has-items');
            var href = $this.attr('href');
            
            console.log('Widget cliqué:', {
                hasItems: hasItems,
                href: href,
                element: $this
            });
            
            // Si le widget n'a pas d'éléments, empêcher la navigation
            if (hasItems === 'false') {
                e.preventDefault();
                return false;
            }
            
            // Pour les widgets avec des éléments, ajouter un feedback visuel
            if (hasItems === 'true') {
                $this.addClass('stat-loading');
                
                // Si c'est un lien interne, laisser le comportement normal
                if (href && href.indexOf('le-margo-reservations') !== -1) {
                    return true;
                }
            }
        });
    }
    
    // Gestion du test d'email
    function initEmailTest() {
        $('#test-email-btn').on('click', function() {
            var $btn = $(this);
            var $result = $('#test-email-result');
            
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Test en cours...');
            $result.html('<div class="notice notice-info inline"><p>⏳ Test d\'envoi d\'email en cours...</p></div>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'le_margo_test_email'
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<div class="notice notice-success inline"><p>✅ ' + response.data + '</p></div>');
                    } else {
                        $result.html('<div class="notice notice-error inline"><p>❌ ' + response.data + '</p></div>');
                    }
                },
                error: function() {
                    $result.html('<div class="notice notice-error inline"><p>❌ Erreur lors du test AJAX</p></div>');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-email-alt"></span> Tester l\'envoi d\'emails');
                }
            });
        });
    }
    
    // Gestion du smooth scroll
    function initSmoothScroll() {
        $('a[href*="#"]').click(function(e) {
            if (this.hash !== '') {
                var target = $(this.hash);
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 50
                    }, 600);
                }
            }
        });
    }
    
    // Auto-scroll après actions
    function handleActionScrolling() {
        // Si une action vient d'être effectuée, scroll vers la liste des réservations
        if (window.location.search.includes('action=') && window.location.search.includes('id=')) {
            setTimeout(function() {
                var $target = $('#reservations-list');
                if ($target.length) {
                    $('html, body').animate({
                        scrollTop: $target.offset().top - 80
                    }, 800);
                }
            }, 500);
        }
        
        // Si le filtre pending est actif, mettre en évidence le widget et scroll vers la liste
        if (window.location.search.includes('status_filter=pending')) {
            console.log('Filtre pending actif - mise en évidence du widget');
            $('.stat-box-clickable').eq(1).addClass('stat-active'); // Le widget "En attente" est le 2ème
            setTimeout(function() {
                var $target = $('#reservations-list');
                if ($target.length) {
                    $('html, body').animate({
                        scrollTop: $target.offset().top - 80
                    }, 600);
                }
            }, 300);
        }
        
        // Si on affiche les réservations d'aujourd'hui uniquement
        var urlParams = new URLSearchParams(window.location.search);
        var today = new Date().toISOString().split('T')[0];
        if (urlParams.get('view') === 'day' && urlParams.get('date_filter') === today) {
            console.log('Vue jour + date aujourd\'hui - mise en évidence du widget Aujourd\'hui');
            $('.stat-box-clickable').eq(0).addClass('stat-active'); // Le widget "Aujourd\'hui" est le 1er
        }
    }

    /**
     * Fonctions utilitaires
     */
    function showNotification(message, type = 'info') {
        const notification = $(`
            <div class="notice notice-${type} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Ignorer cette notification.</span>
                </button>
            </div>
        `);
        
        $('.wrap').prepend(notification);
        
        // Auto-dismiss après 5 secondes
        setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Gestion des erreurs AJAX
     */
    function handleAjaxError(xhr, status, error) {
        console.error('Erreur AJAX:', error);
        showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
    }

    /**
     * Initialisation au chargement du DOM
     */
    $(document).ready(function() {
        init();
        
        // Mise à jour initiale des aperçus
        $('.schedule-day').each(function() {
            updateSlotsPreview($(this));
        });

        // NOUVEAU : Initialisation du calendrier pour les vacances (Version robuste)
        if (typeof flatpickr !== 'undefined') {
            const holidaysInput = document.getElementById('le_margo_holiday_dates');
            const holidaysCalendarContainer = document.getElementById('le_margo_holiday_dates_calendar');

            if (holidaysInput && holidaysCalendarContainer) {
                flatpickr(holidaysCalendarContainer, {
                    mode: "multiple",
                    dateFormat: "Y-m-d", // Format de sauvegarde standard
                    locale: 'fr',
                    minDate: "today",
                    showMonths: 2,
                    inline: true, // Afficher le calendrier directement
                    // Pré-sélectionner les dates déjà enregistrées
                    defaultDate: holidaysInput.value.split(',').filter(Boolean),
                    // Mettre à jour le champ caché quand une date est modifiée
                    onChange: function(selectedDates, dateStr, instance) {
                        holidaysInput.value = selectedDates
                            .map(date => instance.formatDate(date, "Y-m-d"))
                            .join(',');
                    }
                });
            }
        }
        
        // Debugging console pour les développeurs
        console.log('Admin réservations Le Margo initialisé avec système d\'horaires avancé');
    });

    /**
     * Export des fonctions pour utilisation externe
     */
    window.LeMargoAdmin = {
        addTimeRange,
        removeTimeRange,
        updateSlotsPreview,
        showNotification
    };

})(jQuery); 