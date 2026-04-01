<?php

namespace App\Models;

class User
{
    public int $id;
    public string $email;
    public string $password;
    public string $first_name;
    public string $last_name;
    public string $role;
    public string $created_at;
}