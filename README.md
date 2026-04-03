# Tutor Connect — Web Development 2

As a Tutor:
You can register and log in to create your profile. You can select the subject you teach, your available days, your hourly rate, your years of experience (to show why students should book you), and write your bio. You can also view your schedule. In your schedule, you can see the student's name, their age and email, any messages they left for you, and the booking status. From there, it is your choice to accept or decline the tutoring session.

As a Student:
You can register, log in, and set up your profile. You can filter tutors based on your needs, find the one you want, select the date, and "pay" to book a lesson. It will then show up on your schedule. (Note: I haven't put up a fake payment gateway link because 1. it wasn't mentioned in the project proposal, and 2. I tried it and saw it looked bad/broke the flow). Students can also leave a rating and review for their tutor after a session.

As an Admin:
You can log in and see platform statistics: total users, platform earnings, total bookings, active tutors, and the most popular tutors ordered from highest to lowest. Moreover, you can manage users (view, edit, delete). (Note: I didn't add the option for Admins to create new users because it wasn't in the proposal, and there is no need since anyone can easily sign up at the start). You can also moderate content. For example, if a tutor has 5 different profiles for different subjects and writes a curse word in the bio of one of them, you can just edit or delete that specific subject profile. Admins can also moderate and delete inappropriate reviews.

That's all the main features. Have a nice day!

## Getting Started

## 1. Start the Backend (Docker)

```bash
docker compose up -d
```

This starts:

- PHP API at `http://localhost`
- phpMyAdmin at `http://localhost:8080`

## 2. Import the Database

Open phpMyAdmin at `http://localhost:8080` and import `mysql.sql`, or let the Docker init script handle it.

## 3. Install Composer Dependencies

```bash
docker compose exec php composer install
```

## 4. Start the Frontend (Vue)

```bash
cd frontend
npm install
npm run dev
```

The Vue app runs at `http://localhost:5173`.

## Login Credentials

All accounts use the Password: password

## Examples: Role Email Password

Admin Admin@admin password
Tutor Enes@gmail.com password
Student bruh@gmail.com password
Student EminKarabulut@gmail.com password
You can always create any user with any email and password you want to use.

## AI Disclosure

I developed this project with the assistance of gemini. I used it to help me debug for the pagination filter, to understand the logic for validating JWT expiry in the frontend, and to help structure my Vue Pinia stores correctly based on the weekly lectures.
