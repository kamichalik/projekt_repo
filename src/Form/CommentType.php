<?php
/**
 * Comment type.
 */

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Posting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CommentType
 */
class CommentType extends AbstractType
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
            'user',
            TextType::class,
                [
                'label' => 'label.user',
                'required' => true,
                'attr' => ['max_length' => 45],
                ]
            )

            ->add(
                'content',
                TextType::class,
                [
                    'label' => 'label.content',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            )

            ->add('posting', EntityType::class, [
                'class' => Posting::class,
                'choice_label' => 'title',
                'label' => 'label.posting',
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
