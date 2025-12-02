<?php

namespace App\Controller\Api;

use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/lessons')]
class LessonApiController extends AbstractController
{
    #[Route('/', name: 'api_lessons_list')]
    public function list(LessonRepository $lessonRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->serialize($lessonRepository->findAll(), 'json', ['groups' => ['lesson:read']]);
        return new JsonResponse($data, json: true);
    }
}
