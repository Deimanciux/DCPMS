<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = [User::ROLE_PATIENT, User::ROLE_CLINIC_WORKER, User::ROLE_DOCTOR, User::ROLE_ADMIN];

        yield TextField::new('personalCode');
        yield TextField::new('name');
        yield TextField::new('surname');
        yield TextField::new('phone');
        yield TextField::new('email');
        yield DateTimeField::new('dateOfBirth');
        yield BooleanField::new('isVerified');
        yield ImageField::new('avatar')
            ->setBasePath('images/users')
            ->setUploadDir('public/images/users');
        yield ChoiceField::new('roles')
           ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->renderAsBadges();
    }
}
