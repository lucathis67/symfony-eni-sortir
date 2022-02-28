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

        // CREATION DE TROIS VILLES

        $villeSaintHerblain = new Ville();
        $villeSaintHerblain->setNom('Saint Herblain');
        $villeSaintHerblain->setCodePostal('44800');
        $manager->persist($villeSaintHerblain);

        $villeChartresDeBretagne = new Ville();
        $villeChartresDeBretagne->setNom('Chartres de Bretagne');
        $villeChartresDeBretagne->setCodePostal('35066');
        $manager->persist($villeChartresDeBretagne);

        $villeLaRocheSurYon = new Ville();
        $villeLaRocheSurYon->setNom('La Roche sur Yon');
        $villeLaRocheSurYon->setCodePostal('85000');
        $manager->persist($villeLaRocheSurYon);




        // CREATION DES TROIS CAMPUS

        $campusSaintHerblain = new Campus();
        $campusSaintHerblain->setNom('SAINT HERBLAIN');
        $manager->persist($campusSaintHerblain);

        $campusChartresDeBretagne = new Campus();
        $campusChartresDeBretagne->setNom('CHARTRES DE BRETAGNE');
        $manager->persist($campusChartresDeBretagne);

        $campusLaRocheSurYon = new Campus();
        $campusLaRocheSurYon->setNom('LA ROCHE SUR YON');
        $manager->persist($campusLaRocheSurYon);




        // CREATION DE PLUSIEURS PARTICIPANTS

        $participantLucas = new Participant();
        $participantLucas->setNom('mathis');
        $participantLucas->setPrenom('lucas');
        $participantLucas->setPseudo('lucas');
        $participantLucas->setEmail('lucas@email.fr');
        $participantLucas->setPassword($this->hasher->hashPassword($participantLucas, 'lucas'));
        $participantLucas->setTelephone('0000000000');
        $participantLucas->setAdministrateur(true);
        $participantLucas->setActif(true);
        $participantLucas->setCampus($campusSaintHerblain);
        $manager->persist($participantLucas);

        $participantSofian = new Participant();
        $participantSofian->setNom('barkallah');
        $participantSofian->setPrenom('sofian');
        $participantSofian->setPseudo('sofian');
        $participantSofian->setEmail('sofian@email.fr');
        $participantSofian->setPassword($this->hasher->hashPassword($participantSofian, 'sofian'));
        $participantSofian->setTelephone('0000000001');
        $participantSofian->setAdministrateur(true);
        $participantSofian->setActif(true);
        $participantSofian->setCampus($campusChartresDeBretagne);
        $manager->persist($participantSofian);

        $participantKamel = new Participant();
        $participantKamel->setNom('dhib');
        $participantKamel->setPrenom('kamel');
        $participantKamel->setPseudo('kamel');
        $participantKamel->setEmail('kamel@email.fr');
        $participantKamel->setPassword($this->hasher->hashPassword($participantKamel, 'kamel'));
        $participantKamel->setTelephone('0699070126');
        $participantKamel->setAdministrateur(true);
        $participantKamel->setActif(true);
        $participantKamel->setCampus($campusLaRocheSurYon);
        $manager->persist($participantKamel);




        // CREATION DES DIFFERENTS ETATS

        $etatOuvert = new Etat();
        $etatOuvert->setLibelle('Ouverte');
        $manager->persist($etatOuvert);

        $etatCree = new Etat();
        $etatCree->setLibelle('Créée');
        $manager->persist($etatCree);

        $etatCloture = new Etat();
        $etatCloture->setLibelle('Clôturée');
        $manager->persist($etatCloture);

        $etatEnCours = new Etat();
        $etatEnCours->setLibelle('Activité en cours');
        $manager->persist($etatEnCours);

        $etatPassee = new Etat();
        $etatPassee->setLibelle('Passée');
        $manager->persist($etatPassee);

        $etatAnnule = new Etat();
        $etatAnnule->setLibelle('Annulée');
        $manager->persist($etatAnnule);




        // CREATION DE PLUSIEURS LIEUX DE SORTIES

        $lieuCampus = new Lieu();
        $lieuCampus->setNom('Campus de Saint Herblain');
        $lieuCampus->setRue('rue de l\'eni');
        $lieuCampus->setVille($villeSaintHerblain);
        $lieuCampus->setLatitude(47.12);
        $lieuCampus->setLongitude(1.38);
        $manager->persist($lieuCampus);

        $lieuRueDeLaSoif = new Lieu();
        $lieuRueDeLaSoif->setNom('bars de Rennes');
        $lieuRueDeLaSoif->setRue('rue de la soif');
        $lieuRueDeLaSoif->setVille($villeChartresDeBretagne);
        $lieuRueDeLaSoif->setLatitude(48.11);
        $lieuRueDeLaSoif->setLongitude(-1.67);
        $manager->persist($lieuRueDeLaSoif);

        $lieuCinema = new Lieu();
        $lieuCinema->setNom('Le cinéville');
        $lieuCinema->setRue('rue francois cevert');
        $lieuCinema->setVille($villeLaRocheSurYon);
        $lieuCinema->setLatitude(46.67);
        $lieuCinema->setLongitude(-1.42);
        $manager->persist($lieuCinema);




        // CREATION DE PLUSIEURS SORTIES

        $sortieCampus = new Sortie();
        $sortieCampus->setNom('Visite de la silicon valley eni');
        $sortieCampus->setDateHeureDebut(new \DateTime('now - 30 day'));
        $sortieCampus->setDuree(3);
        $sortieCampus->setDateLimiteInscription(new \DateTime('now - 38 day'));
        $sortieCampus->setNbInscriptionsMax(40);
        $sortieCampus->setInfosSortie('Visite des locaux eni et rencontre entre etudiants');
        $sortieCampus->setEtat($etatPassee);
        $sortieCampus->setOrganisateur($participantLucas);
        $sortieCampus->setCampus($campusSaintHerblain);
        $sortieCampus->setLieu($lieuCampus);
        $manager->persist($sortieCampus);

        $sortieBistrot = new Sortie();
        $sortieBistrot->setNom('Allons boire un coup!');
        $sortieBistrot->setDateHeureDebut(new \DateTime('now + 10 day'));
        $sortieBistrot->setDuree(3);
        $sortieBistrot->setDateLimiteInscription(new \DateTime('now + 8 day'));
        $sortieBistrot->setNbInscriptionsMax(10);
        $sortieBistrot->setInfosSortie('découverte de la fameuse rue de la soif de Rennes');
        $sortieBistrot->setEtat($etatOuvert);
        $sortieBistrot->setOrganisateur($participantSofian);
        $sortieBistrot->setCampus($campusChartresDeBretagne);
        $sortieBistrot->setLieu($lieuRueDeLaSoif);
        $manager->persist($sortieBistrot);

        $sortieCinema = new Sortie();
        $sortieCinema->setNom('Sortie movies entre potes :)');
        $sortieCinema->setDateHeureDebut(new \DateTime('now + 20 day'));
        $sortieCinema->setDuree(3);
        $sortieCinema->setDateLimiteInscription(new \DateTime('now + 19 day'));
        $sortieCinema->setNbInscriptionsMax(40);
        $sortieCinema->setInfosSortie('Sortie ciné sur la Roche sur Yon, film au choix!');
        $sortieCinema->setEtat($etatOuvert);
        $sortieCinema->setOrganisateur($participantKamel);
        $sortieCinema->setCampus($campusLaRocheSurYon);
        $sortieCinema->setLieu($lieuCinema);
        $manager->persist($sortieCinema);



        // ENREGISTREMENT EN BDD

        $manager->flush();
    }
}
