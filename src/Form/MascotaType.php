<?php

namespace App\Form;

use App\Entity\Mascota;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MascotaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'attr' => ['placeholder' => 'Nombre de la mascota'],
            ])
            ->add('raza', TextType::class, [
                'attr' => ['placeholder' => 'Raza'],
            ])
            ->add('sexo', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Macho / Hembra'],
            ])
            ->add('fechaNacimiento', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('peso', NumberType::class, [
                'required' => false,
                'scale' => 1,
                'attr' => ['min' => 0, 'step' => '0.1', 'placeholder' => 'Peso en kg'],
            ])
            ->add('tamano', TextType::class, [
                'attr' => ['placeholder' => 'Pequeño, mediano o grande'],
            ])
            ->add('foto', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Ruta o URL de la fotografía'],
            ])
            ->add('nota', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 3, 'placeholder' => 'Notas adicionales'],
            ])
            ->add('cliente', EntityType::class, [
                'class' => User::class,
                'choice_label' => static fn (User $user): string => trim($user->getNombre().' '.$user->getApellido()),
                'placeholder' => 'Selecciona un cliente',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mascota::class,
        ]);
    }
}
