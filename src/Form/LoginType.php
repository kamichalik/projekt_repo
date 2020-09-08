<?php
/**
 * Login type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginType
 */
class LoginType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'label.email',
                    'required' => true,
                    'attr' => ['max_length' => 45],
                ]
            )

            ->add(
                'passsword',
                PasswordType::class,
                [
                    'label' => 'label.password',
                    'required' => true,
                    'attr' => ['max_length' => 45],
                ]
            )

            ->add(
                'logIn',
                SubmitType::class,
                [
                    'label' => 'action.login',
                ]
            );
    }
}
