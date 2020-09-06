<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 */
class UserController extends AbstractController
{
    /**
     * Edit user data.
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TranslatorInterface          $translator
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/user/edit", name="user_edit")
     */
    public function userEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $emailForm = $this->createForm(ProfileType::class, ['email' => $this->getUser()->getUsername()], ['method' => 'PUT']);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        $emailForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($this->getUser());

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $user->setEmail($emailForm->getData()['email']);
            $this->persist($user);

            $this->addFlash('success', $translator->trans('account.updated'));
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $newPassword = $data['password'];

            if (!$passwordEncoder->isPasswordValid($this->getUser(), $data['currentPassword'])) {
                $this->addFlash('danger', $translator->trans('log.incorrect_pw'));
            } elseif ($newPassword !== $data['repeatPassword']) {
                $this->addFlash('danger', $translator->trans('log.not_match'));
            } else {
                $user->setPassword($passwordEncoder->encodePassword($this->getUser(), $newPassword));
                $this->persist($user);

                $this->addFlash('success', $translator->trans('account.updated'));
            }
        }

        return $this->render('security/_profileform.html.twig', [
            'profileForm' => $emailForm->createView(),
            'changePasswordForm' => $passwordForm->createView(),
        ]);
    }

    /**
     * @param User $user
     */
    private function persist(User $user): void
    {
        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($user);
        $doctrine->flush();
    }
}
