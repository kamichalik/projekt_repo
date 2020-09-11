<?php
/**
 * Posting controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posting;
use App\Form\PostingType;
use App\Repository\CategoryRepository;
use App\Repository\PostingRepository;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use http\Env\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PostingController.
 */
class PostingController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request
     * @param PostingRepository  $postingRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/postings", name="postings_admin")
     */
    public function index(Request $request, PostingRepository $postingRepository, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pagination = $paginator->paginate(
            $postingRepository->queryAll(),
            $request->query->getInt('page', 1),
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'posting/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param Posting $posting
     * @param int     $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/posting/{id}", name="posting", requirements={"id"="\d+"})
     */
    public function view(Posting $posting, int $id)
    {
        $posting->getId();

        return $this->render('posting/view.html.twig', [
            'post' => $posting,
            'posting_id' => $id,
        ]);
    }

    /**
     * Create action.
     *
     * @param Request           $request
     * @param PostingRepository $repository
     *
     * @return Response|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws Exception
     *
     * @Route("/posting/create", name="posting_create", methods={"GET", "POST"})
     */
    public function create(Request $request, PostingRepository $repository)
    {
        $posting = new Posting();
        $form = $this->createForm(PostingType::class, $posting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $posting->setIsActive(0);
            $posting->setDate(new DateTime('now'));
            $repository->save($posting);

            $this->addFlash('success', 'message.post_created_successfully');

            return $this->redirectToRoute('postings');
        }
        $formView = $form->createView();

        return $this->render('posting/create.html.twig', [
            'form' => $formView,
        ]);
    }

    /**
     * Update action.
     *
     * @param Request           $request
     * @param Posting           $posting
     * @param PostingRepository $repository
     * @param int               $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/posting/{id}/update", name="posting_update", requirements={"id": "[1-9]\d*"}, methods={"GET","PUT"})
     */
    public function update(Request $request, Posting $posting, PostingRepository $repository, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $posting->getId();
        $form = $this->createForm(PostingType::class, $posting, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($posting);

            $this->addFlash('success', 'message.post_updated_successfully');

            return $this->redirectToRoute('postings_admin');
        }

        return $this->render('posting/update.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
            'posting' => $posting,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request           $request
     * @param Posting           $posting
     * @param PostingRepository $repository
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/posting/{id}/delete", name="posting_delete", requirements={"id": "[1-9]\d*"}, methods={"GET", "DELETE"})
     */
    public function delete(Request $request, Posting $posting, PostingRepository $repository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PostingType::class, $posting, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($posting);

            $this->addFlash('success', 'message.post_deleted_successfully');

            return $this->redirectToRoute('postings_admin');
        }

        return $this->render(
            'posting/delete.html.twig',
            [
                'form' => $form->createView(),
                'posting' => $posting,
            ]
        );
    }

    /**
     * Activate posting.
     *
     * @param Posting           $posting
     * @param PostingRepository $repository
     *
     * @return Response|RedirectResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/{id}/activate", name="activate", requirements={"id"="\d+"})
     */
    public function activate(Posting $posting, PostingRepository $repository)
    {
        $posting->setIsActive(1);
        $repository->save($posting);

        $this->addFlash('success', 'message.post_activated');

        return $this->redirectToRoute('postings_admin');
    }


    /**
     * Dectivate posting.
     *
     * @param Posting           $posting
     * @param PostingRepository $repository
     *
     * @return Response|RedirectResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/{id}/deactivate", name="deactivate", requirements={"id"="\d+"})
     */
    public function deactivate(Posting $posting, PostingRepository $repository)
    {
        $posting->setIsActive(0);
        $repository->save($posting);

        $this->addFlash('warning', 'message.post_deactivated');

        return $this->redirectToRoute('postings_admin');
    }

    /**
     * View actions in category.
     *
     * @param Request            $request
     * @param PostingRepository  $postingRepository
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @param Category           $currentCategory
     * @param int                $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/{id}/postings", name="postings_in_category")
     */
    public function categoryPostings(Request $request, PostingRepository $postingRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Category $currentCategory, int $id)
    {
        $categories = $categoryRepository->findBy([], ['id' => 'desc']);

        $postings = $postingRepository->findBy(
            ['isActive' => 1, 'category' => $id],
            ['id' => 'desc']
        );

        $pagination = $paginator->paginate(
            $postings,
            $request->query->getInt('page', 1),
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        $categoryRepository->find($id);

        return $this->render('main/index.html.twig', [
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'pagination' => $pagination,
        ]);
    }
}
