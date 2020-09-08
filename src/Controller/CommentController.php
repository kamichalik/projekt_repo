<?php
/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommentController
 */
class CommentController extends AbstractController
{
    /**
     * Index action.
     *
     * @param CommentRepository  $commentRepository
     * @param int                $pageNumber
     * @param Request            $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     *
     * @Route("/comments/{pageNumber}", name="comments", defaults={"pageNumber"=1}, methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, CommentRepository $commentRepository, int $pageNumber): Response
    {
//        return $this->render('comment/index.html.twig', [
//            'comments' => $commentRepository->findAll(),
//            'pageNumber' => $pageNumber,
//        ]);

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pagination = $paginator->paginate(
            $commentRepository->queryAll(),
            $request->query->getInt('page', 1),
            CommentRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'comment/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/comment/new", name="comment_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setDate(new \DateTime('now'));
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'message.comment_created_successfully');

            return $this->redirectToRoute('comments');
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
     * @Route("/comment/{id}", name="comment_view", methods={"GET"})
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
     * @param Request           $request
     * @param Comment           $comment
     * @param CommentRepository $repository
     * @param int               $id
     *
     * @return \http\Env\Response|Response
     *
     * @Route("/comment/{id}/update", name="comment_update", requirements={"id": "[1-9]\d*"}, methods={"GET","PUT"})
     */
    public function update(Request $request, Comment $comment, CommentRepository $repository, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment = $this->getComment($id);
        $form = $this->createForm(CommentType::class, $comment, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($comment);

            $this->addFlash('success', 'message.comment_updated_successfully');

            return $this->redirect('/comments');
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
     * @param Request           $request
     * @param Comment           $comment
     * @param CommentRepository $repository
     *
     * @return \http\Env\Response|Response
     *
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/comment/{id}/delete", name="comment_delete", requirements={"id": "[1-9]\d*"}, methods={"GET", "DELETE"})
     */
    public function delete(Request $request, Comment $comment, CommentRepository $repository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CommentType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($comment);

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

    /**
     * Repository getter.
     *
     * @return \Doctrine\Persistence\ObjectRepository
     */
    private function getRepository(): \Doctrine\Persistence\ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Comment::class);
    }

    /**
     * Comment getter.
     *
     * @param int $id
     *
     * @return Comment|object
     */
    private function getComment($id)
    {
        $doctrineRepo = $this->getRepository();
        $comment = $doctrineRepo->find($id);

        return $comment;
    }
}
