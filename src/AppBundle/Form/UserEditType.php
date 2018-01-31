<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'Login'
                ],
                'label' => 'Login',
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
                'required' => false,
            ])
            ->add('submit', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ],
                'label' => 'Edytuj'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}