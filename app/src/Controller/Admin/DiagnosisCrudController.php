<?php

namespace App\Controller\Admin;

use App\Entity\Diagnosis;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DiagnosisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Diagnosis::class;
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
