<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contient', SearchType::class, [
                'label' => 'Le nom de la sortie contient',
                'required' => false,
                'mapped' => false,
            ])
            ->add('campus', ChoiceType::class, [
                'choices' => [
                    'SAINT-HERBLAIN' => 'SAINT-HERBLAIN',
                    'CHARTRES DE BRETAGNE' => 'CHARTRES DE BRETAGNE',
                    'LA ROCHE SUR YON' => 'LA ROCHE SUR YON',
                ],
                'multiple' => false,
                'mapped' => false,
            ])
            ->add('dateHeureDebut', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre',
                'required' => false,
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'et',
                'required' => false,
            ])
            ->add('organisee', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
                'mapped' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'mapped' => false,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'mapped' => false,
            ])
            ->add('passees', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
