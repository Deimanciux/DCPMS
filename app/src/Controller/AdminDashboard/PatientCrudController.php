<?php

namespace App\Controller\AdminDashboard;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class PatientCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityRepository $entityRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
       $qb = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

       return $qb->andWhere('entity.roles like :role')
           ->setParameter('role', '%'.User::ROLE_PATIENT.'%');
    }

    public function configureActions(Actions $actions): Actions
    {
        parent::configureActions($actions);

        $viewHealthRecords = Action::new('viewHealthRecords', 'Health records')
            ->linkToRoute('app_health_records_by_patient', function (User $user): array {
                return [
                    'user'   => $user->getId(),
                ];
            });

        return $actions->add(Crud::PAGE_INDEX, $viewHealthRecords);
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
