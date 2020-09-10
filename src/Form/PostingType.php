<?php
/**
 * Posting type.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Posting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PostingType.
 */
class PostingType extends AbstractType
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
                'title',
                TextType::class,
                [
                    'label' => 'label.posting_title',
                    'required' => true,
                    'attr' => ['max_length' => 45],
                ]
            )

            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label' => 'label.category',
                    'required' => true,
                ]
            )

            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'label.description',
                    'required' => true,
                ]
            )

            ->add(
                'img',
                UrlType::class,
                [
                    'label' => 'label.img',
                    'required' => false,
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Posting::class,
        ]);
    }
}
