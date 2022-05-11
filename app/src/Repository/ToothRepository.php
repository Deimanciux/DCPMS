<?php

namespace App\Repository;

use App\Entity\Tooth;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tooth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tooth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tooth[]    findAll()
 * @method Tooth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ToothRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tooth::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Tooth $entity, bool $flush = true): void
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
    public function remove(Tooth $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getTeethBySequence(User $user)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.position', 'p')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.sequenceNumber', 'asc')
            ->getQuery()
            ->getResult();
    }
}
