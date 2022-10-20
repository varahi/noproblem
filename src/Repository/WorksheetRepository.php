<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\Worksheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Worksheet>
 *
 * @method Worksheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Worksheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Worksheet[]    findAll()
 * @method Worksheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorksheetRepository extends ServiceEntityRepository
{
    public const TABLE = 'App\Entity\Worksheet';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Worksheet::class);
    }

    public function add(Worksheet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Worksheet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $id
     * @return int|mixed|string
     */
    public function findByUser(int $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select('w')
            ->from(self::TABLE, 'w')
            ->join('w.user', 'u')
            ->where($qb->expr()->eq('u.id', $id))
            ->andWhere($expr->neq('w.hidden', 1));
        //->andWhere('j.status LIKE :status')
        //->setParameter('status', $status)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Job $job
     */
    public function findByParams($city, $district)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select('w')
            ->from(self::TABLE, 'w')
        ;

        if ($city && $district) {
            $qb->andWhere($qb->expr()->eq('w.city', $city->getId()));
            $qb->andWhere($qb->expr()->eq('w.district', $district->getId()));
        } elseif ($city) {
            $qb->andWhere($qb->expr()->eq('w.city', $city->getId()));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $id
     * @return int|mixed|string
     */
    public function findByCategory(int $id, $currentJob, $limit)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select('w')
            ->from(self::TABLE, 'w')
            ->join('w.category', 'c')
            ->where($qb->expr()->eq('c.id', $id))
            ->setMaxResults($limit)
            ->orderBy('w.created', 'DESC');

        if ($currentJob) {
            $qb->andWhere($expr->neq('w.id', $currentJob));
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Worksheet[] Returns an array of Worksheet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Worksheet
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
