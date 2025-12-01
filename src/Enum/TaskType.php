<?php

namespace App\Enum;

enum TaskType: string
{
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case VOCABULARY = 'vocabulary';
}
