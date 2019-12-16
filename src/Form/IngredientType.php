<?php

namespace App\Form;

use App\Entity\Measure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter ingredient title'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an ingredient title',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'ingredientTitle.short',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                        'maxMessage' => 'ingredientTitle.long',
                    ]),
                ],
            ])
            ->add('measure', EntityType::class, [
                'class' => Measure::class,
                'mapped' => false,
            ])
            ->add('amount', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Enter measure amount'
                ],
                'mapped' => false,
                'required' => false,
            ])
        ;
    }
}
