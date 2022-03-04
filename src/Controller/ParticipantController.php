<?php

namespace App\Controller;

use App\Form\EditPasswordType;
use App\Form\ParticipantFormType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantController extends AbstractController
{
    #[Route('/participant/modifierProfil', name: 'participant_modifier')]
    public function modifier(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        FileUploader                $uploader): Response
    {
        $participant = $this->getUser();
        if ($participant != null) {
            $form = $this->createForm(ParticipantFormType::class, $participant);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
//                $participant->setPassword(
//                    $userPasswordHasher->hashPassword(
//                        $participant,
//                        $form->get('password')->getData()
//                    )
//                );

                $photoFile = $form->get('upload_file')->getData();
                /** @var UploadedFile $photoFile */
                if ($photoFile) {
                    $photoFileName = $uploader->upload($photoFile);
                    $persistPhoto = $this->getParameter('upload_directory') . '/' . $participant->getPhoto();

                    if ($participant->getPhoto() && file_exists($persistPhoto)) {
                        unlink($persistPhoto);
                    }
                    $participant->setPhoto($photoFileName);

                }

                $entityManager->persist($participant);
                $entityManager->flush();

                $this->addFlash('success', 'Profil modifié!!');

                return $this->redirectToRoute("participant_afficher", ['id' => $participant->getId()]);
            }

            return $this->render('participant/profil.html.twig', [
                'participantForm' => $form->createView(),
                "participant" => $participant,
            ]);
        } else {
            $this->addFlash('fail', "Utilisateur inconnu !!");

            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/participant/{id}', name: 'participant_afficher')]
    public function afficher(string $id, ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($id);
        if ($participant != null) {
            return $this->render('participant/afficher.html.twig', ["participant" => $participant]);
        }
    }

    #[Route('/editPwd', name: 'participant_editPassword')]
    public function editPassword(EntityManagerInterface $entityManager,
                                 Request $request,
                                 UserPasswordHasherInterface $userPasswordHasher):Response
    {

        $participant = $this->getUser();
        if ($participant != null)
        {
            $form = $this->createForm(EditPasswordType::class, $participant);

            $form->handleRequest($request);

            if($form->isSubmitted()&& $form->isValid())
            {
                $participant->setPassword(
                    $userPasswordHasher->hashPassword(
                        $participant,
                        $form->get('password')->getData()
                    )
                );

                $entityManager->persist($participant);
                $entityManager->flush();
                $this->addFlash('success','Mot de passe modifié! Bonne journée');
                return $this->redirectToRoute('participant_modifier');
            }

            return $this->render('participant/editPassword.html.twig', [
                'editPasswordForm' => $form->createView(),
                'participant'=>$participant
            ]);

        }
        else
        {
            $this->addFlash('fail', "Utilisateur inconnu !!");
            return $this->redirectToRoute('app_login');
        }

    }

}
