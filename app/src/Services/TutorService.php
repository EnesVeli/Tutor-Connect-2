<?php

namespace App\Services;

use App\Repositories\TutorRepository;

class TutorService
{
    private TutorRepository $tutorRepo;

    public function __construct()
    {
        $this->tutorRepo = new TutorRepository();
    }
    public function searchTutors(?string $subject, ?float $minPrice, ?float $maxPrice): array
    {
        if (empty($maxPrice) || $maxPrice == 0) {
            $maxPrice = null;
            $minPrice = null;
        }
        return $this->tutorRepo->searchTutors($subject, $minPrice, $maxPrice);
    }
    
    public function getTutorProfile(int $id)
    {
        return $this->tutorRepo->findByUserId($id);
    }
}