<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\User;
use App\Entity\WorkSchedule;
use App\Repository\UserRepository;
use App\Repository\WorkScheduleRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use Symfony\Component\Security\Core\User\UserInterface;

class WorkScheduleCrudController extends AbstractCrudController
{
    public function __construct(
        private WorkScheduleRepository $repository,
        private UserRepository $userRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return WorkSchedule::class;
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

        $usedWeekdays = $this->repository->getCreatedWeekdays($this->getUser());
        $weekDays = array_filter(
            $weekDays,
            static function(int $key) use ($usedWeekdays) {
                return !in_array($key, $usedWeekdays, true);
            },
            ARRAY_FILTER_USE_KEY
        );

        yield TimeField::new('workFrom');
        yield TimeField::new('workTo');
        yield ChoiceField::new('weekDay')->setChoices(array_flip($weekDays))->renderAsBadges();
        yield AssociationField::new('user')->setQueryBuilder(
            fn (QueryBuilder $queryBuilder) => $queryBuilder
                ->where('entity.roles like :role')
                ->setParameter('role', '%'.User::ROLE_DOCTOR.'%')
        );
    }
}
