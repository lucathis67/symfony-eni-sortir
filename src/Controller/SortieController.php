<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SearchDataType;
use App\Form\SortieCancelType;
use App\Form\SortieType;
use App\Manager\SortieManager;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    /**
     * @var SortieManager
     */
    private $sortieManager;

    /**
     * @param SortieManager $sortieManager
     */
    public function __construct(SortieManager $sortieManager)
    {
        $this->sortieManager = $sortieManager;
    }

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

    #[Route('/', name: 'inscription')]
    public function inscription(uuid $id, SortieRepository $sortieRepository, EntityManager $entityManager)
    {
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

    #[Route('/ajouter', name: 'create')]
    public function create(Request $request): Response
    {
        /** @var Participant $participant */
        $participant = $this->getUser();
        $sortie = new Sortie();
        $sortie->setOrganisateur($participant);
        $sortie->setCampus($participant->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $this->sortieManager->createOrUpdate($sortie, $sortieForm->get('saveAndPublish')->isClicked());

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

    #[Route('/modifier/{id}', name: 'update')]
    public function update(Sortie $sortie, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $sortie);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $this->sortieManager->createOrUpdate($sortie, $sortieForm->get('saveAndPublish')->isClicked());

            $this->addFlash('success', 'Sortie mise à jour ! ');
            return $this->redirectToRoute("sortie_show", ['id' => $sortie->getId()]);
        }

        return $this->render("sortie/update.html.twig",
            [
                'sortieForm' => $sortieForm->createView(),
                'sortieId' => $sortie->getId()
            ]);
    }

    #[Route('/annuler/{id}', name: 'cancel')]
    public function cancel(Sortie $sortie, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $sortie);

        $sortieCancelForm = $this->createForm(SortieCancelType::class, $sortie);
        $sortieCancelForm->handleRequest($request);
        if ($sortieCancelForm->isSubmitted() && $sortieCancelForm->isValid()) {

            $this->sortieManager->cancel($sortie);

            $this->addFlash('success', 'Sortie annulée ! ');
            return $this->redirectToRoute("sortie_list");
        }

        return $this->render("sortie/cancel.html.twig",
            [
                'sortieCancelForm' => $sortieCancelForm->createView(),
                'sortie' => $sortie
            ]);
    }
}
