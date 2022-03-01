<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFilterType;
use App\Form\SortieType;
use App\Manager\SortieManager;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Data\SearchData;
use App\Form\SearchDataType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
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
        $searchData = new SearchData();
        $form = $this->createForm(SearchDataType::class, $searchData);
        $form->handleRequest($request);
        $sorties = $sortieRepository->findUsingFilter(searchData: $searchData, user: $this->getUser());
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'sortieForm' => $form->createView()
        ]);
    }

    public function inscription(int $id, SortieRepository $sortieRepository, EntityManager $entityManager)
    {
        $sortie = $sortieRepository->find($id);
        $dateDebutSortie = $sortie->getDateHeureDebut();
        $dateInscription = getdate();

        if ($dateDebutSortie > $dateInscription &&
            $sortie->getEtat()->getLibelle() == 'Ouverte' &&
            $sortie->getParticipants()->count() < $sortie->getNbInscriptionsMax()) {

            $participant = $this->getUser();//TODO voir pourquoi le getUser ne fonctionne pas
            $sortie->addParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }
    }

    public function desinscription(int $id, SortieRepository $sortieRepository, EntityManager $entityManager)
    {
        $sortie = $sortieRepository->find($id);
        $participant = $this->getUser();
        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();

    }

    #[Route('/ajouter', name: 'create')]
    public function create(Request $request, SortieManager $sortieManager): Response
    {
        $participant = $this->getUser();
        $sortie = new Sortie();
        $sortie->setOrganisateur($participant);
        $sortie->setCampus($participant->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortieManager->create($sortie, $sortieForm->get('saveAndPublish')->isClicked());

            $this->addFlash('success', 'Sortie ajoutée ! ');
            return $this->redirectToRoute("sortie_show", ['id' => $sortie->getId()]);
        }

        return $this->render("sortie/create.html.twig", ['sortieForm' => $sortieForm->createView()]);
    }

    #[Route('/afficher/{id}', name: 'show')]
    public function show(Sortie $sortie): Response
    {
        return $this->render("sortie/show.html.twig", ['sortie' => $sortie]);
    }
}
