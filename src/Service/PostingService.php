<?php
/**
 * Posting service.
 */

namespace App\Service;

use App\Entity\Posting;
use App\Repository\PostingRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostingService
 */
class PostingService
{
    /**
     * Posting repository.
     *
     * @var PostingRepository
     */
    private $postingRepository;

    /**
     * Paginator.
     *
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * PostingService constructor.
     *
     * @param PostingRepository  $postingRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(PostingRepository $postingRepository, PaginatorInterface $paginator)
    {
        $this->postingRepository = $postingRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list.
     *
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->postingRepository->queryAll(),
            $page,
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );
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
        $this->postingRepository->save($posting);
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
        $this->postingRepository->delete($posting);
    }
}
