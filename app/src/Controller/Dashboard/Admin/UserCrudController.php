<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
        return [
            TextField::new('personalCode'),
            TextField::new('name'),
            TextField::new('surname'),
            TextField::new('phone'),
            TextField::new('email'),
            DateTimeField::new('dateOfBirth'),
            BooleanField::new('isVerified'),
            ImageField::new('avatar')
                ->setBasePath('images/users')
                ->setUploadDir('public/images/users')
        ];
    }
}
