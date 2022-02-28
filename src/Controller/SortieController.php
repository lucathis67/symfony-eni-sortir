<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchDataType;
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
        $searchData = new SearchData();
        $form = $this->createForm(SearchDataType::class, $searchData);
        $form->handleRequest($request);


        if ($form->isSubmitted())
        {
            $sorties = $sortieRepository->findUsingFilter(searchData: $searchData, user: $this->getUser());
        }
        else
        {
            $sorties = $sortieRepository->findAll();
        }

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'sortieForm' => $form->createView()
        ]);
    }
}
