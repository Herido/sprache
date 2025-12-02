<?php

namespace App\Controller\Api;

use App\Repository\ProgressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('ROLE_USER')]
class ProgressApiController extends AbstractController
{
    public function __construct(private readonly ProgressRepository $progressRepository)
    {
    }

    #[Route('/progress', name: 'api_progress', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $progress = $user ? $this->progressRepository->findBy(['user' => $user]) : [];

        $payload = array_map(static function ($entry) {
            return [
                'course' => (string) $entry->getCourse()?->getTitle(),
                'value' => $entry->getProgressValue(),
                'updatedAt' => $entry->getUpdatedAt()->format(DATE_ATOM),
            ];
        }, $progress);

        return $this->json($payload);
    }
}
