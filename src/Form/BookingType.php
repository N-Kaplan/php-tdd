<?php

namespace App\Form;

use App\Entity\Bookings;
use App\Entity\Room;
use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class BookingType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, array(
                'widget' => 'choice',
                 'years' => range(date('Y'), date('Y') . 5),
               ))
            ->add('endDate', DateTimeType::class, array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') . 5),
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bookings::class,
        ]);
    }
}
