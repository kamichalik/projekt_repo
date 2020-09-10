<?php
/**
 * Profile type.
 */

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProfileType.
 */
class ProfileType extends \Symfony\Component\Form\AbstractType
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
                'save',
                SubmitType::class,
                [
                    'label' => 'action.save',
                ]
            );
    }
}
