<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Diagnosis;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DiagnosisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Diagnosis::class;
    }
}
