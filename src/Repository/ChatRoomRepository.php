<?php

namespace App\Repository;

use App\Entity\ChatRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<ChatRoom>
 *
 * @method ChatRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatRoom[]    findAll()
 * @method ChatRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRoomRepository extends ServiceEntityRepository
{
    public const TABLE = 'App\Entity\ChatRoom';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatRoom::class);
    }

    public function add(ChatRoom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChatRoom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array $users
     * @return float|int|mixed|string
     */
    public function findOneByUsers($user1, $user2)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('chr')
            ->from(self::TABLE, 'chr')

            ->join('chr.users', 'u')
            ->where('u.id = :val1')
            ->setParameter('val1', $user1->getId())

            ->leftJoin('chr.users', 'u2')
            ->andWhere('u2.id = :val2')
            ->setParameter('val2', $user2->getId());

        return $qb->getQuery()->getResult();

        //$result = $qb->getQuery()->setMaxResults(1)->getResult();
        //return $result[0];

        /*if(!empty($result)) {
            $result = $qb->getQuery()->setMaxResults(1)->getResult();
            return $result[0];
        } else {
            return null;
        }*/
    }

//    /**
//     * @return ChatRoom[] Returns an array of ChatRoom objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChatRoom
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
