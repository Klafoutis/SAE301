<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
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

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // --- BLOC 1 ---
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'choice_attr' => function(Service $service) {
                    return ['data-duree' => $service->getDuration(), 'data-prix'  => $service->getPrice()];
                },
            ])
            ->add('includeDepose', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'J\'ai besoin d\'une dépose (Retrait d\'ancien vernis/gel)',
                'attr' => ['class' => 'js-depose', 'data-duree' => 20, 'data-prix' => 10]
            ])

            // --- BLOC 2 ---
            ->add('dateRdv', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date du rendez-vous',
                'attr' => ['min' => date('Y-m-d')]
            ])

            // --- BLOC 3 : VISITEUR (Non requis par défaut, géré par validation groups si on voulait pousser plus loin) ---
            ->add('guestFirstname', TextType::class, ['required' => false, 'label' => 'Prénom'])
            ->add('guestLastname', TextType::class, ['required' => false, 'label' => 'Nom'])
            ->add('guestEmail', EmailType::class, ['required' => false, 'label' => 'Email de confirmation'])
            ->add('guestPhone', TelType::class, ['required' => false, 'label' => 'Numéro de mobile'])

            // --- BLOC 3 : COMMUN ---
            ->add('visitAddress', TextType::class, [
                'label' => 'Adresse exacte du domicile',
                'attr' => ['placeholder' => 'Ex: 12 rue de la République']
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'Précisions d\'accès (Optionnel)',
                'attr' => ['rows' => 3, 'placeholder' => 'Digicode A123, 3ème étage gauche, attention au chat...']
            ])

            // --- BLOC 4 : PAIEMENT & CGU (Non mappés) ---
            ->add('promoCode', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Code Promo'
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'mapped' => false,
                'expanded' => true, // Radio buttons
                'multiple' => false,
                'choices' => [
                    'Payer maintenant' => 'online',
                    'Payer sur place' => 'onsite'
                ],
                'data' => 'onsite' // Valeur par défaut
            ])
            ->add('acceptCGU', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'J\'accepte les Conditions Générales de Vente et je m\'engage à être présente à mon domicile à l\'heure indiquée.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Reservation::class]);
    }
}
