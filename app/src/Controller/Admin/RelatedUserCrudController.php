<?php

namespace App\Controller\Admin;

use App\Entity\RelatedUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RelatedUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RelatedUser::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
