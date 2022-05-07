<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\HealthRecord;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HealthRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HealthRecord::class;
    }
}
