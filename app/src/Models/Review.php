<?php

namespace App\Models;

class Review
{
    public int $id;
    public int $booking_id;
    public int $student_id;
    public int $tutor_profile_id;
    public int $rating;
    public ?string $comment = null;
    public string $created_at;
}
