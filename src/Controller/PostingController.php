<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posting;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\PostingType;
use App\Form\ProfileType;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PostingController extends AbstractController
{
    /**
     * @Route("/postings/{pageNumber}", name="postings", defaults={"pageNumber"=1})
     */
    public function index($pageNumber)
    {
        $limit = 3;
        $postings = $this->getRepository()->findBy(
            ['is_active' => 1],
            ['id' => 'desc'],
            $limit,
            ($pageNumber - 1) * $limit
        );

        return $this->renderPostings($postings, $pageNumber, $currentCategory = null);
    }

    /**
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
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/posting/{id}", name="posting")
     */

    public function show($id): Response
    {
        $posting = $this->getPosting($id);

        return $this->render('posting/view.html.twig', [
            'post' => $posting,
            ]);
    }

    /**
     * @Route("/posting/{id}/update", name="posting_update")
     */
    public function update(Request $request, $id)
    {
        $posting = $this->getPosting($id);
        $form = $this->createForm(PostingType::class, $posting);
//        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posting = $form->getData();
            $this->persist($posting);

            return $this->redirect('postings');
        }
        $formView = $form->createView();

        return $this->render('posting/update.html.twig', [
            'id' => $id,
            'postingForm' => $formView,
        ]);
    }

    /**
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
            $this->persist($posting);

            return $this->redirect('postings');
        }
        $formView = $form->createView();

        return $this->render('posting/create.html.twig', [
            'postingForm' => $formView,
        ]);
    }

    private function getRepository(): \Doctrine\Persistence\ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Posting::class);
    }

    /**
     * @param $id
     * @return Posting
     */

    private function getPosting($id): Posting
    {
        $doctrineRepo = $this->getRepository();
        $posting = $doctrineRepo->find($id);

        return $posting;
    }

    /**
     * @param $posting
     */
    private function persist($posting): void
    {
        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($posting);
        $doctrine->flush();
    }

    /**
     * @Route("/{id}/activate", name="activate")
     */
    public function activate($id): RedirectResponse
    {
        $posting = $this->getPosting($id);
        $posting->setIsActive(1);
        $this->persist($posting);

        return new RedirectResponse('/all');
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response http response
     * @Route("/category/{id}/postings", name="postings_in_category")
     */
    public function categoryPostings(int $id): Response
    {
        $postings = $this->getRepository()->findBy(['is_active' => 1, 'category' => $id], ['id' => 'desc']);

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        return $this->renderPostings($postings, 1, $categoryRepository->find($id));
    }

    /**
     * @param array $postings
     * @param int $pageNumber
     * @param Category|null $currentCategory
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
