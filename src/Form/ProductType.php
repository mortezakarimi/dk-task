<?php

namespace App\Form;

use App\Entity\Product;
use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('variant', CollectionType::class, [
                'entry_type' => VariantType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required' => true,
                'attr' => [
                    'class' => 'variant-collection'
                ]
            ])
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary float-left'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
