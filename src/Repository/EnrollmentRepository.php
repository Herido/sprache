<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    public function findOneByUserAndCourse(User $user, Course $course): ?Enrollment
    {
        return $this->findOneBy(['user' => $user, 'course' => $course]);
    }
}
