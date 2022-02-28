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


    public function create(Sortie $sortie, bool $publish)
    {
        $publish
            ? $sortie->setEtat($this->etatRepository->findOneBy(['libelle'=>'Ouverte']))
            : $sortie->setEtat($this->etatRepository->findOneBy(['libelle'=>'Créée']));
        //dump($sortie);exit;
        $this->em->persist($sortie);
        $this->em->flush();
    }
}