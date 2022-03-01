<?php

namespace App\Api;

use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/ville')]
class VilleController extends AbstractController
{
    /**
     * @var VilleRepository
     */
    private VilleRepository $villeRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var LieuRepository
     */
    private LieuRepository $lieuRepository;

    /**
     * @param VilleRepository $villeRepository
     * @param SerializerInterface $serializer
     * @param LieuRepository $lieuRepository
     */
    public function __construct(VilleRepository     $villeRepository,
                                SerializerInterface $serializer,
                                LieuRepository      $lieuRepository)
    {
        $this->villeRepository = $villeRepository;
        $this->serializer = $serializer;
        $this->lieuRepository = $lieuRepository;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', methods: ["GET"])]
    public function liste()
    {
        $villes = $this->villeRepository->findAll();
        $json = $this->serializer->serialize(
            $villes,
            'json',
            ['groups' => ['min', 'full']]
        );

        return new JsonResponse($json, json: true);
    }

    /**
     * @param Ville $ville
     * @return JsonResponse
     */
    #[Route('/lieux/{id}', methods: ["GET"])]
    public function getLieux(Ville $ville, SerializerInterface $serializer): JsonResponse
    {
        $responseArray = $serializer->serialize($ville, 'json');

        return new JsonResponse($responseArray, json: true);
    }
}