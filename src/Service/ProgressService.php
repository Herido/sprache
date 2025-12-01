<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\User;
use App\Repository\EnrollmentRepository;
use App\Repository\LessonProgressRepository;

class ProgressService
{
    public function __construct(
        private EnrollmentRepository $enrollmentRepository,
        private LessonProgressRepository $lessonProgressRepository
    ) {
    }

    public function enroll(User $user, Course $course): void
    {
        $enrollment = $this->enrollmentRepository->findOneByUserAndCourse($user, $course) ?? new \App\Entity\Enrollment();
        $enrollment->setUser($user)->setCourse($course)->setProgress(0);
        $this->enrollmentRepository->getEntityManager()->persist($enrollment);
        $this->enrollmentRepository->getEntityManager()->flush();
    }

    public function completeLesson(User $user, Lesson $lesson): float
    {
        $course = $lesson->getCourse();
        if (!$course) {
            return 0.0;
        }

        $this->lessonProgressRepository->markCompleted($user, $lesson);
        $completed = $this->lessonProgressRepository->countCompletedByCourse($user, (string) $course->getId());
        $totalLessons = max(1, count($course->getLessons()));
        $progress = ($completed / $totalLessons) * 100;

        $enrollment = $this->enrollmentRepository->findOneByUserAndCourse($user, $course);
        if ($enrollment) {
            $enrollment->setProgress($progress);
            $this->enrollmentRepository->getEntityManager()->flush();
        }

        return $progress;
    }

    public function getCourseProgress(User $user, Course $course): float
    {
        $enrollment = $this->enrollmentRepository->findOneByUserAndCourse($user, $course);
        if (!$enrollment) {
            return 0.0;
        }

        return $enrollment->getProgress();
    }
}
