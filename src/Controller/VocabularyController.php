<?php

namespace App\Controller;

use App\Entity\VocabularyEntry;
use App\Repository\VocabularyEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vocabulary')]
class VocabularyController extends AbstractController
{
    public function __construct(private VocabularyEntryRepository $repository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'vocabulary_index')]
    public function index(Request $request): Response
    {
        $entries = $this->repository->findByUserAndQuery($this->getUser(), $request->query->get('q'));

        return $this->render('vocabulary/index.html.twig', [
            'entries' => $entries,
        ]);
    }

    #[Route('/new', name: 'vocabulary_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $entry = (new VocabularyEntry())
            ->setUser($this->getUser())
            ->setWord($request->request->get('word', ''))
            ->setTranslation($request->request->get('translation', ''))
            ->setExampleSentence($request->request->get('example'));

        $this->entityManager->persist($entry);
        $this->entityManager->flush();
        $this->addFlash('success', 'Vokabel gespeichert');

        return $this->redirectToRoute('vocabulary_index');
    }

    #[Route('/{id}/delete', name: 'vocabulary_delete')]
    public function delete(string $id): Response
    {
        $entry = $this->repository->find($id);
        if ($entry) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('vocabulary_index');
    }
}
