<?php

namespace App\Repository;

use App\Entity\Tariff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tariff>
 *
 * @method Tariff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tariff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tariff[]    findAll()
 * @method Tariff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TariffRepository extends ServiceEntityRepository
{
    public const TABLE = 'App\Entity\Tariff';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tariff::class);
    }

    public function add(Tariff $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tariff $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $limit
     * @param $offset
     * @return float|int|mixed|string
     */
    public function findLimitOrder($limit, $offset)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();

        $qb->select('t')
            ->from(self::TABLE, 't')
            //->where('r.hidden is not NULL')
            ->where($expr->neq('t.hidden', 1))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('t.id', 'ASC');

        $reviews = $qb->getQuery()->getResult();
        return $reviews;

        //return $this->findBy([], $order);
    }

    /**
     * @param $limit
     * @param $offset
     * @param $type
     * @return float|int|mixed|string
     */
    public function findLimitOrderAndType($limit, $offset, $type)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();

        $qb->select('t')
            ->from(self::TABLE, 't')
            //->where('r.hidden is not NULL')
            ->where($expr->neq('t.hidden', 1))
            ->andWhere($qb->expr()->eq('t.type', $type))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('t.id', 'ASC');

        $reviews = $qb->getQuery()->getResult();
        return $reviews;

        //return $this->findBy([], $order);
    }

//    /**
//     * @return Tariff[] Returns an array of Tariff objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tariff
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
