<?php

namespace App\Enum;

enum TaskType: string
{
    case QUIZ = 'QUIZ';
    case VOCABULARY = 'VOCABULARY';
    case LESSON = 'LESSON';
}
