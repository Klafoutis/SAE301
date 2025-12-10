// assets/logic/reservation.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Système de réservation chargé 💅');

    // --- VARIABLES GLOBALES ---
    let currentStep = 1;
    const totalSteps = 4;

    // --- GESTION DU STEPPER (Navigation Corrigée) ---

    // Dictionnaire pour lier le numéro de l'étape à l'ID HTML exact
    const stepMapping = {
        1: 'step-services',
        2: 'step-planning',
        3: 'step-info',
        4: 'step-payment'
    };

    window.nextStep = function(stepTarget) {
        console.log("Tentative passage à l'étape :", stepTarget); // Debug

        // 1. Validation de l'étape actuelle avant de bouger
        // (On valide l'étape d'avant, donc stepTarget - 1)
        if (!validateStep(stepTarget - 1)) {
            return; // On arrête tout si pas valide
        }

        // 2. Changement d'affichage
        goToStep(stepTarget);
    };

    window.prevStep = function(stepTarget) {
        goToStep(stepTarget);
    };

    function goToStep(stepNumber) {
        // 1. On masque TOUTES les étapes pour éviter les conflits
        const allSteps = document.querySelectorAll('fieldset'); // ou .step-section
        allSteps.forEach(el => el.style.display = 'none');

        // 2. On récupère l'ID correspondant au numéro
        const targetId = stepMapping[stepNumber];
        const targetElement = document.getElementById(targetId);

        // 3. Si l'élément existe, on l'affiche
        if (targetElement) {
            targetElement.style.display = 'block';
            currentStep = stepNumber; // On met à jour la variable globale

            // Petit bonus : remonter en haut de page (UX)
            window.scrollTo(0, 0);

            // Si on arrive au récap (Etape 4), on met à jour le texte
            if (stepNumber === 4) updateFinalSummary();
        } else {
            console.error("ERREUR CRITIQUE : Impossible de trouver l'élément avec l'ID : " + targetId);
            alert("Erreur technique : L'étape suivante est introuvable.");
        }
    }



    function validateStep(step) {
        // Étape 1 : Au moins un service coché ?
        if (step === 1) {
            // CORRECTION : On utilise la classe '.service-checkbox' au lieu du 'name'
            const checked = document.querySelectorAll('.service-checkbox:checked');

            console.log("Cases cochées :", checked.length); // Pour le debug

            if (checked.length === 0) {
                alert("Veuillez sélectionner au moins une prestation pour continuer.");
                return false; // Bloque le passage à l'étape suivante
            }
        }
        // Étape 2 : Date remplie ?
        if (step === 2) {
            const dateInput = document.getElementById('reservation_dateRdv'); // ID généré par Symfony
            // Astuce : Si l'ID est différent, essayez document.querySelector('input[type="date"]')

            if (dateInput && !dateInput.value) {
                alert("Veuillez choisir une date dans le calendrier.");
                return false;
            }
        }
        return true;
    }

    // --- CALCULATEUR DE PRIX & DURÉE ---
    // CORRECTION : On cible la classe CSS au lieu du "name" qui change avec Symfony
    const servicesInputs = document.querySelectorAll('.service-checkbox');
    const deposeInput = document.querySelector('.js-depose');
    // Debug : Vérifions qu'on trouve bien les cases
    console.log('Nombre de services trouvés :', servicesInputs.length);

    function calculateTotal() {
        let totalPrix = 0;
        let totalMinutes = 0;

        // Somme des services
        servicesInputs.forEach(input => {
            if (input.checked) {
                // On convertit bien en Float/Int car les attributs data sont des chaînes
                // Si data-prix n'existe pas, on met 0 par sécurité
                let prix = parseFloat(input.dataset.prix || 0);
                let duree = parseInt(input.dataset.duree || 0);

                totalPrix += prix;
                totalMinutes += duree;
            }
        });

        // Ajout dépose
        if (deposeInput && deposeInput.checked) {
            // On s'assure de bien lire les données (sinon 0)
            let prixDepose = parseFloat(deposeInput.dataset.prix || 0);
            let dureeDepose = parseInt(deposeInput.dataset.duree || 0);

            console.log("Dépose ajoutée :", prixDepose, "€", dureeDepose, "min"); // Debug

            totalPrix += prixDepose;
            totalMinutes += dureeDepose;
        }

        // Affichage (Conversion minutes -> Heures)
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        // Formatage propre : "1h05" ou "45min"
        let timeString = '';
        if (hours > 0) {
            timeString += hours + 'h';
            if (minutes > 0) timeString += minutes.toString().padStart(2, '0');
        } else {
            timeString = minutes + 'min';
        }

        // Mise à jour du DOM
        const displayTime = document.getElementById('total-time');
        const displayPrice = document.getElementById('total-price');

        if(displayTime) displayTime.textContent = timeString;
        if(displayPrice) displayPrice.textContent = totalPrix + "€";
    }

    // Écouteurs d'événements
    servicesInputs.forEach(input => input.addEventListener('change', calculateTotal));
    if(deposeInput) {
        deposeInput.addEventListener('change', calculateTotal);
    }

    // --- GESTION DU PAIEMENT (CB vs Sur place) ---
    window.togglePaymentFields = function() {
        const isOnline = document.getElementById('pay_online').checked;
        const cardContainer = document.getElementById('card-details');

        if (!cardContainer) return; // Sécurité si le bloc n'existe pas

        const cardInputs = cardContainer.querySelectorAll('input');

        if (isOnline) {
            cardContainer.style.display = 'block';
            cardInputs.forEach(input => input.setAttribute('required', 'required'));
        } else {
            cardContainer.style.display = 'none';
            cardInputs.forEach(input => {
                input.removeAttribute('required');
                input.value = ''; // Reset valeur
            });
        }
    };

    // Attacher l'événement aux boutons radio paiement
    const radioOnline = document.getElementById('pay_online');
    const radioOnsite = document.getElementById('pay_onsite');
    if(radioOnline) radioOnline.addEventListener('change', window.togglePaymentFields);
    if(radioOnsite) radioOnsite.addEventListener('change', window.togglePaymentFields);

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
