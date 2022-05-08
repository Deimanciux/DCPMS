<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Tooth;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ToothCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tooth::class;
    }
}
