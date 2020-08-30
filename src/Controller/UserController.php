<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/user/edit", name="user_edit")
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

    /**
     * @param $user
     */
    private function persist($user): void
    {
        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($user);
        $doctrine->flush();
    }
}
