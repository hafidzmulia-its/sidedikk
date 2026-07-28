<?php

namespace App\Enums;

enum EducationPostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Unpublished = 'unpublished';
    case Archived = 'archived';
}
