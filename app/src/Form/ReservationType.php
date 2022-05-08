<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAction($options['action'])
            ->add('reasonOfVisit', TextType::class)
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'title'
            ])
            ->add('doctor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name'
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'input' => 'datetime_immutable',
                'mapped' => false,
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'html5' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('user', EntityType::class, [
                'label' => 'Patient',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.roles like :role')
                        ->setParameter('role', '%'.User::ROLE_PATIENT.'%');
                },
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('id', HiddenType::class)
            ->add('save', SubmitType::class)
            ->add('delete', ButtonType::class, [
                'attr' => [
                    'class' => 'btn-danger',
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
