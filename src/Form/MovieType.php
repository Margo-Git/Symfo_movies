<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            // généré par le code :
            // ->add('createdAt')
            // ->add('updatedAt')
            ->add('releaseDate')
            ->add('duration')
            ->add('poster')
            // calculé en front
            // ->add('rating')
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'multiple' => true,
                'choice_label' => 'name',
                // Un élément HTML par choix
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
