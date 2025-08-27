<?php

namespace App\Repository;

use App\Entity\Mentor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class MentorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mentor::class);
    }

    public function findMentorsWithCoursesInMonth(\DateTimeInterface $start, \DateTimeInterface $end): array
{
    return $this->createQueryBuilder('m')
        ->innerJoin('App\Entity\Course', 'c', 'WITH', 'c.mentor = m')
        ->andWhere('c.startTime BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->distinct()
        ->getQuery()
        ->getResult();
}

    // Ajoute ici des méthodes personnalisées si besoin
}
