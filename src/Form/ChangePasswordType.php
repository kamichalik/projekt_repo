<?php
/**
 * Change password type.
 */

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChangePasswordType
 */
class ChangePasswordType extends \Symfony\Component\Form\AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('currentPassword', PasswordType::class)
            ->add('password', PasswordType::class)
            ->add('repeatPassword', PasswordType::class)
            ->add('save', SubmitType::class);
    }
}
