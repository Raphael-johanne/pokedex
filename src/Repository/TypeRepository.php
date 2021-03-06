<?php

namespace App\Repository;

use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Type|null find($id, $lockMode = null, $lockVersion = null)
 * @method Type|null findOneBy(array $criteria, array $orderBy = null)
 * @method Type[]    findAll()
 * @method Type[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Type::class);
    }

    /**
     * Retrieve Pokemon's type
     * 
     * @return Type[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find One By Name
     * 
     * @param string $name
     * 
     * @return Type|null
     */
    public function findOneByName($value): ?Type
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
