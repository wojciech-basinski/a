<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'Login'
                ],
                'label' => 'Login',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Pole loginu nie może być puste']),
                    new Assert\Length([
                        'min' => 6,
                        'max' => 20,
                        'minMessage' => 'Login musi zawierać co najmniej 6 znaków',
                        'maxMessage' => 'Login musi zawierać najwyżej 20 znaków'
                    ])
                ]
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Hasło'
                    ],
                    'label' => 'Hasło'
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Powtórz hasło'
                    ],
                    'label' => 'Powtórz hasło'
                ],
                'invalid_message' => 'Pola hasła muszą być identyczne',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Wartość nie może być pusta']),
                    new Assert\Length(['min' => 8, 'minMessage' => 'Hasło musi zawierać co najmniej 8 znaków'])
                ]
            ])
            ->add('submit', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ],
                'label' => 'Rejestracja'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['create']
        ]);
    }
}