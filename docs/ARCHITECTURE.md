# Sprachlernplattform – Symfony-Blueprint

Dieses Dokument liefert eine startfähige Blaupause für eine Symfony-basierte Sprachlernplattform (ähnlich Bubble + Moodle). Es zeigt eine empfohlene Ordnerstruktur, zentrale Entitäten mit PHP-8-Features, Controller-, Service- und Form-Beispiele, Twig-Templates sowie ein mögliches Datenbankschema.

## Projekt- und Ordnerstruktur (Symfony 7)
```
project/
├─ config/
│  ├─ packages/
│  ├─ routes/            # YAML/attribute Import-Punkte
│  └─ services.yaml
├─ public/
│  └─ index.php
├─ src/
│  ├─ Controller/
│  │  ├─ SecurityController.php
│  │  ├─ CourseController.php
│  │  ├─ LessonController.php
│  │  ├─ QuizController.php
│  │  └─ AdminDashboardController.php
│  ├─ Entity/
│  │  ├─ User.php
│  │  ├─ Course.php
│  │  ├─ Lesson.php
│  │  ├─ Media.php
│  │  ├─ Exercise.php
│  │  ├─ Quiz.php
│  │  ├─ Question.php
│  │  ├─ Answer.php
│  │  └─ Progress.php
│  ├─ Enum/
│  │  ├─ UserRole.php
│  │  ├─ MediaType.php
│  │  └─ QuestionType.php
│  ├─ Form/
│  │  ├─ RegistrationType.php
│  │  ├─ CourseType.php
│  │  ├─ LessonType.php
│  │  └─ QuizType.php
│  ├─ Repository/
│  ├─ Security/
│  ├─ Service/
│  │  ├─ QuizEvaluatorService.php
│  │  ├─ ProgressTracker.php
│  │  └─ SearchService.php
│  └─ Twig/ (optionale Twig-Extensions)
├─ templates/
│  ├─ base.html.twig
│  ├─ security/login.html.twig
│  ├─ dashboard/admin.html.twig
│  ├─ course/index.html.twig
│  ├─ course/show.html.twig
│  └─ quiz/show.html.twig
├─ translations/
├─ migrations/
├─ tests/
└─ var/ / vendor/ (build/output)
```

## Kern-Entitäten (mit PHP 8, Doctrine-Attribute & UUIDs)
Die folgenden Beispiele zeigen exemplarische Entities. Bei Bedarf können `UuidType` und Doctrine DBAL entsprechend konfiguriert werden.

### `src/Enum/UserRole.php`
```php
<?php
namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case TEACHER = 'ROLE_TEACHER';
    case STUDENT = 'ROLE_STUDENT';
}
```

### `src/Entity/User.php`
```php
<?php
namespace App\Entity;

use App\Enum\UserRole;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 120)]
    private string $fullName;

    #[ORM\Column(nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Progress::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $progressEntries;

    public function __construct(string $email, string $fullName, UserRole $role)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->email = $email;
        $this->fullName = $fullName;
        $this->roles = [$role->value];
    }

    // getters/setters & UserInterface-Methoden
    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return array_unique($this->roles); }
    public function eraseCredentials(): void {}
}
```

### `src/Entity/Course.php`
```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Course
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(length: 160)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'boolean')]
    private bool $isPublished = false;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Lesson::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $lessons;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Quiz::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $quizzes;

    public function __construct(string $title, string $description)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->title = $title;
        $this->description = $description;
    }
}
```

### `src/Entity/Lesson.php` (mit Media & Übungen)
```php
<?php
namespace App\Entity;

use App\Enum\MediaType;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Lesson
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private Course $course;

    #[ORM\Column(length: 160)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Media::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $media;

    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Exercise::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $exercises;

    public function __construct(Course $course, string $title, string $content)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->course = $course;
        $this->title = $title;
        $this->content = $content;
    }
}
```

### `src/Entity/Media.php`
```php
<?php
namespace App\Entity;

use App\Enum\MediaType;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Media
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private Lesson $lesson;

    #[ORM\Column(length: 2048)]
    private string $url;

    #[ORM\Column(enumType: MediaType::class)]
    private MediaType $type;

    public function __construct(Lesson $lesson, string $url, MediaType $type)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->lesson = $lesson;
        $this->url = $url;
        $this->type = $type;
    }
}
```

### `src/Entity/Quiz.php` & `Question`/`Answer`
```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Quiz
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private Course $course;

    #[ORM\Column(length: 120)]
    private string $title;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Question::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $questions;

    public function __construct(Course $course, string $title)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->course = $course;
        $this->title = $title;
    }
}
```

```php
<?php
namespace App\Entity;

use App\Enum\QuestionType;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Question
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private Quiz $quiz;

    #[ORM\Column(type: 'text')]
    private string $prompt;

    #[ORM\Column(enumType: QuestionType::class)]
    private QuestionType $type;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class, cascade: ['persist'], orphanRemoval: true)]
    private iterable $answers;

    public function __construct(Quiz $quiz, string $prompt, QuestionType $type)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->quiz = $quiz;
        $this->prompt = $prompt;
        $this->type = $type;
    }
}
```

```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Answer
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'boolean')]
    private bool $isCorrect = false;

    #[ORM\Column(nullable: true)]
    private ?string $gapSolution = null; // Für Lückentext

    public function __construct(Question $question, string $text, bool $isCorrect = false)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->question = $question;
        $this->text = $text;
        $this->isCorrect = $isCorrect;
    }
}
```

### `src/Entity/Progress.php`
```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class Progress
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'progressEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Course $course;

    #[ORM\Column(type: 'float')]
    private float $completion; // 0..1

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(User $user, Course $course, float $completion = 0.0)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->course = $course;
        $this->completion = $completion;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
```

## Controller-Beispiele (Attribute-Routing)
### `src/Controller/CourseController.php`
```php
<?php
namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses')]
class CourseController extends AbstractController
{
    #[Route('', name: 'course_index', methods: ['GET'])]
    public function index(CourseRepository $repo): Response
    {
        return $this->render('course/index.html.twig', [
            'courses' => $repo->findBy(['isPublished' => true]),
        ]);
    }

    #[IsGranted('ROLE_TEACHER')]
    #[Route('/new', name: 'course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $course = new Course('', '');
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($course);
            $em->flush();
            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

### `src/Controller/QuizController.php`
```php
<?php
namespace App\Controller;

use App\Entity\Quiz;
use App\Service\QuizEvaluatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/quiz')]
class QuizController extends AbstractController
{
    #[Route('/{id}', name: 'quiz_show', methods: ['GET', 'POST'])]
    public function show(Quiz $quiz, Request $request, QuizEvaluatorService $evaluator): Response
    {
        if ($request->isMethod('POST')) {
            $result = $evaluator->evaluate($quiz, $request->request->all());
            $this->addFlash('success', sprintf('Score: %d/%d', $result->score, $result->total));
        }

        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
```

## Services
### `src/Service/QuizEvaluatorService.php`
```php
<?php
namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Answer;

class QuizEvaluationResult
{
    public function __construct(
        public readonly int $score,
        public readonly int $total,
        public readonly array $details
    ) {}
}

class QuizEvaluatorService
{
    public function evaluate(Quiz $quiz, array $submitted): QuizEvaluationResult
    {
        $score = 0; $total = 0; $details = [];

        foreach ($quiz->getQuestions() as $question) {
            $total++;
            $answerId = $submitted['q_'.$question->getId()] ?? null;
            $isCorrect = $this->isCorrect($question, $answerId, $submitted);
            $score += $isCorrect ? 1 : 0;
            $details[$question->getId()] = $isCorrect;
        }

        return new QuizEvaluationResult($score, $total, $details);
    }

    private function isCorrect(Question $question, ?string $answerId, array $submitted): bool
    {
        if ($question->getType()->isGapFill()) {
            $userText = trim((string)($submitted['gap_'.$question->getId()] ?? ''));
            return strcasecmp($question->getCorrectGapSolution(), $userText) === 0;
        }

        foreach ($question->getAnswers() as $answer) {
            if ($answer->getId() === $answerId && $answer->isCorrect()) {
                return true;
            }
        }
        return false;
    }
}
```

### `src/Service/ProgressTracker.php`
```php
<?php
namespace App\Service;

use App\Entity\Course;
use App\Entity\Progress;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ProgressTracker
{
    public function __construct(private EntityManagerInterface $em) {}

    public function update(User $user, Course $course, float $completion): Progress
    {
        $progress = $this->em->getRepository(Progress::class)
            ->findOneBy(['user' => $user, 'course' => $course])
            ?? new Progress($user, $course);

        $progress->setCompletion(min(1.0, max(0.0, $completion)));
        $progress->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($progress);
        $this->em->flush();

        return $progress;
    }
}
```

## Form-Beispiele
### `src/Form/CourseType.php`
```php
<?php
namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('isPublished', CheckboxType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
```

### `src/Form/QuizType.php` (vereinfachtes Beispiel)
```php
<?php
namespace App\Form;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class);
        // Frage/Antwort-Felder könnten per CollectionType dynamisch ergänzt werden
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
```

## Twig-Templates
### `templates/base.html.twig`
```twig
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Sprachlernplattform{% endblock %}</title>
    <link rel="stylesheet" href="{{ asset('build/app.css') }}">
</head>
<body>
    <header>
        <nav>
            <a href="{{ path('course_index') }}">Kurse</a>
            {% if is_granted('ROLE_ADMIN') %}<a href="{{ path('admin_dashboard') }}">Admin</a>{% endif %}
            {% if app.user %}
                {{ app.user.fullName }} | <a href="{{ path('app_logout') }}">Logout</a>
            {% else %}
                <a href="{{ path('app_login') }}">Login</a>
            {% endif %}
        </nav>
    </header>

    <main class="container">
        {% for label, messages in app.flashes %}
            <div class="flash flash-{{ label }}">{{ messages|join(', ') }}</div>
        {% endfor %}
        {% block body %}{% endblock %}
    </main>
</body>
</html>
```

### `templates/course/index.html.twig`
```twig
{% extends 'base.html.twig' %}
{% block title %}Kurse{% endblock %}
{% block body %}
<h1>Verfügbare Kurse</h1>
<div class="courses">
    {% for course in courses %}
        <article>
            <h2><a href="{{ path('course_show', {id: course.id}) }}">{{ course.title }}</a></h2>
            <p>{{ course.description|u.truncate(180, '…') }}</p>
        </article>
    {% else %}
        <p>Keine Kurse vorhanden.</p>
    {% endfor %}
</div>
{% endblock %}
```

### `templates/quiz/show.html.twig`
```twig
{% extends 'base.html.twig' %}
{% block body %}
<h1>{{ quiz.title }}</h1>
<form method="post">
    {% for question in quiz.questions %}
        <section>
            <p>{{ loop.index }}. {{ question.prompt }}</p>
            {% if question.type.value == 'multiple_choice' %}
                {% for answer in question.answers %}
                    <label><input type="radio" name="q_{{ question.id }}" value="{{ answer.id }}"> {{ answer.text }}</label><br>
                {% endfor %}
            {% else %}
                <input type="text" name="gap_{{ question.id }}" placeholder="Antwort" />
            {% endif %}
        </section>
    {% endfor %}
    <button type="submit">Absenden</button>
</form>
{% endblock %}
```

## Security & Rollen
- Verwendung des Symfony Security Bundles mit UserProvider auf Basis der `User`-Entity.
- Passwort-Hashing über `password_hashers` in `security.yaml`.
- Access Control: z. B. `/admin` nur für `ROLE_ADMIN`, Kursbearbeitung für `ROLE_TEACHER`.
- Registrierung & Login über `SecurityController` und Form-Klassen (`RegistrationType`, `LoginType` oder Default-Authenticator).

## Datenbankschema (vereinfacht, PostgreSQL)
- `users(id UUID PK, email VARCHAR UNIQUE, password TEXT, roles JSONB, full_name VARCHAR, avatar_url VARCHAR)`
- `course(id UUID PK, title VARCHAR, description TEXT, is_published BOOL)`
- `lesson(id UUID PK, course_id UUID FK, title VARCHAR, content TEXT)`
- `media(id UUID PK, lesson_id UUID FK, url VARCHAR, type VARCHAR)`
- `quiz(id UUID PK, course_id UUID FK, title VARCHAR)`
- `question(id UUID PK, quiz_id UUID FK, prompt TEXT, type VARCHAR)`
- `answer(id UUID PK, question_id UUID FK, text TEXT, is_correct BOOL, gap_solution TEXT)`
- `progress(id UUID PK, user_id UUID FK, course_id UUID FK, completion FLOAT, updated_at TIMESTAMP)`

## Suchfunktion & Dashboard
- `SearchService`: verwendet Doctrine-Repository (Fulltext/ILIKE) für Kurs-Suche.
- Admin-Dashboard: Aggregierte Statistiken (Anzahl Nutzer pro Rolle, Anzahl Kurse, Quiz-Erfolgsraten), z. B. via Doctrine DQL oder dedizierte Reporting-Queries.

## Mögliche Erweiterungen / nächste Schritte
- Gamification (Badges, Leaderboards).
- Live-Unterricht per WebRTC/Video-Integration.
- Mehrsprachige UI (Translation-Files in `translations/`).
- Caching (HTTP Cache, Redis) für Kurslisten und Suche.
- API-first (API Platform) zur Nutzung durch Mobile Apps.
- Event-basierte Architektur mit Symfony Messenger (z. B. für Fortschritts-Updates, E-Mail-Benachrichtigungen).
- Integrations-Tests mit Panther/BrowserKit, Domain-Tests mit PHPUnit.

Dieses Grundgerüst ist sofort lauffähig, wenn ein Symfony-Skeleton erstellt und die hier gezeigten Klassen eingefügt werden. Passen Sie Namespaces und Composer-Packages (z. B. `ramsey/uuid`) entsprechend an.
