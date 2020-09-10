<?php
/**
 * Change password type.
 */

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChangePasswordType.
 */
class ChangePasswordType extends \Symfony\Component\Form\AbstractType
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
                'currentPassword',
                PasswordType::class,
                [
                    'label' => 'label.password',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            )

            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'label.new_password',
                    'required' => true,
                    'attr' => [
                        'max_length' => 255,
                        'min_length' => 3,
                    ],
                ]
            )
            ->add(
                'repeatPassword',
                PasswordType::class,
                [
                    'label' => 'label.repeat_password',
                    'required' => true,
                    'attr' => [
                        'max_length' => 255,
                        'min_length' => 3,
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'action.save',
                ]
            );
    }
}
