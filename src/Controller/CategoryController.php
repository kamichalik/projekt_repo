<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->add('save', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($category);
            $doctrine->flush();

            return $this->redirect('/categories');
        }
        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'categoryForm' => $formView,
        ]);
    }

    /**
     * @Route("/category/{pageNumber}", name="categories", defaults={"pageNumber"=1})
     */
    public function index($pageNumber)
    {
        $limit = 3;
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(
            [],
            ['id' => 'desc'],
            $limit,
            ($pageNumber - 1) * $limit
        );

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'pageNumber' => $pageNumber,
        ]);
    }

    /**
     * @Route("/category/{id}/update", name="category_update")
     */
    public function update(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = $this->getCategory();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($category);
            $doctrine->flush();

            return $this->redirect('/categories');
        }

        $formView = $form->createView();

        return $this->render('category/update.html.twig', [
            'categoryForm' => $formView,
        ]);
    }

    private function getRepository(): \Doctrine\Persistence\ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Category::class);
    }

    /**
     * @param $id
     *
     * @return Category
     */
    private function getCategory($id)
    {
        $doctrineRepo = $this->getRepository();
        $category = $doctrineRepo->find($id);

        return $category;
    }

    /**
     * @param $category
     */
    private function persist($category): void
    {
        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($category);
        $doctrine->flush();
    }

    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            if (count($category->getPostings()) > 0) {
                $this->addFlash('danger', 'Kategoria niepusta');
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($category);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('categories');
    }
}
