<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Job;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 *
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public const TABLE = 'App\Entity\Job';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function add(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array $order
     * @return Job[]
     */
    public function findAllOrder(array $order)
    {
        return $this->findBy([], $order);
    }

    /**
     * @param int $id
     * @return int|mixed|string
     */
    public function findByUser(int $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('j')
            ->from(self::TABLE, 'j')
            ->join('j.owner', 'o')
            ->where($qb->expr()->eq('o.id', $id))
            //->andWhere('j.status LIKE :status')
            //->setParameter('status', $status)
        ;

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
        $qb->select('j')
            ->from(self::TABLE, 'j')
            ->join('j.category', 'c')
            ->where($qb->expr()->eq('c.id', $id))
            ->setMaxResults($limit)
            ->orderBy('j.created', 'DESC');

        if ($currentJob) {
            $qb->andWhere($expr->neq('j.id', $currentJob));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Job $job
     */
    public function findByParams($category, $city, $district, $tasks)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('j')
            ->from(self::TABLE, 'j')
            ->orderBy('j.created', 'DESC')
            ;

        if ($category) {
            $qb->join('j.category', 'c')
                ->where($qb->expr()->eq('c.id', $category->getId()));
        }

        if ($city && $district) {
            $qb->andWhere($qb->expr()->eq('j.city', $city->getId()));
            $qb->andWhere($qb->expr()->eq('j.district', $district->getId()));
        } elseif ($city) {
            $qb->andWhere($qb->expr()->eq('j.city', $city->getId()));
        }

        if ($tasks) {
            $qb
                ->leftJoin('j.tasks', 't')
                ->andWhere($qb->expr()->in('t.id', [$tasks]));
        }

        return $qb->getQuery()->getResult();
    }

    public function findSelectedProfiles($user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('j')
            ->from(self::TABLE, 'j')
            ->join('j.featuredUsers', 'u')
            ->where($qb->expr()->eq('u.id', $user->getId()))
        ;

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Job[] Returns an array of Job objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Job
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
