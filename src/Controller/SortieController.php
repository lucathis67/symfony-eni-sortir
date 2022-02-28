<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Manager\SortieManager;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties
        ]);
    }

    #[Route('/ajouter', name: 'create')]
    public function create(Request $request, SortieManager $sortieManager, EtatRepository $etatRepository): Response
    {
        $participant = $this->getUser();
        $sortie = new Sortie();
        $sortie->setOrganisateur($participant);
        $sortie->setCampus($participant->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        //dump($request);exit;

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortieManager->create($sortie, $sortieForm->get('saveAndPublish')->isClicked());

            $this->addFlash('success', 'Sortie ajoutée ! ');
            return $this->render("sortie/afficher.html.twig", ['id' => $sortie->getId()]);
        }

        return $this->render("sortie/create.html.twig", ['sortieForm' => $sortieForm->createView()]);
    }
}
