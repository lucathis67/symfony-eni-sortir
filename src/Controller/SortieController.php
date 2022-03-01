<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFilterType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

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
    #[Route('/', name: 'inscription')]
    public function inscription(uuid $id, SortieRepository $sortieRepository, EntityManager $entityManager)
    {
        dd($id);
        $sortie = $sortieRepository->find($id);

        $dateDebutSortie = $sortie->getDateHeureDebut();
        $dateInscription = getdate();

        if ($dateDebutSortie > $dateInscription &&
            $sortie->getEtat()->getLibelle() == 'Ouverte' &&
            $sortie->getParticipants()->count() < $sortie->getNbInscriptionsMax()) {

            $participant = $this->getUser();
            $sortie->addParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute("sortie_list");
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
}
