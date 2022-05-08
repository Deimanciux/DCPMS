<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\WorkSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkSchedule[]    findAll()
 * @method WorkSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkSchedule::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(WorkSchedule $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(WorkSchedule $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getCreatedWeekdays(User $user): array
    {
        $result =  $this->createQueryBuilder('w')
            ->select('w.weekDay')
            ->where('w.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult()
        ;

        return array_column($result, 'weekDay');
    }
}
