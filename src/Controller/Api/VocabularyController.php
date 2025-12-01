<?php

namespace App\Controller\Api;

use App\Repository\VocabularyEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vocabulary')]
class VocabularyController extends AbstractController
{
    public function __construct(private VocabularyEntryRepository $repository)
    {
    }

    #[Route('/', name: 'api_vocabulary_list')]
    public function list(): JsonResponse
    {
        $entries = $this->repository->findBy(['user' => $this->getUser()]);
        $data = array_map(fn ($entry) => [
            'id' => (string) $entry->getId(),
            'word' => $entry->getWord(),
            'translation' => $entry->getTranslation(),
            'example' => $entry->getExampleSentence(),
        ], $entries);

        return $this->json($data);
    }
}
