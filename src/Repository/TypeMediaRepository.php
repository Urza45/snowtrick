<?php

namespace App\Repository;

use App\Entity\TypeMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeMedia[]    findAll()
 * @method TypeMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeMedia::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TypeMedia $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(TypeMedia $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
