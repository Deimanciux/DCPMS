<?php

declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [

                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a email',
                    ]),
                    new Length([
                                   'min' => 2,
                                   'minMessage' => 'Your email should be at least {{ limit }} characters',
                                   'max' => 50,
                               ]),
                ],
            ])
            ->add('password', PasswordType::class, [
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
            ->add('Login', SubmitType::class);
    }
}
