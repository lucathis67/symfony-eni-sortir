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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

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

    #[Route('_inscription/{id}', name: 'inscription')]
    public function inscription(string $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {

        $sortie = $sortieRepository->find($id);
        $dateDebutSortie = $sortie->getDateHeureDebut();
        $dateInscription = getdate();

        if($sortie->getEtat()->getLibelle() != 'Ouverte')
        {
            $this->addFlash('fail', 'La sortie est fermée!!');
        }
        elseif ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax())
        {
            $this->addFlash('fail', 'Le nombre de participant est au maximum');
        }
        elseif ($dateDebutSortie < $dateInscription )
        {
            $this->addFlash('fail', 'La date d\'inscription est supérieur à la date de début de l\evenement.');
        }else{
            $participant = $this->getUser();
            $sortie->addParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('succes', 'Inscription à la sortie effectuée!!');
        }

//        if($sortie->getEtat()->getLibelle() != 'Ouverte')
//        {
//            $this->addFlash('fail', 'La sortie n\'est pas encore ouverte!!');
//        }
//
//        if ($dateDebutSortie > $dateInscription &&
//            $sortie->getEtat()->getLibelle() == 'Ouverte' &&
//            $sortie->getParticipants()->count() < $sortie->getNbInscriptionsMax()) {
//
//
//
//        }

        return $this->redirectToRoute("sortie_list");
    }

    #[Route('_desinscription/{id}', name: 'desinscription')]
    public function desinscription(string $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        //dd($id);
        $sortie = $sortieRepository->find($id);
        $participant = $this->getUser();
        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute("sortie_list");
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
