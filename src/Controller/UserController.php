<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Update user data.
     *
     * @param Request                      $request
     * @param UserRepository               $repository
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/user/update", name="user_update", methods={"GET", "POST"})
     */
    public function update(Request $request, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $emailForm = $this->createForm(ProfileType::class, ['email' => $this->getUser()->getUsername()], ['method' => 'PUT']);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        $emailForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        $user = $repository->find($this->getUser());

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $user->setEmail($emailForm->getData()['email']);
            $repository->save($user);

            $this->addFlash('success', 'account.updated');
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $newPassword = $data['password'];

            if (!$passwordEncoder->isPasswordValid($this->getUser(), $data['currentPassword'])) {
                $this->addFlash('danger', 'log.incorrect_pw');
            } elseif ($newPassword !== $data['repeatPassword']) {
                $this->addFlash('danger', 'log.not_match');
            } else {
                $user->setPassword($passwordEncoder->encodePassword($this->getUser(), $newPassword));

                $this->addFlash('success', 'account.updated');
            }
            $repository->save($user);
        }

        return $this->render('security/_profileform.html.twig', [
            'profileForm' => $emailForm->createView(),
            'changePasswordForm' => $passwordForm->createView(),
        ]);
    }
}
