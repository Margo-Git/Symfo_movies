<?php

namespace App\Form;

use PDO;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // erreur array to string conversion avec la config par défaut =>
            // ->add('roles')
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    // label => valeur
                    'Administrateur' => 'ROLE_ADMIN',
                    'Manager' => 'ROLE_MANAGER',
                    'Membre' => 'ROLE_USER',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe saisis ne correspondent pas',
                // pour ne pas mapper sur l'entité
                'mapped' => false,
                'options' => ['attr' => ['class' => 'password-field']],
                // 'required' => true,
                'first_options'  => [
                  'constraints' => [
                    new Regex('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&-\/])[A-Za-z\d@$!%*#?&-\/]{8,}$/'),
                ],
                  'label' => 'Mot de passe',
                  'help' => 'Minimum eight characters, at least one letter, one number and one special character.',
                  'attr' => [
                    'placeholder' => 'Laissez vide si inchangé...',
                ],
                ],
                'second_options' => ['label' => 'Répeter le mot de passe'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
