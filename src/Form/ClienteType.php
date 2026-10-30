<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ClienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['autocomplete' => 'given-name', 'placeholder' => 'Nombre'],
                'constraints' => [new NotBlank(), new Length(max: 100)],
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellidos',
                'attr' => ['autocomplete' => 'family-name', 'placeholder' => 'Apellidos'],
                'constraints' => [new NotBlank(), new Length(max: 150)],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'correo@ejemplo.com'],
                'constraints' => [new NotBlank(), new Email(), new Length(max: 180)],
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
                'attr' => ['autocomplete' => 'tel', 'placeholder' => '+34 600 000 000'],
                'constraints' => [new NotBlank(), new Length(max: 20)],
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'required' => false,
                'attr' => ['autocomplete' => 'street-address', 'placeholder' => 'Calle, número, ciudad'],
                'constraints' => [new Length(max: 255)],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => !$isEdit,
                'invalid_message' => 'Las contraseñas no coinciden.',
                'first_options' => [
                    'label' => $isEdit ? 'Nueva contraseña' : 'Contraseña',
                    'attr' => ['autocomplete' => $isEdit ? 'new-password' : 'new-password', 'placeholder' => $isEdit ? 'Dejar vacía para conservarla' : 'Al menos 8 caracteres'],
                ],
                'second_options' => [
                    'label' => 'Repetir contraseña',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Repite la contraseña'],
                ],
                'constraints' => $isEdit ? [
                    new Length(min: 8, max: 4096, minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres.'),
                ] : [
                    new NotBlank(message: 'Introduce una contraseña.'),
                    new Length(min: 8, max: 4096, minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}