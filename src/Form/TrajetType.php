<?php

namespace App\Form;

use App\Entity\Trajet;
use App\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TrajetType extends AbstractType
{
    private $transformer;

    public function __construct(DateTimeToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('villeDepart', TextType::class)
            ->add('villeDestination', TextType::class)
            ->add('dateDepart', TextType::class, [
                'attr' => [
                    'data-toggle' => 'flatpickr',
                    'data-enable-time' => 'true'
                ]
            ])
            ->add('dateArrivee', TextType::class, [
                'attr' => [
                    'data-toggle' => 'flatpickr',
                    'data-enable-time' => 'true'
                ]
            ])
            ->add('nombrePlaces', IntegerType::class);

        $builder->get('dateDepart')->addModelTransformer($this->transformer);
        $builder->get('dateArrivee')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trajet::class,
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'validateArrivalDateAfterDepartureDate'],
                ]),
            ],
        ]);
    }

    public function validateArrivalDateAfterDepartureDate(Trajet $trajet, ExecutionContextInterface $context)
    {
        if ($trajet->getDateDepart() && $trajet->getDateArrivee() && $trajet->getDateArrivee() <= $trajet->getDateDepart()) {
            $context->buildViolation('La date d\'arrivée doit être après la date de départ.')
                ->atPath('dateArrivee')
                ->addViolation();
        }
    }
}

