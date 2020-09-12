<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService
 */
class CommentService
{
    /**
     * Comment repository.
     *
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * Paginator.
     *
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * CommentService constructor.
     *
     * @param CommentRepository  $commentRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
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
            $this->commentRepository->queryAll(),
            $page,
            CommentRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save category.
     *
     * @param Comment $comment
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete category.
     *
     * @param Comment $comment
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}
