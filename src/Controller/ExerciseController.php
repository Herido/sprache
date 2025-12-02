<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Enum\ExerciseType;
use App\Repository\ExerciseRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/exercises')]
class ExerciseController extends AbstractController
{
    #[Route('/', name: 'exercise_index')]
    public function index(ExerciseRepository $exerciseRepository): Response
    {
        return $this->render('exercise/index.html.twig', [
            'exercises' => $exerciseRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'exercise_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        LessonRepository $lessonRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $exercise = new Exercise();
        $lessons = $lessonRepository->findAll();

        if ($request->isMethod('POST')) {
            $lessonId = $request->request->get('lesson');
            $lesson = $lessonRepository->find($lessonId ? Uuid::fromString($lessonId) : null);

            if ($lesson) {
                $exercise
                    ->setLesson($lesson)
                    ->setQuestion($request->request->get('question', ''))
                    ->setType(ExerciseType::from($request->request->get('type', ExerciseType::MULTIPLE_CHOICE->value)))
                    ->setAnswer($request->request->get('answer', ''));

                $entityManager->persist($exercise);
                $entityManager->flush();

                $this->addFlash('success', 'Übung angelegt.');
                return $this->redirectToRoute('exercise_index');
            }

            $this->addFlash('danger', 'Bitte eine Lektion wählen.');
        }

        return $this->render('exercise/form.html.twig', [
            'exercise' => $exercise,
            'lessons' => $lessons,
            'types' => ExerciseType::cases(),
        ]);
    }

    #[Route('/{id}', name: 'exercise_show', requirements: ['id' => '[0-9a-f\-]{36}'])]
    public function show(ExerciseRepository $exerciseRepository, string $id): Response
    {
        $exercise = $exerciseRepository->find(Uuid::fromString($id));
        if (!$exercise) {
            throw $this->createNotFoundException('Übung nicht gefunden.');
        }

        return $this->render('exercise/show.html.twig', [
            'exercise' => $exercise,
        ]);
    }

    #[Route('/{id}/edit', name: 'exercise_edit', methods: ['GET', 'POST'], requirements: ['id' => '[0-9a-f\-]{36}'])]
    public function edit(
        Request $request,
        ExerciseRepository $exerciseRepository,
        LessonRepository $lessonRepository,
        EntityManagerInterface $entityManager,
        string $id
    ): Response {
        $exercise = $exerciseRepository->find(Uuid::fromString($id));
        if (!$exercise) {
            throw $this->createNotFoundException('Übung nicht gefunden.');
        }

        $lessons = $lessonRepository->findAll();

        if ($request->isMethod('POST')) {
            $lessonId = $request->request->get('lesson');
            $lesson = $lessonRepository->find($lessonId ? Uuid::fromString($lessonId) : null);
            if ($lesson) {
                $exercise
                    ->setLesson($lesson)
                    ->setQuestion($request->request->get('question', $exercise->getQuestion()))
                    ->setType(ExerciseType::from($request->request->get('type', $exercise->getType()->value)))
                    ->setAnswer($request->request->get('answer', $exercise->getAnswer()));

                $entityManager->flush();
                $this->addFlash('success', 'Übung aktualisiert.');
                return $this->redirectToRoute('exercise_show', ['id' => $exercise->getId()]);
            }

            $this->addFlash('danger', 'Bitte eine Lektion wählen.');
        }

        return $this->render('exercise/form.html.twig', [
            'exercise' => $exercise,
            'lessons' => $lessons,
            'types' => ExerciseType::cases(),
        ]);
    }
}
