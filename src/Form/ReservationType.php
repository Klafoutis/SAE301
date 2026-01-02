<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;

class ReservationType extends AbstractType
{
    private const ALLOWED_ZIPCODES = [
        '10000', // Troyes
        '10300', // Sainte-Savine
        '10120', // St-André
        '10600', // La Chapelle
        '10150', // Pont-Ste-Marie / Lavau
        '10430', // Rosières
        '10800', // St-Julien / Buchères
        '10450', // Bréviandes
        '10420', // Les Noës
        '10410', // St-Parres-aux-Tertres
        '10180', // St-Lyé
        '10320', // Bouilly
        '10270', // Lusigny
    ];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // --- ÉTAPE 1 : PANIER ---
        $builder
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true, // Checkboxes
                'group_by' => function (Service $service) {
                    return $service->getCategory() ? $service->getCategory()->getName() : 'Autre';
                },
                'query_builder' => function (ServiceRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.active = 1')
                        ->orderBy('s.category', 'ASC');
                },
                'choice_attr' => function (Service $service) {
                    return [
                        'data-price' => $service->getPrice(),
                        'data-duration' => $service->getDuration(),
                        'class' => 'service-checkbox'
                    ];
                },
                'label' => 'Choisissez vos soins',
            ])
            ->add('hasRemoval', CheckboxType::class, [
                'mapped' => false, // Pas dans l'entité
                'required' => false,
                'label' => 'J\'ai besoin d\'une dépose',
                'help' => 'Important : +20 min / +10€',
                'attr' => [
                    'class' => 'removal-switch',
                    'data-duration' => 20, // [cite: 10]
                    'data-price' => 10     // Prix demandé
                ]
            ])
        ->add('visitZipcode', TextType::class, [
        'mapped' => false, // Toujours non mappé (on concatène à la fin)
        'label' => 'Code Postal (Vérification de zone)',
        'help' => 'Nous desservons uniquement Troyes et ses alentours (20km).',
        'attr' => [
            'class' => 'zipcode-input', // Pour le JS
            'placeholder' => 'Ex: 10000',
            'maxlength' => 5
        ],
        'constraints' => [
            new NotBlank(['message' => 'Le code postal est requis.']),
            new Callback([$this, 'validateZipcode']) // Validation personnalisée
        ]
    ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // On récupère l'heure soumise par le JS
            $selectedTime = $data['timeSlot'] ?? null;

            // On recrée le champ timeSlot avec cette seule option valide pour que Symfony l'accepte
            $form->add('timeSlot', ChoiceType::class, [
                'mapped' => false,
                'choices' => $selectedTime ? [$selectedTime => $selectedTime] : [],
                'label' => 'Horaires disponibles',
                'attr' => ['id' => 'reservation_timeSlot'] // Important pour le JS
            ]);
        });

        // --- ÉTAPE 2 : CALENDRIER ---
        $builder
            ->add('dateRdv', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date du rendez-vous',
                'attr' => [
                    // MODIFICATION ICI : On force la date min à dans 2 jours
                    'min' => (new \DateTime('+2 days'))->format('Y-m-d'),
                    'class' => 'form-control' // Style optionnel
                ]
            ])
            ->add('timeSlot', ChoiceType::class, [
                'mapped' => false,
                'choices' => [],
                'label' => 'Horaires disponibles',
                'disabled' => true, // Activé par JS
                'attr' => ['id' => 'reservation_timeSlot']
            ]);

        // --- ÉTAPE 3 : COORDONNÉES ---
        $builder
            ->add('guestFirstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true, // Bloquage Navigateur
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']) // Bloquage Serveur
                ],
                'attr' => ['placeholder' => 'Ex: Julie']
            ])
            ->add('guestLastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.'])
                ],
                'attr' => ['placeholder' => 'Ex: Sauvage']
            ])
            ->add('guestEmail', EmailType::class, [
                'label' => 'Email de confirmation',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire.']),
                    new Email(['message' => 'Veuillez entrer un email valide.'])
                ],
                'attr' => ['placeholder' => 'Ex: julie@mail.com']
            ])
            ->add('guestPhone', TelType::class, [
                'label' => 'Téléphone Mobile',
                'required' => true,
                'help' => 'Je vous envoie un SMS en arrivant.',
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire.']),
                    new Regex([
                        'pattern' => '/^0[1-9][0-9]{8}$/', // Format Français standard
                        'message' => 'Veuillez entrer un numéro valide à 10 chiffres.'
                    ])
                ],
                'attr' => [
                    'pattern' => '[0-9]{10}',
                    'maxlength' => 10,
                    'placeholder' => '06 12 34 56 78'
                ]
            ])
            ->add('visitAddress', TextType::class, [
                'label' => 'Adresse (Numéro et Rue)',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire.'])
                ],
                'attr' => ['placeholder' => 'Ex: 10 rue de la République']
            ])
            ->add('visitCity', TextType::class, [
                'mapped' => false,
                'label' => 'Ville',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La ville est obligatoire.'])
                ],
                // On peut pré-remplir "Troyes" si on veut faciliter la vie
                'attr' => ['placeholder' => 'Ex: Troyes']
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Précisions d\'accès (Optionnel)',
                'required' => false, // LE SEUL CHAMP FACULTATIF
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Facultatif : Digicode, étage, interphone...'
                ]
            ]);

        // --- ÉTAPE 4 : PAIEMENT ---
        $builder
            ->add('paymentMethod', ChoiceType::class, [
                'mapped' => false, // Pas dans l'entité, sera traité en Controller
                'label' => 'Moyen de paiement',
                'expanded' => true,
                'choices' => [
                    'Payer maintenant (Carte Bancaire)' => 'online',
                    'Payer sur place' => 'onsite'
                ],
                'attr' => ['class' => 'payment-method-radios']
            ])
            ->add('acceptCGU', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [new IsTrue(['message' => 'Veuillez accepter les CGV.'])],
                'label' => 'J\'accepte les conditions générales'
            ]);
    }
    // Fonction de validation Backend
    public function validateZipcode($value, ExecutionContextInterface $context): void
    {
        if (!in_array($value, self::ALLOWED_ZIPCODES)) {
            $context->buildViolation('Désolé, nous ne nous déplaçons pas encore dans cette zone (Code: {{ value }}).')
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Reservation::class]);
    }
}
