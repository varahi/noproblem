<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public const CATEGORY_TABLE = 'App\Entity\ArticleCategory';

    public const ARTICLE_TABLE = 'App\Entity\Article';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array $order
     * @return Article[]
     */
    public function findAllOrder(array $order)
    {
        return $this->findBy([], $order);
    }

    /**
     * @param int $id
     * @return int|mixed|string
     */
    public function findByCategory(int $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select('article');
        $qb->from(self::ARTICLE_TABLE, 'article');
        $qb->join('article.category', 'cat');
        $qb->where($qb->expr()->eq('cat.id', $id));
        $qb->andWhere($expr->neq('article.hidden', 1));

        return $qb->getQuery()->getResult();
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

        $qb->select('a')
            ->from(self::ARTICLE_TABLE, 'a')
            //->where('r.hidden is not NULL')
            ->where($expr->neq('a.hidden', 1))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('a.id', 'ASC');

        $reviews = $qb->getQuery()->getResult();
        return $reviews;

        //return $this->findBy([], $order);
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
