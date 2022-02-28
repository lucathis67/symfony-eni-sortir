<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @param UserInterface|null $user
     * @return Sortie[]
     */
    public function findUsingFilter(
        UserInterface $user = null,
        String $contient = null,
        String $campus = null,
        \DateTime $dateHeureDebut = null,
        \DateTime $dateLimiteInscription = null,
        bool $organisee = false,
        bool $inscrit = false,
        bool $nonInscrit = false,
        bool $passees = false,
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('sortie');
        $queryBuilder->select('sortie');


        if ($contient) {
            $queryBuilder
                ->andWhere("sortie.nom LIKE :contient")
                ->setParameter('contient', "%".$contient."%");
        }

        if ($campus) {
            $queryBuilder
                ->andWhere('sortie.campus = :campus')
                ->setParameter('campus', $campus);
        }

        if ($dateHeureDebut) {
            $queryBuilder
                ->andWhere('sortie.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', $dateHeureDebut);
        }

        if ($dateLimiteInscription) {
            $queryBuilder
                ->andWhere('sortie.dateLimiteInscription <= :dateLimiteInscription')
                ->setParameter('dateLimiteInscription', $dateLimiteInscription);
        }

        if ($organisee && $user) {
            $queryBuilder
                ->andWhere('sortie.organisateur = :user')
                ->setParameter('user', $user->getUserIdentifier());
        }

        if ($inscrit && $user) {
            $queryBuilder
                ->andWhere('user MEMBER OF sortie.participants')
                ->setParameter('user', $user->getUserIdentifier());
        }

        if ($nonInscrit && $user) {
            $queryBuilder
                ->andWhere('user NOT MEMBER OF sortie.participants')
                ->setParameter('user', $user->getUserIdentifier());
        }

        if ($passees) {
            $queryBuilder
                ->andWhere('sortie.dateHeureDebut <= :today')
                ->setParameter('today', new \DateTime());
        }

        $queryBuilder->orderBy('sortie.dateHeureDebut', 'ASC');
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
