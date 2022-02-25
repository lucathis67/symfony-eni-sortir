<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFilterType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {
        $campuses = $campusRepository->findAll();
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieFilterType::class, $sortie);

        if (isset($_POST))
        {
            $sorties = $sortieRepository->findBy(
                [],
                []
            );
            return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'campuses' => $campuses,
                'sortieForm' => $sortieForm->createView()
                ]);
        }
        else
        {
            $sorties = $sortieRepository->findAll();
            return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'campuses' => $campuses,
                'sortieForm' => $sortieForm->createView()
            ]);
        }
    }
}
