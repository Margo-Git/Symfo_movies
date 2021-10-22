<?php

namespace App\Form;

use PDO;
use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
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
            // https://symfony.com/doc/current/form/events.html
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

                // on récupère le form
                // on est dans une fonction , donc pas accçs à $form en dehors
                $form = $event->getForm();

                // on récupère le user
                $user = $event->getData();

                // si nouveau user : il est encore vide, il n'a pas été persisté, ik n'a pas d'id
                if ($user->getId() === null) {

                    // => code add user
                    $form->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les mots de passe saisis ne correspondent pas',
                        'options' => ['attr' => ['class' => 'password-field']],
                        // 'required' => true,
                        'first_options'  => [
                            'label' => 'Mot de passe',
                            'constraints' => new NotBlank(),
                        ],
                        'second_options' => ['label' => 'Répeter le mot de passe'],
                    ]);
                }

                // si user existant : il a un id
                else {

                    // => code edit user
                    $form->add('password', RepeatedType::class, [
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
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
