<?php

namespace App\Repository;

use App\Entity\ArticleCategory;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public const TABLE = 'App\Entity\Task';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function add(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array $order
     * @return Task[]
     */
    public function findAllOrder(array $order)
    {
        return $this->findBy([], $order);
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
            ->where($expr->neq('t.hidden', 1))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('t.name', 'ASC')
        ;

        $reviews = $qb->getQuery()->getResult();
        return $reviews;
    }

    /**
     * @param int $id
     * @return int|mixed|string
     */
    public function findByCategory($category)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select('t')
            ->from(self::TABLE, 't')
            ->join('t.category', 'c')
            ->where($qb->expr()->eq('c.id', $category->getId()))
            ->andWhere($expr->neq('t.hidden', 1))
            ->orderBy('t.name', 'DESC');


        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Task[] Returns an array of Task objects
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

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
