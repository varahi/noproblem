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
    public function findByParams($category, $tasks, $city, $citizen, $age, $now, $payment, $district, $busyness)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        //$expr = $qb->expr();
        $qb->select('w')
            ->from(self::TABLE, 'w')
            ->orderBy('w.created', 'DESC')
        ;

        if ($category) {
            $qb->join('w.category', 'c')
                ->where($qb->expr()->eq('c.id', $category->getId()));
        }

        if ($city && $district) {
            $qb->andWhere($qb->expr()->eq('w.city', $city->getId()));
            $qb->andWhere($qb->expr()->eq('w.district', $district->getId()));
        } elseif ($city) {
            $qb->andWhere($qb->expr()->eq('w.city', $city->getId()));
        }

        if ($tasks) {
            $qb->leftJoin('w.tasks', 't')
                ->andWhere($qb->expr()->in('t.id', $tasks));
        }

        if ($citizen) {
            $qb->leftJoin('w.citizen', 'citizen')
                ->andWhere($qb->expr()->in('citizen.id', [$citizen->getId()]));
        }

        if ($age) {
            $qb->andWhere($qb->expr()->gte('w.age', $age));
        }

        if ($busyness) {
            $qb->leftJoin('w.busynesses', 'b')
                ->andWhere($qb->expr()->eq('b.id', $busyness));
        }

        $qb->andWhere($qb->expr()->eq('w.startNow', $now));

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function findByCategory($id, $currentJob, $limit)
    {
        if ($id == null) {
            return null;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select('w')
            ->from(self::TABLE, 'w')
            ->join('w.category', 'c')
            ->where($qb->expr()->eq('c.id', $id))
            ->andWhere($expr->neq('w.hidden', 1))
            ->setMaxResults($limit)
            ->orderBy('w.created', 'DESC');

        if ($currentJob) {
            $qb->andWhere($expr->neq('w.id', $currentJob));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $order
     * @return Worksheet[]
     */
    public function findAllOrder(array $order)
    {
        return $this->findBy([], $order);
    }

    public function findSelectedProfiles($user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('w')
            ->from(self::TABLE, 'w')
            ->join('w.featuredUsers', 'u')
            ->where($qb->expr()->eq('u.id', $user->getId()))
        ;

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
