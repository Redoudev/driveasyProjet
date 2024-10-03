<?php

namespace App\Form;

use App\Entity\Agence;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Voitures;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_depart', null, [
                'widget' => 'single_text',
            ])
            ->add('date_retour', null, [
                'widget' => 'single_text',
            ])
            ->add('agence', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'nom',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' - ' . $user->getPrenom();
                },
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voitures::class,
                'choice_label' => function (Voitures $voiture) {
                    return $voiture->getMarque() . ' - ' . $voiture->getModele();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}