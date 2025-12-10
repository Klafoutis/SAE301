// assets/logic/reservation.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Système de réservation chargé 💅');

    // --- VARIABLES GLOBALES ---
    let currentStep = 1;
    const totalSteps = 4;

    // --- GESTION DU STEPPER (Navigation) ---
    window.nextStep = function(stepTarget) {
        // Validation simple avant de passer à l'étape suivante
        if (!validateStep(currentStep)) return;

        // Masquer l'étape actuelle
        document.getElementById('step-' + (stepTarget - 1)).style.display = 'none';

        // Afficher la cible
        const nextFieldset = document.getElementById('step-' + stepTarget);
        if(nextFieldset) { // On vérifie que l'élément existe car id="step-payment" ou "step-4" ?
            // Note: Assurez-vous que vos ID dans le HTML sont bien : step-1, step-2, etc.
            // Si vous avez mis "step-services", "step-planning", adaptez la logique ici.
            // Pour simplifier, utilisons les ID sémantiques définis plus tôt :
            showStepById(stepTarget);
        }

        currentStep = stepTarget;
        updateSummary(); // Mettre à jour les récaps
    };

    window.prevStep = function(stepTarget) {
        showStepById(stepTarget);
        currentStep = stepTarget;
    };

    function showStepById(stepIndex) {
        // Masquer tout
        document.getElementById('step-services').style.display = 'none';
        document.getElementById('step-planning').style.display = 'none';
        document.getElementById('step-info').style.display = 'none';
        document.getElementById('step-payment').style.display = 'none';

        // Afficher le bon
        if (stepIndex === 1) document.getElementById('step-services').style.display = 'block';
        if (stepIndex === 2) document.getElementById('step-planning').style.display = 'block';
        if (stepIndex === 3) document.getElementById('step-info').style.display = 'block';
        if (stepIndex === 4) document.getElementById('step-payment').style.display = 'block';
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

        return true; // Tout est bon, on passe !
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

});
