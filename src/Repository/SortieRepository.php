<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * @param null|Participant $user
     * @param SearchData $searchData
     * @return Sortie[]
     */
    public function findUsingFilter(SearchData $searchData, null|Participant $user): array
    {

        $queryBuilder = $this
            ->createQueryBuilder('sortie')
            ->select('sortie', 'campus', 'organisateur')
            ->join('sortie.campus', 'campus')
            ->join('sortie.organisateur', 'organisateur')
            ->join('sortie.etat', 'etat')
            ->andWhere("etat.libelle != 'Créée'")
        ;

        if ($user) {
            $queryBuilder = $queryBuilder
                ->orWhere("etat.libelle = 'Créée' AND sortie.organisateur = :user")
                ->setParameter('user', $user->getId(), 'uuid');
            ;
        }

        if (!empty($searchData->contient)) {
            $queryBuilder = $queryBuilder
                ->andWhere("sortie.nom LIKE :contient")
                ->setParameter('contient', "%{$searchData->contient}%");
        }

        if (!empty($searchData->campus)) {
            $queryBuilder = $queryBuilder
                ->andWhere('sortie.campus = :campus')
                ->setParameter('campus', $searchData->campus->getId(), 'uuid');
        }

        if (!empty($searchData->dateHeureDebut)) {
            $queryBuilder = $queryBuilder
                ->andWhere('sortie.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', $searchData->dateHeureDebut);
        }

        if (!empty($searchData->dateLimiteInscription)) {
            $queryBuilder = $queryBuilder
                ->andWhere('sortie.dateLimiteInscription <= :dateLimiteInscription')
                ->setParameter('dateLimiteInscription', $searchData->dateLimiteInscription);
        }

        if (!empty($searchData->organisee && $user)) {
            $queryBuilder = $queryBuilder
                ->andWhere('sortie.organisateur = :user')
                ->setParameter('user', $user->getId(), 'uuid');
        }

        if (!empty($searchData->inscrit && $user)) {
            $queryBuilder = $queryBuilder
                ->andWhere(':user MEMBER OF sortie.participants')
                ->setParameter('user', $user->getId(), 'uuid');
        }

        if (!empty($searchData->nonInscrit && $user)) {
            $queryBuilder = $queryBuilder
                ->andWhere(':user NOT MEMBER OF sortie.participants')
                ->setParameter('user', $user->getId(), 'uuid');
        }

        if (!empty($searchData->passees)) {
            $queryBuilder = $queryBuilder
                ->andWhere('sortie.dateHeureDebut <= :today')
                ->setParameter('today', new \DateTime());
        } else {
            $queryBuilder = $queryBuilder
                ->andWhere('sortie.dateHeureDebut >= :limite')
                ->setParameter('limite', new \DateTime('- 30 days'));
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
