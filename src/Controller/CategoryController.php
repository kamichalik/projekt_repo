<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Entity;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use http\Env\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController.
 */
class CategoryController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function index(Request $request, CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pagination = $paginator->paginate(
            $categoryRepository->queryAll(),
            $request->query->getInt('page', 1),
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws Exception
     *
     * @Route("/category/create", name="category_create", methods={"GET", "POST"})
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($category);
            $doctrine->flush();

            $this->addFlash('success', 'message.category_created_successfully');

            return $this->redirect('/categories');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Update action.
     *
     * @param Request            $request
     * @param Category           $category
     * @param CategoryRepository $repository
     * @param int                $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/category/{id}/update", name="category_update", requirements={"id": "[1-9]\d*"}, methods={"GET","PUT"})
     */
    public function update(Request $request, Category $category, CategoryRepository $repository, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category->getId();
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($category);

            $this->addFlash('success', 'message.category_updated_successfully');

            return $this->redirectToRoute('categories');
        }

        return $this->render('category/update.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request            $request
     * @param Category           $category
     * @param CategoryRepository $repository
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/category/{id}/delete", name="category_delete", requirements={"id": "[1-9]\d*"}, methods={"GET", "DELETE"})
     */
    public function delete(Request $request, Category $category, CategoryRepository $repository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($category->getPostings()->count()) {
            $this->addFlash('danger', 'message.category_not_empty');

            return $this->redirectToRoute('categories');
        }

        $form = $this->createForm(CategoryType::class, $category, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($category);

            $this->addFlash('success', 'message.category_deleted_successfully');

            return $this->redirectToRoute('categories');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
