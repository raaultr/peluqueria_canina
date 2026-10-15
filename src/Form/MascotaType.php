<?php

namespace App\Form;

use App\Entity\Mascota;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MascotaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('raza')
            ->add('sexo')
            ->add('fechaNacimiento')
            ->add('peso')
            ->add('tamano')
            ->add('foto')
            ->add('nota')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('updatedAt')
            ->add('cliente', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
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
