<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                         'message' => 'Please enter a name',
                     ]),
                    new Length([
                       'min' => 2,
                       'minMessage' => 'Your name should be at least {{ limit }} characters',
                       'max' => 50,
                    ]),
                ],
            ])
            ->add('surname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                    'message' => 'Please enter a surname',
                ]),
                    new Length([
                    'min' => 6,
                    'minMessage' => 'Your surname should be at least {{ limit }} characters',
                    'max' => 4096,
                   ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a phone',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your phone should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('personalCode', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a personal code',
                    ]),
                    new Length([
                    'min' => 6,
                    'minMessage' => 'Your personal code should be at least {{ limit }} characters',
                    'max' => 4096,
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Your plain password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                       'message' => 'You should agree to our terms.',
                   ]),
                ],
            ])
            ->add('Register', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
