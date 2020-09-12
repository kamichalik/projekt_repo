<?php
/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostingRepository;
use App\Service\CommentService;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommentController.
 */
class CommentController extends AbstractController
{
    /**
     * Comment service.
     *
     * @var CommentService
     */
    private $commentService;

    /**
     * CommentController constructor.
     *
     * @param CommentService $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Index action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/comments", name="comments", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $page = $request->query->getInt('page', 1);
        $pagination = $this->commentService->createPaginatedList($page);

        return $this->render(
            'comment/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param Request           $request
     * @param PostingRepository $postingRepository
     * @param int               $postingId
     *
     * @return Response
     *
     * @throws Exception
     *
     * @Route("/comment/new/{postingId}", name="comment_create", requirements={"posting_id": "[1-9]\d*"}, methods={"GET","POST"})
     */
    public function create(Request $request, PostingRepository $postingRepository, int $postingId): Response
    {
        $comment = new Comment();
        $posting = $postingRepository->find($postingId);
        $comment->setPosting($posting);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setDate(new DateTime('now'));
            $this->commentService->save($comment);

            $this->addFlash('success', 'message.comment_created_successfully');

            return $this->redirectToRoute('posting', ['id' => $postingId]);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * View action.
     *
     * @param Comment $comment
     *
     * @return Response
     *
     * @Route("/comment/{id}", name="comment_view", requirements={"id": "[1-9]\d*"}, methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/view.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * Update action.
     *
     * @param Request $request
     * @param Comment $comment
     * @param int     $id
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/comment/{id}/update", name="comment_update", requirements={"id": "[1-9]\d*"}, methods={"GET","PUT"})
     */
    public function update(Request $request, Comment $comment, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment->getId();
        $form = $this->createForm(CommentType::class, $comment, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment);

            $this->addFlash('success', 'message.comment_updated_successfully');

            return $this->redirectToRoute('comments');
        }

        return $this->render('comment/update.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request
     * @param Comment $comment
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/comment/{id}/delete", name="comment_delete", requirements={"id": "[1-9]\d*"}, methods={"GET", "DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash('success', 'message.comment_deleted_successfully');

            return $this->redirectToRoute('comments');
        }

        return $this->render(
            'comment/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
