<?php

namespace App\Models;

class Booking
{
    public int $id;
    public int $student_id;
    public int $tutor_id;
    public string $scheduled_at;
    public string $status;
    public string $student_comment;
    public string $created_at;
}