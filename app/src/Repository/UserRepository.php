<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getDoctorsQuery()
    {
        return $this->createQueryBuilder('entity')
             ->where('entity.roles like :role')
            ->setParameter('role', '%'.User::ROLE_DOCTOR.'%');
    }
}
