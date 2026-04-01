<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\TutorService;

class StudentController extends Controller
{
    private TutorService $tutorService;

    public function __construct()
    {
        $this->tutorService = new TutorService();
    }
    public function index()
    {
        $subject = $_GET['subject'] ?? null;
        $minPrice = !empty($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = !empty($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $tutors = $this->tutorService->searchTutors($subject, $minPrice, $maxPrice);

        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode($tutors);
            exit;
        }

        $this->view('Student/TutorList', [
            'tutors' => $tutors,
            'selectedSubject' => $subject,
            'selectedMinPrice' => $minPrice,
            'selectedMaxPrice' => $maxPrice
        ]);
    }
}