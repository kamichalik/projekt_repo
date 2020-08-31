<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController
 */
class CategoryController extends AbstractController
{

    /**
     * Index action.
     *
     * @param int $pageNumber
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route("/categories/{pageNumber}", name="categories", defaults={"pageNumber"=1})
     */
    public function index(int $pageNumber):\Symfony\Component\HttpFoundation\Response
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
     * Create action.
     *
     * @param Request             $request
     * @param TranslatorInterface $translator
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request, TranslatorInterface $translator)
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

            $this->addFlash('success', $translator->trans('category.created'));

            return $this->redirect('/categories');
        }
        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'categoryForm' => $formView,
        ]);
    }


    /**
     * @param Request             $request
     * @param TranslatorInterface $translator
     * @param int                 $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/{id}/update", name="category_update", methods={"GET","PUT"})
     */
    public function update(Request $request, TranslatorInterface $translator, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = $this->getCategory($id);
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($category);
            $doctrine->flush();

            $this->addFlash('success', $translator->trans('category.updated'));

            return $this->redirect('/categories');
        }

        $formView = $form->createView();

        return $this->render('category/update.html.twig', [
            'categoryForm' => $formView,
        ]);
    }


    /**
     * Delete action.
     *
     * @param Request             $request
     * @param TranslatorInterface $translator
     * @param Category            $category
     *
     * @return RedirectResponse
     *
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request, TranslatorInterface $translator, Category $category): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            if (count($category->getPostings()) > 0) {
                $this->addFlash('danger', $translator->trans('category.not_empty'));
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($category);
                $entityManager->flush();
                $this->addFlash('danger', $translator->trans('category.deleted'));
            }
        }

        return $this->redirectToRoute('categories');
    }

    /**
     * Repository getter.
     *
     * @return \Doctrine\Persistence\ObjectRepository
     */
    private function getRepository(): \Doctrine\Persistence\ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Category::class);
    }

    /**
     * Category getter.
     *
     * @param int $id Cateogry $id
     *
     * @return Category Category entity
     */
    private function getCategory(int $id): Category
    {
        $doctrineRepo = $this->getRepository();
        $category = $doctrineRepo->find($id);

        return $category;
    }
}
