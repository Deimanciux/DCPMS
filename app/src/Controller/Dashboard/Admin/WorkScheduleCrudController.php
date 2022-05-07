<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\WorkSchedule;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WorkScheduleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WorkSchedule::class;
    }
}
