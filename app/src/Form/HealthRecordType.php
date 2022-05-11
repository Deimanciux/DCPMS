<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Diagnosis;
use App\Entity\HealthRecord;
use App\Entity\Tooth;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HealthRecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tooth', EntityType::class, [
                'class' => Tooth::class,
                'choice_label' => function(?Tooth $tooth) {
                    return sprintf("[%s] %s", $tooth->getPosition()->getPosition(), $tooth->getPosition()->getTitle());
                }
            ])
            ->add('diagnosis', EntityType::class, [
                'class' => Diagnosis::class,
                'choice_label' => function(?Diagnosis $diagnosis) {
                    return $diagnosis->getCode() . ' ' . $diagnosis->getTitle();
                }
            ])
            ->add('isRemoved', CheckboxType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('notes', TextType::class)
            ->add('user', HiddenType::class, [
                'mapped' => false
            ])
            ->add('id', HiddenType::class)
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => HealthRecord::class,
        ]);
    }
}
