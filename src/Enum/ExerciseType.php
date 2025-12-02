<?php

namespace App\Enum;

enum ExerciseType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case FILL_IN_THE_BLANK = 'fill_in_the_blank';
    case TRUE_FALSE = 'true_false';
    case SHORT_ANSWER = 'short_answer';
}
