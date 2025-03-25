<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @param int $limit
     * @return array
     */
    public function findTopNewsWithMostComments(int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->select('n.title', 'COUNT(c.id) AS comment_count')
            ->join('n.comments', 'c')
            ->groupBy('n.id')
            ->orderBy('comment_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
