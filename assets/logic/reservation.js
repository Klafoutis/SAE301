document.addEventListener('DOMContentLoaded', () => {
    console.log('Système de réservation chargé 💅');

    // --- 1. VARIABLES & MAPPING ---
    let currentStep = 1;

    // Dictionnaire pour lier le numéro de l'étape à l'ID HTML exact
    const stepMapping = {
        1: 'step-services',
        2: 'step-planning',
        3: 'step-info',
        4: 'step-payment'
    };


    // --- 2. FONCTIONS DE VALIDATION (Celle qui manquait !) ---
    function validateStep(step) {
        // Validation Étape 1 : Au moins un service coché ?
        if (step === 1) {
            // On utilise la classe CSS spécifique
            const checked = document.querySelectorAll('.service-checkbox:checked');
            if (checked.length === 0) {
                alert("Veuillez sélectionner au moins une prestation.");
                return false;
            }
        }

        // Validation Étape 2 : Date remplie ?
        if (step === 2) {
            // On cherche l'input date (par ID Symfony ou type générique)
            const dateInput = document.getElementById('reservation_dateRdv') || document.querySelector('input[type="date"]');
            if (dateInput && !dateInput.value) {
                alert("Veuillez choisir une date.");
                return false;
            }
        }

        return true; // Tout est bon
    }


    // --- 3. FONCTIONS DE NAVIGATION (Globales pour onclick) ---

    // Fonction globale accessible depuis le HTML
    window.nextStep = function(stepTarget) {
        // On valide l'étape PRÉCÉDENTE (stepTarget - 1) avant d'avancer
        if (!validateStep(stepTarget - 1)) {
            return; // Stop si pas valide
        }
        goToStep(stepTarget);
    };

    window.prevStep = function(stepTarget) {
        goToStep(stepTarget);
    };

    // Fonction interne pour gérer l'affichage
    function goToStep(stepNumber) {
        // Masquer toutes les étapes (fieldset)
        document.querySelectorAll('fieldset').forEach(el => el.style.display = 'none');

        // Afficher la cible
        const targetId = stepMapping[stepNumber];
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            targetElement.style.display = 'block';
            currentStep = stepNumber;

            // Remonter en haut de page
            window.scrollTo(0, 0);

            // Si on arrive au récapitulatif, on le met à jour
            if (stepNumber === 4) {
                updateFinalSummary();
            }
        } else {
            console.error("Erreur: Impossible de trouver l'étape avec l'ID " + targetId);
        }
    }


    // --- 4. CALCULATEUR DE PRIX (Total) ---
    const servicesInputs = document.querySelectorAll('.service-checkbox');
    const deposeInput = document.querySelector('.js-depose');

    function calculateTotal() {
        let totalPrix = 0;
        let totalMinutes = 0;

        // Somme des services
        servicesInputs.forEach(input => {
            if (input.checked) {
                totalPrix += parseFloat(input.dataset.prix || 0);
                totalMinutes += parseInt(input.dataset.duree || 0);
            }
        });

        // Ajout dépose
        if (deposeInput && deposeInput.checked) {
            totalPrix += parseFloat(deposeInput.dataset.prix || 0);
            totalMinutes += parseInt(deposeInput.dataset.duree || 0);
        }

        // Formatage Heures/Minutes
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        let timeString = '';
        if (hours > 0) {
            timeString += hours + 'h';
            if (minutes > 0) timeString += minutes.toString().padStart(2, '0');
        } else {
            timeString = minutes + 'min';
        }

        // Mise à jour DOM
        const displayTime = document.getElementById('total-time');
        const displayPrice = document.getElementById('total-price');

        if(displayTime) displayTime.textContent = timeString || "0min";
        if(displayPrice) displayPrice.textContent = totalPrix + "€";
    }

    // Écouteurs pour le calcul auto
    servicesInputs.forEach(input => input.addEventListener('change', calculateTotal));
    if(deposeInput) deposeInput.addEventListener('change', calculateTotal);


    // --- 5. RÉCAPITULATIF FINAL (Étape 4) ---
    function updateFinalSummary() {
        const summaryBox = document.getElementById('final-summary');
        if (!summaryBox) return;

        // 1. Récupération des totaux (Prix / Durée)
        const timeText = document.getElementById('total-time').textContent;
        const priceText = document.getElementById('total-price').textContent;

        // 2. Récupération de la DATE (Champ Symfony)
        const dateInput = document.getElementById('reservation_dateRdv') || document.querySelector('input[type="date"]');
        let dateVal = "Date inconnue";

        // Petite astuce pour afficher la date joliment (format français) si possible
        if (dateInput && dateInput.value) {
            const d = new Date(dateInput.value);
            dateVal = d.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
        }

        // 3. Récupération de l'HEURE (Notre select manuel) -- C'est ici l'ajout !
        const timeSelect = document.getElementById('time_slot');
        const timeVal = timeSelect ? timeSelect.value : "Heure non définie";

        // 4. Construction de l'affichage
        summaryBox.innerHTML = `
            <div class="text-center border p-3 rounded bg-white">
                <h5 class="text-secondary mb-3">Votre rendez-vous :</h5>

                <p class="fs-4 mb-1 text-dark">
                    📅 <strong style="text-transform:capitalize;">${dateVal}</strong>
                </p>
                <p class="fs-3 text-primary fw-bold mb-3">
                    🕑 à ${timeVal}
                </p>

                <hr>

                <div class="d-flex justify-content-center justify-content-around">
                    <div>
                        <small class="text-muted">Prix total</small><br>
                        <strong>${priceText}</strong>
                    </div>
                    <div>
                        <small class="text-muted">Durée</small><br>
                        <strong>${timeText}</strong>
                    </div>
                </div>
            </div>
        `;
    }

    // --- 6. GESTION PAIEMENT (CB vs Sur Place) ---
    // Cette fonction est appelée par les boutons radio avec onchange="togglePayment(...)"
    // Mais on peut aussi la définir globalement pour être sûr
    window.togglePayment = function(isOnline) {
        const block = document.getElementById('card-details-block');
        if(!block) return;

        block.style.display = isOnline ? 'block' : 'none';

        const inputs = block.querySelectorAll('input');
        inputs.forEach(i => {
            if(isOnline) i.setAttribute('required', 'required');
            else i.removeAttribute('required');
        });
    };

    // --- 7. GESTION DYNAMIQUE DU CALENDRIER ---

    const dateInput = document.getElementById('reservation_dateRdv') || document.querySelector('input[type="date"]');
    const timeSelect = document.getElementById('time_slot');

    if (dateInput && timeSelect) {
        dateInput.addEventListener('change', function() {
            const dateVal = new Date(this.value);
            const dayOfWeek = dateVal.getDay(); // 0 = Dimanche, 1 = Lundi, etc.

            // Réinitialiser le select
            timeSelect.innerHTML = '';
            timeSelect.disabled = false;

            // CONFIGURATION DES HORAIRES (Doit correspondre à votre Controller PHP)
            let startHour = 9;
            let endHour = 18;

            // Règle 1 : Dimanche Fermé
            if (dayOfWeek === 0) {
                const option = document.createElement('option');
                option.text = "Fermé le dimanche";
                timeSelect.add(option);
                timeSelect.disabled = true; // On bloque
                return; // On arrête là
            }

            // Règle 2 : Samedi (Journée courte)
            if (dayOfWeek === 6) {
                endHour = 14;
            } else if (dayOfWeek === 5) { // Vendredi (Nocturne)
                endHour = 19;
            }

            // GÉNÉRATION DES CRÉNEAUX (Toutes les 30 min)
            // On s'arrête un peu avant la fin pour laisser le temps de la prestation
            // Pour faire simple ici, on génère tout, le backend vérifiera le surbooking.

            const optionDefaut = document.createElement('option');
            optionDefaut.text = "-- Choisissez un horaire --";
            optionDefaut.value = "";
            timeSelect.add(optionDefaut);

            for (let h = startHour; h < endHour; h++) {
                // Créneau pile (ex: 09:00)
                addOption(h, '00');
                // Créneau demi (ex: 09:30)
                addOption(h, '30');
            }
        });
    }

    function addOption(hour, min) {
        const option = document.createElement('option');
        // Formatage 9h -> 09h
        const hStr = hour.toString().padStart(2, '0');
        const timeStr = `${hStr}:${min}`;

        option.value = timeStr;
        option.text = timeStr;
        timeSelect.add(option);
    }

});
