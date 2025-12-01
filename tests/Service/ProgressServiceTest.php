<?php

namespace App\Tests\Service;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\Lesson;
use App\Entity\LessonProgress;
use App\Entity\User;
use App\Repository\EnrollmentRepository;
use App\Repository\LessonProgressRepository;
use App\Service\ProgressService;
use PHPUnit\Framework\TestCase;

class ProgressServiceTest extends TestCase
{
    public function testCompleteLessonCalculatesProgress(): void
    {
        $user = new User();
        $course = new Course();
        $lesson = (new Lesson())->setCourse($course);

        $course->addLesson($lesson);

        $progressRepo = $this->createMock(LessonProgressRepository::class);
        $progressRepo->expects($this->once())->method('markCompleted')->willReturn(new LessonProgress());
        $progressRepo->expects($this->once())->method('countCompletedByCourse')->willReturn(1);

        $enrollment = (new Enrollment())->setUser($user)->setCourse($course);
        $enrollmentRepo = $this->createMock(EnrollmentRepository::class);
        $enrollmentRepo->method('findOneByUserAndCourse')->willReturn($enrollment);
        $enrollmentRepo->method('getEntityManager')->willReturn(new class {
            public function flush(): void {}
            public function persist($entity): void {}
        });

        $service = new ProgressService($enrollmentRepo, $progressRepo);
        $progress = $service->completeLesson($user, $lesson);

        $this->assertSame(100.0, $progress);
        $this->assertSame(100.0, $enrollment->getProgress());
    }
}
