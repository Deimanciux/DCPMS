<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Reservation $entity, bool $flush = true): void
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
    public function remove(Reservation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getReservationsByDate(User $doctor, \DateTimeImmutable $dateFrom): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.doctor = :doctor')
            ->andWhere('r.startDate >= :from and r.startDate <= :to')
            ->setParameter('doctor', $doctor)
            ->setParameter('from', $dateFrom->format('Y-m-d'). ' 08:00')
            ->setParameter('to', $dateFrom->format('Y-m-d'). ' 18:00')
            ->getQuery()
            ->getResult();
    }

    public function getUserReservationsByDate(User $user, \DateTimeImmutable $dateFrom): array
    {
        return $this->createQueryBuilder('r')
                    ->where('r.user = :user')
                    ->andWhere('r.startDate >= :from and r.startDate <= :to')
                    ->setParameter('user', $user)
                    ->setParameter('from', $dateFrom->format('Y-m-d'). ' 08:00')
                    ->setParameter('to', $dateFrom->format('Y-m-d'). ' 18:00')
                    ->getQuery()
                    ->getResult();
    }
}
