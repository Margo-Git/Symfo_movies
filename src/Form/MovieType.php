<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Repository\GenreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            // généré par le code :
            // ->add('createdAt')
            // ->add('updatedAt')
            ->add('releaseDate', null, [
                'years' => range(date('Y') - 100, date('Y') + 10),
                // this is actually the default format for single_text
                'widget' => 'single_text',
            ])
            ->add('duration')
            ->add('poster', UrlType::class)
            // calculé en front
            // ->add('rating')
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'multiple' => true,
                'choice_label' => 'name',
                // Un élément HTML par choix
                'expanded' => true,
                // custom request en option du champs de form
                'query_builder' => function (GenreRepository $gr) {
                    return $gr->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
