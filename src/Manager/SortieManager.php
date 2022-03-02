<?php

namespace App\Manager;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortieManager
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EtatRepository
     */
    private  $etatRepository;

    /**
     * @param EntityManagerInterface $em
     * @param EtatRepository $etatRepository
     */
    public function __construct(EntityManagerInterface $em, EtatRepository $etatRepository)
    {
        $this->em = $em;
        $this->etatRepository = $etatRepository;
    }

    public function createOrUpdate(Sortie $sortie, bool $publish)
    {
        $publish
            ? $sortie->setEtat($this->etatRepository->findOneBy(['libelle'=>'Ouverte']))
            : $sortie->setEtat($this->etatRepository->findOneBy(['libelle'=>'Créée']));
        $this->em->persist($sortie);
        $this->em->flush();
    }

    public function cancel(Sortie $sortie)
    {
        $sortie->setEtat($this->etatRepository->findOneBy(['libelle'=>'Annulée']));
        $this->em->persist($sortie);
        $this->em->flush();
    }
}