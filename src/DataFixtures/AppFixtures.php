<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Enum\CourseLevel;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setEmail('demo@example.com')
            ->setName('Demo User')
            ->setRoles([UserRole::STUDENT->value]);
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $course = (new Course())
            ->setTitle('Spanisch für Anfänger')
            ->setDescription('Grundlagen der spanischen Sprache mit praxisnahen Beispielen.')
            ->setLevel(CourseLevel::BEGINNER);

        $lesson1 = (new Lesson())
            ->setTitle('Begrüßungen')
            ->setContent('Hola! ¿Qué tal?')
            ->setCourse($course);
        $lesson2 = (new Lesson())
            ->setTitle('Zahlen')
            ->setContent('eins, dos, tres...')
            ->setCourse($course);

        $quiz = (new Quiz())
            ->setTitle('Begrüßungen Quiz')
            ->setLesson($lesson1);

        $question = (new Question())
            ->setQuiz($quiz)
            ->setPrompt('Wie sagt man Hallo auf Spanisch?')
            ->setChoices(['Hola', 'Adios', 'Gracias'])
            ->setAnswer('Hola');

        $manager->persist($course);
        $manager->persist($lesson1);
        $manager->persist($lesson2);
        $manager->persist($quiz);
        $manager->persist($question);

        $manager->flush();
    }
}
