<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, ['required' => true])
            ->add('prenom', TextType::class, ['required' => true, 'label' => "Prénom"])
            ->add('nom', TextType::class, ['required' => true, 'label' => "Nom"])
            ->add('telephone', TextType::class, ['required' => true, 'label' => "Téléphone"])
            ->add('email', EmailType::class, ['required' => true, 'label' => "E-mail"])
//            ->add('password', RepeatedType::class, [
//                'type' => PasswordType::class,
//                'invalid_message' => 'Les deux mots de passe saisies sont différents',
//                'required' => true,
//                'first_options' => ['label' => 'Mot de passe'],
//                'second_options' => ['label' => 'Confirmer le mot de passe'],
//                'mapped' => false,
//                'attr' => ['autocomplete' => 'new-password'],
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Merci de saisir un mot de passe',
//                    ]),
//                    new Length([
//                        'min' => 8,
//                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
//                        // max length allowed by Symfony for security reasons
//                        'max' => 4096,
//                    ]),
//                ],
//            ])
            ->add('campus', EntityType::class, [
                'required' => true,
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('upload_file', FileType::class, [
                'label' => 'Uploader une photo (jpg,jpeg,png,bmp,gif) Taille max: 8192 Ko',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '8192k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/bmp',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => "Le format n'est pas valide.",
                    ])
                ],
            ])
//            ->add('submit', SubmitType::class, [
//                'label' => 'Enregistrer',
//                'attr' => [
//                    'class' => 'btn-profil'
//                ]
//            ])
//            ->add('reset', ResetType::class, [
//                'label' => 'Annuler',
//                'attr' => [
//                    'class' => 'btn-profil'
//                ]
//            ])
//            ->add('reset', UrlType::class, [
//                'label' => 'Modifier le mot de passe',
//                'attr' => [
//                    'class' => 'btn-profil'
//                ]
//            ]);

//            ->add('administrateur')
//            ->add('actif')
//            ->add('sorties')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }


}
