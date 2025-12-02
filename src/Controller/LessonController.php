<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/lessons')]
class LessonController extends AbstractController
{
    #[Route('/', name: 'lesson_index')]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'lesson_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, CourseRepository $courseRepository): Response
    {
        $lesson = new Lesson();
        $courses = $courseRepository->findAll();

        if ($request->isMethod('POST')) {
            $courseId = $request->request->get('course');
            $course = $courseRepository->find($courseId ? Uuid::fromString($courseId) : null);

            if ($course) {
                $lesson
                    ->setCourse($course)
                    ->setTitle($request->request->get('title', ''))
                    ->setContent($request->request->get('content', ''));

                $entityManager->persist($lesson);
                $entityManager->flush();

                $this->addFlash('success', 'Lektion wurde erstellt.');
                return $this->redirectToRoute('lesson_index');
            }

            $this->addFlash('danger', 'Bitte einen Kurs wählen.');
        }

        return $this->render('lesson/form.html.twig', [
            'lesson' => $lesson,
            'courses' => $courses,
        ]);
    }

    #[Route('/{id}', name: 'lesson_show', requirements: ['id' => '[0-9a-f\-]{36}'])]
    public function show(LessonRepository $lessonRepository, string $id): Response
    {
        $lesson = $lessonRepository->find(Uuid::fromString($id));
        if (!$lesson) {
            throw $this->createNotFoundException('Lektion nicht gefunden.');
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}/edit', name: 'lesson_edit', methods: ['GET', 'POST'], requirements: ['id' => '[0-9a-f\-]{36}'])]
    public function edit(
        Request $request,
        LessonRepository $lessonRepository,
        CourseRepository $courseRepository,
        EntityManagerInterface $entityManager,
        string $id
    ): Response {
        $lesson = $lessonRepository->find(Uuid::fromString($id));
        if (!$lesson) {
            throw $this->createNotFoundException('Lektion nicht gefunden.');
        }

        $courses = $courseRepository->findAll();

        if ($request->isMethod('POST')) {
            $courseId = $request->request->get('course');
            $course = $courseRepository->find($courseId ? Uuid::fromString($courseId) : null);
            if ($course) {
                $lesson
                    ->setCourse($course)
                    ->setTitle($request->request->get('title', $lesson->getTitle()))
                    ->setContent($request->request->get('content', $lesson->getContent()));

                $entityManager->flush();
                $this->addFlash('success', 'Lektion aktualisiert.');
                return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()]);
            }

            $this->addFlash('danger', 'Bitte einen Kurs wählen.');
        }

        return $this->render('lesson/form.html.twig', [
            'lesson' => $lesson,
            'courses' => $courses,
        ]);
    }
}
