<?php

namespace App\Controller\Api;

use App\Repository\ProgressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/progress')]
class ProgressApiController extends AbstractController
{
    #[Route('/', name: 'api_progress_list')]
    public function list(ProgressRepository $progressRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->serialize($progressRepository->findAll(), 'json', ['groups' => ['progress:read']]);
        return new JsonResponse($data, json: true);
    }
}
