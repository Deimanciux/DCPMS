<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\ClinicWorker;

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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DoctorsCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityRepository $entityRepository,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $qb->andWhere('entity.roles like :role')->setParameter('role', '%'.User::ROLE_DOCTOR.'%');
    }

    public function configureActions(Actions $actions): Actions
    {
        parent::configureActions($actions)->remove(Action::INDEX, Action::BATCH_DELETE)
            ->remove(Action::INDEX, Action::EDIT)
            ->remove(Action::INDEX, Action::NEW)
            ->remove(Action::INDEX, Action::DELETE);

        $viewReservations = Action::new('viewReservations', 'Reservation')
        ->linkToRoute('reservation_by_user', function (User $user): array {
           return [
               'user' => $user->getId(),
           ];
        });

        return $actions->add(Crud::PAGE_INDEX, $viewReservations);
    }
}
