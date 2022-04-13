<?php

namespace App\Controller\AdminDashboard;

use App\Entity\WorkSchedule;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WorkScheduleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WorkSchedule::class;
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