<?php

namespace App\Controller\Dashboard\Doctor;

use App\Entity\User;
use App\Entity\WorkSchedule;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class WorkScheduleCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityRepository $entityRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return WorkSchedule::class;
    }

    public function createEntity(string $entityFqcn)
    {
        /**
         * @var WorkSchedule $entity
         */
        $entity = parent::createEntity($entityFqcn);
        $entity->setUser($this->getUser());

        return $entity;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $qb->andWhere('entity.user = :user')->setParameter('user', $this->getUser());
    }

    public function configureFields(string $pageName): iterable
    {
        $weekDays = [
            1 => "Monday",
            2 => "Tuesday",
            3 => "Wednesday",
            4 => "Thursday",
            5 => "Friday",
            6 => "Saturday",
            7 => "Sunday",
        ];

        yield TimeField::new('workFrom');
        yield TimeField::new('workTo');
        yield ChoiceField::new('weekDay')->setChoices(array_flip($weekDays))->renderAsBadges();
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions); // TODO: Change the autogenerated stub
    }


}
