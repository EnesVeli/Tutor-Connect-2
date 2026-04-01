<?php

namespace App\Models;

class TutorProfile
{
    public int $id;
    public int $user_id;
    public ?string $bio;
    public float $hourly_rate;
    public int $experience_years;
    public string $subject;
    public string $availability_start = "09:00"; 
    public string $availability_end = "22:00";  
    public ?string $available_days = null;
}