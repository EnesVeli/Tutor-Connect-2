<?php

namespace App\Models;

class Booking
{
    public int $id;
    public int $student_id;
    public int $tutor_id;
    public ?int $profile_id = null;
    public string $scheduled_at;
    public string $status;
    public ?string $student_comment = null;
    public string $created_at;
}