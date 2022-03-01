<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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

            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    protected function addElements(FormInterface $form, Ville $ville = null) {
        $form->add('ville', EntityType::class, [
            'required' => true,
            'label' => "Ville : ",
            'class' => Ville::class,
            'choice_label' => 'nom',
            'mapped' => false,
            'data' => $ville,
            'placeholder' => 'Choisissez une ville'
        ]);

        $lieux = array();

        if ($ville) {
            $lieuRepository = $this->em->getRepository(Lieu::class);

            $lieux = $lieuRepository->findByIdVille($ville->getId());
        }

        $form->add('lieu', EntityType::class, [
            'required' => true,
            'label' => "Lieu : ",
            'class' => Lieu::class,
            'choice_label' => 'nom',
            'mapped' => true,
            'placeholder' => 'Choisissez un lieu',
            'attr' => [
                'disabled' => true
            ],
            'choices' => $lieux
        ]);
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected Ville and convert it into an Entity
        $ville = $this->em->getRepository(Ville::class)->find($data['ville']);

        $this->addElements($form, $ville);
    }

    function onPreSetData(FormEvent $event) {
        $sortie = $event->getData();
        $form = $event->getForm();

        // When you create a new sortie, the ville is always empty
        $ville = $sortie->getLieu()?->getVille() ? $sortie->getLieu->getVille() : null;

        $this->addElements($form, $ville);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
