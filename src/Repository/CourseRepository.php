<?php

namespace App\Repository;

use App\Entity\Course;
use App\Enum\CourseLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /** @return Course[] */
    public function findByLevel(CourseLevel $level): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.level = :level')
            ->setParameter('level', $level)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
