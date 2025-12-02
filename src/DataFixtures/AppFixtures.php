<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Progress;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\User;
use App\Entity\Vocabulary;
use App\Enum\CourseLevel;
use App\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setName('Admin')
            ->setEmail('admin@example.com')
            ->setRoles([Role::ROLE_ADMIN->value]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin1234'));

        $user = (new User())
            ->setName('Max Mustermann')
            ->setEmail('max@example.com')
            ->setRoles([Role::ROLE_USER->value]);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'pass1234'));

        $manager->persist($admin);
        $manager->persist($user);

        $course = (new Course())
            ->setTitle('Deutsch A1')
            ->setDescription('Grundlagen der deutschen Sprache für Anfänger:innen')
            ->setLevel(CourseLevel::BEGINNER);

        $lesson1 = (new Lesson())
            ->setCourse($course)
            ->setTitle('Begrüßungen')
            ->setContent('Hallo, Guten Tag, Auf Wiedersehen.');
        $lesson2 = (new Lesson())
            ->setCourse($course)
            ->setTitle('Zahlen')
            ->setContent('Eins, zwei, drei ...');

        $quiz = (new Quiz())
            ->setCourse($course)
            ->setTitle('Begrüßungen Quiz');
        $q1 = (new QuizQuestion())
            ->setQuiz($quiz)
            ->setQuestion('Was bedeutet "Guten Morgen"?')
            ->setAnswers(['Good evening', 'Good morning', 'Good night'])
            ->setCorrectAnswer('Good morning');
        $q2 = (new QuizQuestion())
            ->setQuiz($quiz)
            ->setQuestion('Welche Antwort passt zu "Wie geht es dir?"')
            ->setAnswers(['Mir geht es gut', 'Ich heiße Anna', 'Ich bin 20 Jahre alt'])
            ->setCorrectAnswer('Mir geht es gut');

        $vocab1 = (new Vocabulary())
            ->setCourse($course)
            ->setWord('Hund')
            ->setTranslation('dog');
        $vocab2 = (new Vocabulary())
            ->setCourse($course)
            ->setWord('Katze')
            ->setTranslation('cat');

        $progress = (new Progress())
            ->setUser($user)
            ->setCourse($course)
            ->setProgressValue(25);

        foreach ([$lesson1, $lesson2, $quiz, $q1, $q2, $vocab1, $vocab2, $progress] as $entity) {
            $manager->persist($entity);
        }

        $manager->persist($course);
        $manager->flush();
    }
}
