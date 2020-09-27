<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonRepository extends ServiceEntityRepository
{
    /**
     * Paginator limit
     */
    const PAGINATOR_LIMIT = 60;

    /**
     * ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    /**
     * Search 
     * 
     * @return Pokemon[] Array of Pokemons
     */
    public function search($filters)
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.types', 't');

        if (isset($filters['fulltext'])) {
            $query->andWhere('p.name = :val')
                ->setParameter('val', $filters['fulltext']);
        }

        if (isset($filters['dest'])) {
            $query->orderBy('p.name', $filters['dest']);
        }

        if (isset($filters['p'])) {
            $query->setFirstResult($filters['p'] * self::PAGINATOR_LIMIT);
        }

        if (isset($filters['type'])) {
            $query = $query
                ->andWhere('t.name = :type')
                ->setParameter('type', $filters['type']);
        }

        $query->setMaxResults(self::PAGINATOR_LIMIT);
   
        $results = $query->getQuery()
            ->getResult();
       
        return $results;
    }
    
    /**
     * Find One By Name
     * 
     * @param string $name
     * 
     * @return Pokemon|null
     */
    public function findOneByName($value): ?Pokemon
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
