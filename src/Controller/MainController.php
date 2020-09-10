<?php
/**
 * Main controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\PostingRepository;
use http\Env\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MainController.
 */
class MainController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request
     * @param PostingRepository  $postingRepository
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @param Category           $currentCategory
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="postings")
     */
    public function index(Request $request, PostingRepository $postingRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Category $currentCategory = null)
    {
        $categories = $categoryRepository->findBy([], ['id' => 'desc']);

        $postings = $postingRepository->findBy(
            ['isActive' => 1],
            ['id' => 'desc']
        );

        $pagination = $paginator->paginate(
            $postings,
            $request->query->getInt('page', 1),
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('main/index.html.twig', [
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'pagination' => $pagination,
        ]);
    }
}
