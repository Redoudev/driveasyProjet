<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre prénom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est obligatoire.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ ]+$/', // Autorise les lettres et les espaces
                        'message' => 'Le prénom ne doit contenir que des lettres.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class,  [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre nom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est obligatoire.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ ]+$/', // Autorise les lettres et les espaces
                        'message' => 'Le nom ne doit contenir que des lettres.',
                    ]),
                ],
            ])
            ->add('email' ,EmailType::class, [
                'required' => true,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'adresse email est obligatoire.',
                ]),
                new Email([
                    'message' => 'Veuillez entrer un email valide.',
                ]),
            ],
            ])
            ->add('adresse', TextType::class,[
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez votre adresse'
                ],
            ])
            ->add('phone', TextType::class,  [
                'required' => true,
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le numéro de téléphone est obligatoire.',
                ]),
                new Type([
                    'type' => 'numeric',
                    'message' => 'Veuillez entrer un numéro valide.',
                ]),
                new Length([
                    'min' => 10,
                    'max' => 13,
                    'minMessage' => 'Le numéro de téléphone doit contenir au moins {{ limit }} chiffres.',
                    'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} chiffres.',
                ]),
            ],
            ])
            // ->add('agreeTerms', CheckboxType::class, [
            //                     'mapped' => false,
            //     'constraints' => [
            //         new IsTrue([
            //             'message' => 'You should agree to our terms.',
            //         ]),
            //     ],
            // ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez confirmer votre mot de passe',
                    ]),
                ],
            ])
        ;

        // Ajout du validateur de mot de passe au niveau du formulaire
        $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_SUBMIT, function ($event) {
            $form = $event->getForm();
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Vérification des mots de passe
            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new \Symfony\Component\Form\FormError('Les mots de passe ne correspondent pas.'));
            }
        });
        
    }


    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
