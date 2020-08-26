<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posting;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\PostingType;
use App\Form\ProfileType;
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
     * @Route("/posting/{id}", name="posting")
     */
    public function show($id)
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
     * @Route("/create", name="posting_create")
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
     *
     * @return Posting
     */
    private function getPosting($id)
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
    public function activate($id)
    {
        $posting = $this->getPosting($id);
        $posting->setIsActive(1);
        $this->persist($posting);

        return new RedirectResponse('/all');
    }

    /**
     * @Route("/postings-in-category/{id}", name="postings_in_category")
     */
    public function categoryPostings($id)
    {
        $postings = $this->getRepository()->findBy(['is_active' => 1, 'category' => $id], ['id' => 'desc']);

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        return $this->renderPostings($postings, 1, $categoryRepository->find($id));
    }

    /**
     * @param $pageNumber
     */
    private function renderPostings(array $postings, $pageNumber, $currentCategory = null): \Symfony\Component\HttpFoundation\Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([], ['id' => 'desc']);

        return $this->render('posting/index.html.twig', [
            'postings' => $postings,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'pageNumber' => $pageNumber,
        ]);
    }

    /**
     * @Route("/user-edit", name="user_edit")
     */
    public function userEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $error = '';

        $emailForm = $this->createForm(ProfileType::class, ['email' => $this->getUser()->getUsername()]);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        $emailForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($this->getUser());

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $user->setEmail($emailForm->getData()['email']);
            $this->persist($user);

            $this->addFlash('success', 'Zmieniono email użytkownika');
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $newPassword = $data['password'];

            if (!$passwordEncoder->isPasswordValid($this->getUser(), $data['currentPassword'])) {
                $error = 'Nieprawidłowe hasło';
            } elseif ($newPassword !== $data['repeatPassword']) {
                $error = 'Podane hasła nie są takie same';
            } else {
                $user->setPassword($passwordEncoder->encodePassword($this->getUser(), $newPassword));
                $this->persist($user);
            }
        }

        return $this->render('security/_profileform.html.twig', [
            'profileForm' => $emailForm->createView(),
            'changePasswordForm' => $passwordForm->createView(),
            'passwordError' => $error,
        ]);
    }
}
