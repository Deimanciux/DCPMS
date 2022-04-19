<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('datetime', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'title'
            ])
            ->add('save', SubmitType::class)
            ->add('close', ButtonType::class, [
                'attr' => [
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
