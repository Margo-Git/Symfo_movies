<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre Email',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Critique',
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Appréciation (Note sur 5',
                'choices' => [
                    // label => valeur
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Moyen' => 2,
                    'A Eviter' => 1,
                ],
                'placeholder' => 'Choisir une option',
                // Pas nécessaire ici car valeurs par défaut pour un SELECT,
                // maos pour info :
                // Plusieurs choix possibles ou non
                'multiple' => false,
                // Chaque choix à son widget HTML ou non
                'expanded' => false,
            ])
            ->add('reactions', ChoiceType::class, [
                'label' => 'Ce film vous a fait...',
                'choices' => [
                    // Label => ce qu'on va stocker en base
                    'Rire' => 'smile',
                    'Pleurer' => 'cry',
                    'Réfléchir' => 'think',
                    'Dormir' => 'sleep',
                    'Rêver' => 'dream',
                ],
                // Plusieurs réactions possibles
                'multiple' => true,
                // Une checkbox pour chaque
                'expanded' => true,
            ])
            ->add('watchedAt', DateTimeType::class, [
                'label' => 'Vous avez vu ce film le',
                'input' => 'datetime_immutable',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
