<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\CourseRepository;

class CourseService
{
    public function __construct(private readonly CourseRepository $courseRepository)
    {
    }

    /** @return Course[] */
    public function listCourses(): array
    {
        return $this->courseRepository->findAll();
    }

    public function getCourse(string $id): ?Course
    {
        return $this->courseRepository->find($id);
    }
}
