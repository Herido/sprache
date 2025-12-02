<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/courses')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'course_index')]
    public function index(CourseRepository $courseRepository): Response
    {
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'course_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();

        if ($request->isMethod('POST')) {
            $course
                ->setTitle($request->request->get('title', ''))
                ->setDescription($request->request->get('description', ''))
                ->setLanguage($request->request->get('language', ''));

            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Kurs wurde erstellt.');
            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/form.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}', name: 'course_show', requirements: ['id' => '[0-9a-f\-]{36}'])]
    public function show(CourseRepository $courseRepository, string $id): Response
    {
        $course = $courseRepository->find(Uuid::fromString($id));

        if (!$course) {
            throw $this->createNotFoundException('Kurs nicht gefunden.');
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'course_edit', methods: ['GET', 'POST'], requirements: ['id' => '[0-9a-f\-]{36}'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, CourseRepository $courseRepository, string $id): Response
    {
        $course = $courseRepository->find(Uuid::fromString($id));

        if (!$course) {
            throw $this->createNotFoundException('Kurs nicht gefunden.');
        }

        if ($request->isMethod('POST')) {
            $course
                ->setTitle($request->request->get('title', $course->getTitle()))
                ->setDescription($request->request->get('description', $course->getDescription()))
                ->setLanguage($request->request->get('language', $course->getLanguage()));

            $entityManager->flush();
            $this->addFlash('success', 'Kurs aktualisiert.');
            return $this->redirectToRoute('course_show', ['id' => $course->getId()]);
        }

        return $this->render('course/form.html.twig', [
            'course' => $course,
        ]);
    }
}
