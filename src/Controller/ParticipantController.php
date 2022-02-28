<?php

namespace App\Controller;

use App\Form\ParticipantFormType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class ParticipantController extends AbstractController
{
    #[Route('/participant/modifierProfil', name: 'participant_modifier')]
    public function modifier(
        ParticipantRepository       $participantRepository,
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface  $userAuthenticator,
        AppAuthenticator            $authenticator,
        EntityManagerInterface      $entityManager): Response
    {
        $participant = $this->getUser();
        //$participant = $participantRepository->find($id);
        if ($participant != null) {
            $form = $this->createForm(ParticipantFormType::class, $participant);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $participant->setPassword(
                    $userPasswordHasher->hashPassword(
                        $participant,
                        $form->get('password')->getData()
                    )
                );

                $entityManager->persist($participant);
                $entityManager->flush();

                $this->addFlash('success', 'Profil modifié!!');

                return $this->redirectToRoute("participant_afficher");
//                return $userAuthenticator->authenticateUser(
//                    $participant,
//                    $authenticator,
//                    $request
//                );

            }

            return $this->render('participant/profil.html.twig', [
                'participantForm' => $form->createView(),
                "participant" => $participant,
            ]);
        } else {
            $this->addFlash('warning', "Utilisateur inconnu !!");

            return $this->redirectToRoute('app_login');
        }

    }

    #[Route('/participant', name: 'participant_afficher')]
    public function afficher( ParticipantRepository $participantRepository)
    {
        $participant = $this->getUser();//$participantRepository->find($id);

        if ($participant != null) {
            return $this->render('participant/afficher.html.twig', ["participant" => $participant]);
        }

    }
}
