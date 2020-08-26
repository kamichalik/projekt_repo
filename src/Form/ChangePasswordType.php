<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('currentPassword', PasswordType::class)
            ->add('password', PasswordType::class)
            ->add('repeatPassword', PasswordType::class)
            ->add('save', SubmitType::class);
    }
}
