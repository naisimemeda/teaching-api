<?php

namespace App\Policies;

use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\Teacher;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    public function own(Teacher $teacher, School $school): bool
    {
        return $school->teacher_id == $teacher->id;
    }

    public function show(Teacher $teacher, School $school): bool
    {
        return SchoolTeacher::query()->where('teacher_id', $teacher->id)->where('school_id', $school->id)->exists();
    }
}
