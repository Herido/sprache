<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Progress;
use App\Entity\User;
use App\Repository\ProgressRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProgressService
{
    public function __construct(
        private readonly ProgressRepository $progressRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function recordProgress(User $user, Course $course, int $value): Progress
    {
        $progress = $this->progressRepository->findOneBy(['user' => $user, 'course' => $course])
            ?? (new Progress())->setUser($user)->setCourse($course);

        $progress->setProgressValue($value);
        $this->entityManager->persist($progress);
        $this->entityManager->flush();

        return $progress;
    }
}
