<?php
/**
 * Posting repository.
 */

namespace App\Repository;

use App\Entity\Posting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Posting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posting[]    findAll()
 * @method Posting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * Class PostingRepository
 */
class PostingRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * PostingRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posting::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial posting.{id, title, date, img, isActive, description}',
                'partial category.{id, name}',
                'partial comments.{id}'
            )
            ->join('posting.category', 'category')
            ->leftJoin('posting.comments', 'comments')
            ->orderBy('posting.id', 'DESC');
    }

    /**
     * Save record.
     *
     * @param Posting $posting
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Posting $posting): void
    {
        $this->_em->persist($posting);
        $this->_em->flush($posting);
    }

    /**
     * Delete record.
     *
     * @param Posting $posting
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Posting $posting): void
    {
        $this->_em->remove($posting);
        $this->_em->flush($posting);
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('posting');
    }
}
