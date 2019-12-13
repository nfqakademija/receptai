<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Measure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter ingredient title'
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
