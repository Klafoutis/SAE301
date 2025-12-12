// assets/logic/reservation.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Système de réservation chargé 💅');

    // --- CONFIGURATION MÉTIER ---
    const CONFIG = {
        allowedZips: ['10000', '10120', '10300', '10430', '10600', '10800'], // Ta liste de villes
        lunchStart: 12.0, // Début pause (12h)
        lunchEnd: 13.0,   // Fin pause (13h)
        bufferTrajet: 30, // 30 min de battement après RDV
    };

    let globalDuration = 0; // Pour stocker la durée arrondie
    // --- VARIABLES GLOBALES ---
    let currentStep = 1;
    let currentDiscount = 0;
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
        // Étape 1 : Services + GÉOGRAPHIE
        if (step === 1) {
            // A. Services cochés ?
            const checked = document.querySelectorAll('.service-checkbox:checked');
            if (checked.length === 0) {
                alert("Veuillez sélectionner au moins une prestation.");
                return false;
            }

            // B. Code Postal valide ? (NOUVEAU)
            const zipInput = document.getElementById('check_zipcode');
            if (zipInput) {
                const zipVal = zipInput.value.trim();
                // Si le code n'est pas dans la liste CONFIG
                if (!CONFIG.allowedZips.includes(zipVal)) {
                    alert("Désolé, votre zone (CP " + zipVal + ") n'est pas encore desservie.");
                    return false; // On bloque
                }
            }
        }

        // Étape 2 : Date remplie ?
        if (step === 2) {
            const dateInput = document.getElementById('reservation_dateRdv') || document.querySelector('input[type="date"]');
            if (dateInput && !dateInput.value) {
                alert("Veuillez choisir une date.");
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
                let prix = parseFloat(input.dataset.prix || 0);
                let duree = parseInt(input.dataset.duree || 0);
                totalPrix += prix;
                totalMinutes += duree;
            }
        });

        // Ajout dépose
        if (deposeInput && deposeInput.checked) {
            let prixDepose = parseFloat(deposeInput.dataset.prix || 0);
            let dureeDepose = parseInt(deposeInput.dataset.duree || 0);
            totalPrix += prixDepose;
            totalMinutes += dureeDepose;
        }

        // --- NOUVEAU : Application de la remise ---
        if (currentDiscount > 0) {
            // Calcul de la réduction
            const montantRemise = (totalPrix * currentDiscount) / 100;
            totalPrix = totalPrix - montantRemise;

            // Astuce : On arrondit à 2 décimales pour éviter les 45.00000001€
            totalPrix = Math.round(totalPrix * 100) / 100;
        }

        // --- NOUVEAU : Arrondi au 15 min supérieur ---
        // Ex: 65min -> 75min (1h15)
        globalDuration = Math.ceil(totalMinutes / 15) * 15;

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
        if (currentDiscount > 0) updateFinalSummary();

        // Mettre à jour les créneaux si une date est déjà choisie
        const dateInput = document.getElementById('reservation_dateRdv');
        if (dateInput && dateInput.value) {
            dateInput.dispatchEvent(new Event('change'));
        }


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

        // A. Règle : Minimum 24h à l'avance
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const yyyy = tomorrow.getFullYear();
        const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const dd = String(tomorrow.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;

        // B. Changement de date
        dateInput.addEventListener('change', function() {
            const dateVal = new Date(this.value);
            const dayOfWeek = dateVal.getDay(); // 0 = Dimanche

            timeSelect.innerHTML = '<option value="">-- Heure --</option>';
            timeSelect.disabled = false;

            // Règle : Dimanche Fermé
            if (dayOfWeek === 0) {
                addOption("Fermé le dimanche", "", true);
                timeSelect.disabled = true;
                return;
            }

            // Horaires d'ouverture/Fermeture
            let startH = 9;
            let closeH = 19; // 19h par défaut
            if (dayOfWeek === 6) closeH = 14; // Samedi 14h

            // Génération des créneaux (boucle par 15 min)
            for (let h = startH; h < closeH; h++) {
                checkAndAddSlot(h, 0, closeH);
                checkAndAddSlot(h, 15, closeH);
                checkAndAddSlot(h, 30, closeH);
                checkAndAddSlot(h, 45, closeH);
            }

            if(timeSelect.options.length <= 1) {
                addOption("Aucun créneau dispo (trop court)", "", true);
            }
        });
    }

    // Fonction intelligente qui vérifie si le créneau rentre
    function checkAndAddSlot(h, m, closingHour) {
        // 1. Calculs en décimal (ex: 9h30 = 9.5)
        const start = h + (m / 60);
        const duration = globalDuration / 60; // Durée prestation
        const buffer = CONFIG.bufferTrajet / 60; // Trajet

        const endClient = start + duration; // Quand le client a fini
        const endReal = endClient + buffer; // Quand l'artisan est libre (après trajet)

        // 2. Vérification Fermeture (Anti-débordement)
        // Le client doit avoir fini avant la fermeture
        if (endClient > closingHour) return;

        // 3. Vérification Pause Déjeuner
        // Si le RDV finit après 12h ET commence avant 13h, ça touche la pause
        if (endClient > CONFIG.lunchStart && start < CONFIG.lunchEnd) return;

        // Si tout est bon, on affiche
        addOption(`${format(h)}:${format(m)}`, `${format(h)}:${format(m)}`);
    }

    function format(n) { return n.toString().padStart(2, '0'); }

    function addOption(text, value, disabled = false) {
        const opt = document.createElement('option');
        opt.text = text;
        opt.value = value;
        opt.disabled = disabled;
        timeSelect.add(opt);
    }



});
