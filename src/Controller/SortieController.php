<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFilterType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieFilterType::class, $sortie);
        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted())
        {
            $user = $this->getUser() ?? null;
            $contient = $sortieForm['contient']->getData() ?? null;
            $campus = $sortieForm['campus']->getData() ?? null;
            $dateHeureDebut = $sortieForm['dateHeureDebut']->getData() ?? null;
            $dateLimiteInscription = $sortieForm['dateLimiteInscription']->getData() ?? null;
            $organisee = $sortieForm['organisee']->getData() ?? false;
            $inscrit = $sortieForm['inscrit']->getData() ?? false;
            $nonInscrit = $sortieForm['nonInscrit']->getData() ?? false;
            $passees = $sortieForm['passees']->getData() ?? false;

            $sorties = $sortieRepository->findUsingFilter(
                user: $user,
                contient: $contient,
                campus: $campus,
                dateHeureDebut: $dateHeureDebut,
                dateLimiteInscription: $dateLimiteInscription,
                organisee: $organisee,
                inscrit: $inscrit,
                nonInscrit: $nonInscrit,
                passees: $passees
            );
        }
        else
        {
            $sorties = $sortieRepository->findAll();
        }

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
