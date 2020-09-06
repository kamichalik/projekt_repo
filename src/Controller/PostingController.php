<?php
/**
 * Posting controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posting;
use App\Form\PostingType;
use App\Repository\CategoryRepository;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PostingController
 */
class PostingController extends AbstractController
{
    /**
     * Index action.
     *
     * @param int $pageNumber
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/postings/{pageNumber}", name="postings", defaults={"pageNumber"=1})
     */
    public function index(int $pageNumber)
    {
        $limit = 10;
        $postings = $this->getRepository()->findBy(
            ['isActive' => 1],
            ['id' => 'desc'],
            $limit,
            ($pageNumber - 1) * $limit
        );

        return $this->renderPostings($postings, $pageNumber, $currentCategory = null);
    }

    /**
     * Index action admin view.
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/all", name="postings_admin")
     */
    public function indexAdmin()
    {
        $postings = $this->getRepository()->findBy([], ['id' => 'desc']);

        return $this->render('posting/indexAdmin.html.twig', [
            'postings' => $postings,
        ]);
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
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/posting/{id}/update", name="posting_update", methods={"GET","PUT"})
     */
    public function update(Request $request, int $id):Response
    {
        $posting = $this->getPosting($id);
        $form = $this->createForm(PostingType::class, $posting, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posting = $form->getData();
            $this->persist($posting);

            $this->addFlash('success', 'message.post_updated_successfully');

            return $this->redirect('/postings');
        }
        $formView = $form->createView();

        return $this->render('posting/update.html.twig', [
            'id' => $id,
            'form' => $formView,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request
     * @param Posting $posting
     *
     * @return Response|RedirectResponse
     *
     * @Route("/posting/{id}/delete", name="posting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Posting $posting)
    {
        if ($this->isCsrfTokenValid('delete'.$posting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($posting);
            $entityManager->flush();

            $this->addFlash('success', 'message.post_deleted_successfully');
        }

        return $this->redirectToRoute('postings_admin');
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

        return new RedirectResponse('/all');
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

        return new RedirectResponse('/all');
    }

    /**
     * View actions in category.
     *
     * @param int $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/{id}/postings", name="postings_in_category")
     */
    public function categoryPostings(int $id)
    {
        $postings = $this->getRepository()->findBy(['isActive' => 1, 'category' => $id], ['id' => 'desc']);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        return $this->renderPostings($postings, 1, $categoryRepository->find($id));
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
     * @param int           $pageNumber
     * @param Category|null $currentCategory
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderPostings(array $postings, int $pageNumber, Category $currentCategory = null): \Symfony\Component\HttpFoundation\Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([], ['id' => 'desc']);

        return $this->render('posting/index.html.twig', [
            'postings' => $postings,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'pageNumber' => $pageNumber,

        ]);
    }
}
