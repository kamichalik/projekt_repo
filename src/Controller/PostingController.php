<?php
/**
 * Posting controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posting;
use App\Form\PostingType;
use App\Repository\PostingRepository;
use Doctrine\ORM\ORMException;
use http\Env\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PostingController
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="postings")
     */
    public function index(Request $request, PostingRepository $postingRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $postingRepository->queryAll(),
            $request->query->getInt('page', 1),
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        $postings = $this->getRepository()->findBy(
            ['isActive' => 1],
            ['id' => 'desc'],
            ['pagination' => $pagination]
        );

        return $this->renderPostings($postings, $currentCategory = null, $pagination);
    }

    /**
     * Index action admin view.
     *
     * @param Request            $request
     * @param PostingRepository  $postingRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/postings", name="postings_admin")
     */
    public function indexAdmin(Request $request, PostingRepository $postingRepository, PaginatorInterface $paginator)
    {
//        $postings = $this->getRepository()->findBy([], ['id' => 'desc']);
//
//        return $this->render('posting/indexAdmin.html.twig', [
//            'postings' => $postings,
//        ]);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pagination = $paginator->paginate(
            $postingRepository->queryAll(),
            $request->query->getInt('page', 1),
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'posting/indexAdmin.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param int $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/posting/{id}", name="posting", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $posting = $this->getPosting($id);

        return $this->render('posting/view.html.twig', [
            'post' => $posting,
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     *
     * @Route("/posting/create", name="posting_create")
     */
    public function create(Request $request)
    {
        $posting = new Posting();
        $form = $this->createForm(PostingType::class, $posting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $posting = $form->getData();
            $posting->setIsActive(0);
            $posting->setDate(new \DateTime('now'));
            $this->persist($posting);

            $this->addFlash('success', 'message.post_created_successfully');

            return $this->redirect('/postings');
        }
        $formView = $form->createView();

        return $this->render('posting/create.html.twig', [
            'form' => $formView,
        ]);
    }

    /**
     * Update action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param int                                       $id      Posting id
     * @param Posting                                   $posting
     * @param PostingRepository                         $repository
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/posting/{id}/update", name="posting_update", requirements={"id": "[1-9]\d*"}, methods={"GET","PUT"})
     */
    public function update(Request $request, Posting $posting, PostingRepository $repository, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $posting = $this->getPosting($id);
        $form = $this->createForm(PostingType::class, $posting, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($posting);

            $this->addFlash('success', 'message.post_updated_successfully');

            return $this->redirect('/postings');
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
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return Response|RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @Route("/{id}/activate", name="activate")
     */
    public function activate(int $id): RedirectResponse
    {
        $posting = $this->getPosting($id);
        $posting->setIsActive(1);
        $this->persist($posting);

        $this->addFlash('success', 'message.post_activated');

        return new RedirectResponse('/postings');
    }

    /**
     * Dectivate posting.
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @Route("/{id}/deactivate", name="deactivate")
     */
    public function deactivate(int $id): RedirectResponse
    {
        $posting = $this->getPosting($id);
        $posting->setIsActive(0);
        $this->persist($posting);

        $this->addFlash('warning', 'message.post_deactivated');

        return new RedirectResponse('/postings');
    }

    /**
     * View actions in category.
     *
     * @param Request            $request
     * @param PostingRepository  $postingRepository
     * @param PaginatorInterface $paginator
     * @param int                $pageNumber
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/{id}/postings", name="postings_in_category")
     */
    public function categoryPostings(Request $request, PostingRepository $postingRepository, PaginatorInterface $paginator, int $id)
    {
        $pagination = $paginator->paginate(
            $postingRepository->queryAll(),
            $request->query->getInt('page', 1),
            PostingRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        $postings = $this->getRepository()->findBy(
            ['isActive' => 1, 'category' => $id],
            ['id' => 'desc']
        );

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        return $this->renderPostings($postings, 1, $categoryRepository->find($id), $pagination);
    }

    /**
     * Repository getter.
     *
     * @return \Doctrine\Persistence\ObjectRepository
     */
    private function getRepository(): \Doctrine\Persistence\ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Posting::class);
    }

    /**
     * Posting getter.
     *
     * @param int $id
     *
     * @return Posting|object
     */
    private function getPosting($id)
    {
        $doctrineRepo = $this->getRepository();
        $posting = $doctrineRepo->find($id);

        return $posting;
    }

    /**
     * Persist.
     *
     * @param $posting
     */
    private function persist($posting): void
    {
        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($posting);
        $doctrine->flush();
    }

    /**
     * Render postings.
     *
     * @param array         $postings
     * @param Category|null $currentCategory
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    private function renderPostings(array $postings, Category $currentCategory = null, $pagination)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([], ['id' => 'desc']);

        return $this->render('posting/index.html.twig', [
            'postings' => $postings,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'pagination' => $pagination,

        ]);
    }
}
