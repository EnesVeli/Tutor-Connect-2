<?php

namespace App\Models;

class StudentProfile
{
    public int $id;
    public int $user_id;
    public string $date_of_birth;
    public ?string $bio = null;
}