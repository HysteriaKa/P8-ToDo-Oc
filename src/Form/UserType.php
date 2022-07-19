<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control']
                ],
                [
                    'label' => "Nom d'utilisateur"
                ]
            )
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control']
                ],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau','attr' => ['class' => 'form-control']]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ], ['label' => 'Adresse email'])
            ->add('Roles', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
            
                'label' => 'Choisissez son rôle',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Utilisateur' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
            ]);
        // Data transformer
        $builder->get('Roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }
}
