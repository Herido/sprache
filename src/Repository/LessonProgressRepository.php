<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\LessonProgress;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LessonProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonProgress::class);
    }

    public function countCompletedByCourse(User $user, string $courseId): int
    {
        $qb = $this->createQueryBuilder('lp')
            ->select('COUNT(lp.id)')
            ->join('lp.lesson', 'l')
            ->join('l.course', 'c')
            ->andWhere('lp.user = :user')
            ->andWhere('c.id = :courseId')
            ->andWhere('lp.completed = true')
            ->setParameters(['user' => $user, 'courseId' => $courseId]);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function markCompleted(User $user, Lesson $lesson): LessonProgress
    {
        $progress = $this->findOneBy(['user' => $user, 'lesson' => $lesson]);
        if (!$progress) {
            $progress = (new LessonProgress())->setUser($user)->setLesson($lesson);
        }
        $progress->setCompleted(true);
        $this->_em->persist($progress);
        $this->_em->flush();

        return $progress;
    }
}
