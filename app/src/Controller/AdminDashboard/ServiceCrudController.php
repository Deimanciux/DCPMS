<?php

namespace App\Controller\AdminDashboard;

use App\Entity\Service;
use App\Form\ServiceImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Service::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('description'),
            NumberField::new('price'),
            BooleanField::new('isActive'),
            NumberField::new('duration'),
            CollectionField::new('serviceImages')
                ->setEntryType(ServiceImageType::class)
                ->setFormTypeOption('by_reference', false)
                ->onlyOnForms(),
            CollectionField::new('serviceImages')
                ->setTemplatePath('admin/service_images.html.twig')
                ->onlyOnDetail()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        parent::configureActions($actions);

        return $actions->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL);
    }
}
