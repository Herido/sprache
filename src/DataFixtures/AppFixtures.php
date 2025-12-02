<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Exercise;
use App\Entity\Lesson;
use App\Entity\Progress;
use App\Entity\User;
use App\Enum\ExerciseType;
use App\Enum\ProgressStatus;
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
        $admin = (new User())
            ->setEmail('admin@example.com')
            ->setName('Admin')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));

        $user = (new User())
            ->setEmail('user@example.com')
            ->setName('Demo User')
            ->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'password'));

        $course = (new Course())
            ->setTitle('Spanisch für Anfänger')
            ->setDescription('Grundlagen der spanischen Sprache mit praxisnahen Übungen.')
            ->setLanguage('Spanisch');

        $lesson1 = (new Lesson())
            ->setCourse($course)
            ->setTitle('Begrüßungen')
            ->setContent('Hola! ¿Cómo estás? Typische Begrüßungen und Antworten.');

        $lesson2 = (new Lesson())
            ->setCourse($course)
            ->setTitle('Zahlen')
            ->setContent('Eins bis Zehn auf Spanisch und einfache Übungen.');

        $exercise = (new Exercise())
            ->setLesson($lesson1)
            ->setQuestion('Wie sagt man "Guten Morgen" auf Spanisch?')
            ->setType(ExerciseType::SHORT_ANSWER)
            ->setAnswer('Buenos días');

        $progress = (new Progress())
            ->setUser($user)
            ->setLesson($lesson1)
            ->setStatus(ProgressStatus::IN_PROGRESS);

        foreach ([$admin, $user, $course, $lesson1, $lesson2, $exercise, $progress] as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
