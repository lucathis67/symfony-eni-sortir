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
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('inscription/{id}', name: 'inscription')]
    public function inscription(Sortie $sortie, EntityManagerInterface $entityManager)
    {
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
            $this->addFlash('success', 'Inscription à la sortie effectuée!!');
        }
        return $this->redirectToRoute("sortie_list");
    }

    #[Route('desinscription/{id}', name: 'desinscription')]
    public function desinscription(Sortie $sortie, EntityManagerInterface $entityManager)
    {
        $participant = $this->getUser();
        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute("sortie_list");
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
