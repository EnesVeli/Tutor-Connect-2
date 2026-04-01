<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\BookingService;

class BookingController extends Controller
{
    private BookingService $bookingService;

    public function __construct()
    {
        $this->bookingService = new BookingService();
    }

    public function create()
    {
        $this->requireAuth('student');

        $profileId = $_GET['tutor_id'] ?? null;
        if (!$profileId) die("Tutor ID is required.");

        $data = $this->bookingService->getBookingFormDetails((int)$profileId);

        if (!$data['tutorProfile']) die("Tutor not found.");

        $this->view('Student/Book', [
            'tutorName' => $data['tutorName'],
            'tutorProfile' => $data['tutorProfile'],
            'tutorId' => $profileId
        ]);
    }
    public function process()
    {
        $this->requireAuth('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profileId = $_POST['tutor_id'];
            $date = $_POST['date'];
            $time = $_POST['time'];
            $comment = $_POST['student_comment'];
            $paymentData = $this->bookingService->preparePayment((int)$profileId, $date, $time);

            $this->view('Student/Payment', array_merge($paymentData, [
                'tutorId' => $profileId,
                'studentComment' => $comment,
                'date' => $paymentData['prettyDate']
            ]));
        }
    }
    public function store()
    {
        $this->requireAuth('student');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = $_SESSION['user_id'];
            $profileId = $_POST['tutor_id'];
            $scheduledAt = $_POST['scheduled_at'];
            $comment = $_POST['student_comment'];

            if ($this->bookingService->createBooking($studentId, (int)$profileId, $scheduledAt, $comment)) {
                $this->view('Bookings/Success');
            } else {
                echo "Error saving booking. Profile might not exist.";
            }
        }
    }

    public function index()
    {
        $this->requireAuth(); 
        
        $bookings = $this->bookingService->getUserBookings($_SESSION['user_id'], $_SESSION['user_role']);
        
        $this->view('Bookings/List', ['bookings' => $bookings]);
    }

    public function update()
    {
        $this->requireAuth('tutor');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'];
            $status = $_POST['status'];

            $this->bookingService->updateBookingStatus($bookingId, $status);
            
            $this->redirect('/bookings');
        }
    }
}