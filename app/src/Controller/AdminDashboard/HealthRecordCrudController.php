<?php

namespace App\Controller\AdminDashboard;

use App\Entity\HealthRecord;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HealthRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HealthRecord::class;
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
