<?php
/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController
 */
class CommentController extends AbstractController
{
    /**
     * Index action.
     *
     * @param CommentRepository $commentRepository
     *
     * @return Response
     *
     * @Route("/comments", name="comments", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
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
     * @param Request $request
     * @param Comment $comment
     *
     * @return Response
     *
     * @Route("/comment/{id}/update", name="comment_update", methods={"GET","PUT"})
     */
    public function update(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'message.comment_updated_successfully');

            return $this->redirectToRoute('comments');
        }

        return $this->render('comment/update.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
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
     * @Route("/comment/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'message.comment_deleted_successfully');
        }

        return $this->redirectToRoute('comments');
    }
}
