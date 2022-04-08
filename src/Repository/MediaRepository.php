<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Media $entity, bool $flush = true): void
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
    public function remove(Media $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Media[] Returns an array of Media objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * @return Media[] Returns an array of Media objects
     */
    public function getImage($id)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.typeMedia', 'tm')
            ->where('tm.groupMedia = :val1')
            ->setParameter('val1', 'Image')
            ->innerJoin('m.trick', 'tt')
            ->andWhere('tt.id = :val2')
            ->setParameter('val2', $id)
            ->orderBy('m.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Media[] Returns an array of Media objects
     */
    public function getVideo($id)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.typeMedia', 'tm')
            ->where('tm.groupMedia = :val1')
            ->setParameter('val1', 'Vidéo')
            ->innerJoin('m.trick', 'tt')
            ->andWhere('tt.id = :val2')
            ->setParameter('val2', $id)
            ->orderBy('m.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Media
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * updateFeaturePicture
     *
     * @param  mixed $media
     * @return void
     */
    public function updateFeaturePicture(Media $media)
    {
        return $this->createQueryBuilder('e')
            ->update()
            ->set('e.featurePicture', 0)
            ->where('e.id <> :id')
            ->andWhere('e.trick = :trick')
            ->setParameter('trick', $media->getTrick())
            ->setParameter('id', $media->getId())
            ->getQuery()
            ->execute();
    }
}
