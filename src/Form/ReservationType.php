<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->add('nombrePlaces', ChoiceType::class, [
                'label' => 'Nombre de places',
                'choices' => range(1, $options['nombrePlacesDisponibles']),
                'choice_label' => function ($choice, $key, $value) {
                    return $choice;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'nombrePlacesDisponibles' => null,
        ]);
    }
}
