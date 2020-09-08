<?php
/**
 * Category type.
 */

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType
 */
class CategoryType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => ['max_length' => 45],
            ]
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
