<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de la sortie : "
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => "Date et heure de la sortie : ",
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => "Date limite d'inscription : ",
                'widget' => 'single_text'
            ])
            ->add('duree', NumberType::class, [
                'label' => "Durée : "
            ])
            ->add('nbInscriptionsMax', NumberType::class, [
                'label' => "Nombre de places : "
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => "Description et infos : ",
                'required' => false
            ])
            ->add('campus', TextType::class, [
                'label' => "Campus : ",
                'disabled' => true,
            ])
            ->add('ville', EntityType::class, [
                'label' => "Ville : ",
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'placeholder' => 'Choisissez une ville'
            ])
            ->add('lieu', EntityType::class, [
                'label' => "Lieu : ",
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'mapped' => true,
                'placeholder' => 'Choisissez un lieu',
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add('rue', TextType::class, [
                'label' => "Rue : ",
                'mapped' => false,
                'disabled' => true
            ])
            ->add('codePostal', TextType::class, [
                'label' => "Code postal : ",
                'mapped' => false,
                'disabled' => true
            ])
            ->add('latitude', TextType::class, [
                'label' => "Latitude : ",
                'mapped' => false,
                'disabled' => true
            ])
            ->add('longitude', TextType::class, [
                'label' => "Longitude : ",
                'mapped' => false,
                'disabled' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('saveAndPublish', SubmitType::class, ['label' => 'Publier la sortie']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
