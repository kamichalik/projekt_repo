<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryService;
use Doctrine\ORM\Entity;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController.
 */
class CategoryController extends AbstractController
{
    /**
     * Category service.
     *
     * @var CategoryService
     */
    private $categoryService;

    /**
     * CategoryController constructor.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Index action.
     *
     * @param Request $request
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $page = $request->query->getInt('page', 1);
        $pagination = $this->categoryService->createPaginatedList($page);

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
            $this->categoryService->save($category);

            $this->addFlash('success', 'message.category_created_successfully');

            return $this->redirectToRoute('categories');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Update action.
     *
     * @param Request  $request
     * @param Category $category
     * @param int      $id
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/category/{id}/update", name="category_update", requirements={"id": "[1-9]\d*"}, methods={"GET","PUT"})
     */
    public function update(Request $request, Category $category, int $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category->getId();
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

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
     * @param Request  $request
     * @param Category $category
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route("/category/{id}/delete", name="category_delete", requirements={"id": "[1-9]\d*"}, methods={"GET", "DELETE"})
     */
    public function delete(Request $request, Category $category)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($category->getPostings()->count()) {
            $this->addFlash('danger', 'message.category_not_empty');

            return $this->redirectToRoute('categories');
        }

        $form = $this->createForm(FormType::class, $category, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);

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
