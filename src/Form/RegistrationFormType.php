<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ])
            ->add('nombre', null, [
                'label' => 'Nombre',
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 100),
                ],
            ])
            ->add('apellido', null, [
                'label' => 'Apellido',
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 150),
                ],
            ])
            ->add('telefono', null, [
                'label' => 'Teléfono',
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 20),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Acepto los términos y condiciones',
                'constraints' => [
                    new IsTrue(message: 'Debes aceptar los términos y condiciones.'),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'invalid_message' => 'Las contraseñas no coinciden.',
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Confirmar contraseña'],
                'constraints' => [
                    new NotBlank(
                        message: 'Introduce una contraseña.',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres.',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
