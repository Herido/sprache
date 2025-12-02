<?php

namespace App\Controller;

use App\Repository\VocabularyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class VocabularyController extends AbstractController
{
    public function __construct(private readonly VocabularyRepository $vocabularyRepository)
    {
    }

    #[Route('/vocabulary/{courseId}', name: 'app_vocabulary_list')]
    public function list(string $courseId): Response
    {
        return $this->render('vocabulary/list.html.twig', [
            'entries' => $this->vocabularyRepository->findBy(['course' => $courseId]),
        ]);
    }
}
