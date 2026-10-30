<?php

namespace App\Form;

use App\Entity\Cita;
use App\Entity\Mascota;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CitaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mascota', EntityType::class, [
                'class' => Mascota::class,
                'choice_label' => static fn (Mascota $mascota): string => $mascota->getNombre().' · '.$mascota->getCliente()?->getNombre(),
                'placeholder' => 'Selecciona una mascota',
            ])
            ->add('trabajador', EntityType::class, [
                'class' => User::class,
                'choice_label' => static fn (User $user): string => trim($user->getNombre().' '.$user->getApellido()),
                'placeholder' => 'Sin asignar',
                'required' => false,
            ])
            ->add('inicio', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('fin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => [
                    'Pendiente' => 'Pendiente',
                    'Confirmada' => 'Confirmada',
                    'En curso' => 'En curso',
                    'Realizada' => 'Realizada',
                    'Cancelada' => 'Cancelada',
                ],
                'placeholder' => 'Selecciona un estado',
            ])
            ->add('notas', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 3, 'placeholder' => 'Notas para esta cita'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Cita::class]);
    }
}