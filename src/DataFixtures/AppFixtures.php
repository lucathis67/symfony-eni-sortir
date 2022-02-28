<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {


        for ($i = 0; $i < 15; $i++)
        {
            $ville = new Ville();
            $ville->setNom('Strasbourg');
            $ville->setCodePostal('67270');
            $manager->persist($ville);

            $lieu = new Lieu();
            $lieu->setNom('Cathedrale');
            $lieu->setRue('rue des écrivains');
            $lieu->setVille($ville);
            $lieu->setLatitude(48.34);
            $lieu->setLongitude(7.45);
            $manager->persist($lieu);

            $campus = new Campus();
            $campus->setNom('unistra');
            $manager->persist($campus);

            $participant = new Participant();
            $participant->setNom('mathis');
            $participant->setPrenom('lucas');
            $participant->setPseudo('lucathis' . rand(0, 10000));
            $participant->setEmail('lucas@mathis.fr' . rand(0, 10000));
            $participant->setPassword($this->hasher->hashPassword($participant, 'lucathis' . rand(0, 10000)));
            $participant->setTelephone('0699070126');
            $participant->setAdministrateur(true);
            $participant->setActif(true);
            $participant->setCampus($campus);
            $manager->persist($participant);




            $etat = new Etat();
            $etat->setLibelle('Ouverte');
            $manager->persist($etat);

            $sortie = new Sortie();
            $sortie->setNom('visite de la cathédrale');
            $sortie->setDateHeureDebut(new \DateTime('now + 10 day'));
            $sortie->setDuree(1);
            $sortie->setDateLimiteInscription(new \DateTime('now + 8 day'));
            $sortie->setNbInscriptionsMax(15);
            $sortie->setInfosSortie('Vue à 365 degres sur la ville de Strasbourg depuis la plateforme');
            $sortie->setEtat($etat);
            $sortie->setOrganisateur($participant);
            $sortie->setCampus($campus);
            $sortie->setLieu($lieu);
            $manager->persist($sortie);
        }

        $participant = new Participant();
        $participant->setNom('dhib');
        $participant->setPrenom('kamel');
        $participant->setPseudo('caliel68' );
        $participant->setEmail('kd@kd.com');
        $participant->setPassword($this->hasher->hashPassword($participant, '123456789'));
        $participant->setTelephone('0612345678');
        $participant->setAdministrateur(true);
        $participant->setActif(true);
        $participant->setCampus($campus);
        $manager->persist($participant);

        $manager->flush();
    }
}
