<?php

namespace App\Api;

use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/ville')]
class VilleController extends AbstractController
{
    private VilleRepository $villeRepository;
    private SerializerInterface $serializer;

    public function __construct(VilleRepository $villeRepository, SerializerInterface $serializer)
    {
        $this->villeRepository = $villeRepository;
        $this->serializer = $serializer;
    }

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

    #[Route('/lieux/{id}', methods: ["GET"])]
    public function getLieux(Ville $ville)
    {
        $json = $this->serializer->serialize(
            $ville->getLieux(),
            'json',
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]
        );

        return new JsonResponse($json, json: true);
    }
}