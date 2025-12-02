<?php

namespace App\Controller\Api;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/courses')]
class CourseApiController extends AbstractController
{
    #[Route('/', name: 'api_courses_list')]
    public function list(CourseRepository $courseRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->serialize($courseRepository->findAll(), 'json', ['groups' => ['course:read']]);
        return new JsonResponse($data, json: true);
    }
}
