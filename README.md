# Digital Platform for rural school students n Nabha(LMS)

An platform built with Laravel, Vite, Tailwind CSS, MySQL. The platform supports students, teachers, and admins with role-based dashboards, multilingual content, offline-first PWA features, chatbot assistance, quizzes, courses, live classes, notifications, complaints, and payment flows.

## Features

- Role-based access for `student`, `teacher`, and `admin`
- Course, lesson, quiz, assignment, and batch management
- AI chatbot and assistant workflow powered by Gemini API and NLP logic
- Online and offline chatbot support
- Progressive Web App support with service workers and smart caching
- Offline storage using IndexedDB
- Background synchronization for queued actions
- Multilingual UI support for English, Hindi, and Punjabi
- Teacher analytics, attendance, uploads, and live class management
- Admin approvals, reports, audit logs, and dashboard analytics
- Payment and cart flow for course purchases
- Notification system for updates and approvals
- Responsive UI with modern frontend styling

## Tech Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend: Vite, Tailwind CSS, Alpine.js
- Database: MySQL
- AI/NLP: Gemini API, chatbot services, NLP helpers
- PWA: Service workers, manifest, IndexedDB, background sync
- Testing: PHPUnit

## Project Structure

- `app/Http/Controllers` - request handling and feature logic
- `app/Models` - Eloquent models
- `app/Services` - chatbot, AI, translation, audit, and OTP services
- `app/Jobs` - asynchronous background jobs
- `database/migrations` - database schema
- `resources/views` - Blade templates for dashboards and pages
- `public/sw.js` - service worker for offline support
- `public/js/pwa-offline-store.js` - offline queue and local storage helpers
- `lang/` - multilingual translation files

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js 18+ and npm
- MySQL

## Installation

1. Clone the repository

   ```bash
   git clone https://github.com/alanemohan/capstoneproj.git
   cd capstoneproj
   ```

2. Install PHP dependencies

   ```bash
   composer install
   ```

3. Install frontend dependencies

   ```bash
   npm install
   ```

4. Configure environment variables

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

5. Set your database and service credentials in `.env`

   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`
   - `GEMINI_API_KEY` if AI chat is enabled

6. Run migrations

   ```bash
   php artisan migrate
   ```

7. Start the application in development mode

   ```bash
   php artisan serve
   npm run dev
   ```

## Available Scripts

- `composer run dev` - starts Laravel server, queue listener, logs, and Vite together
- `composer run test` - clears config and runs the test suite
- `npm run dev` - starts the Vite development server
- `npm run build` - creates a production frontend build

## Key Modules

### Student Dashboard

- Courses and lessons
- Quizzes and results
- AI chatbot
- Live classes
- Government schemes
- Notifications
- Complaints/help system
- Cart and payments
- Offline support

### Teacher Dashboard

- Course upload and management
- Lesson and video upload
- Quiz creation and analytics
- Attendance tracking
- Reports and notifications

### Admin Dashboard

- User and teacher approvals
- Analytics and reports
- Complaint management
- Quiz approvals
- Revenue tracking
- Notifications and audit logs

## PWA and Offline Flow

- The app uses a service worker for caching and offline availability.
- IndexedDB stores offline actions and chatbot-related data locally.
- Background sync helps submit queued updates once the network is restored.
- Smart caching improves load speed for repeated visits.

## AI Chatbot Flow

- User asks a question from the dashboard.
- The app checks whether online AI is available.
- If online, the request is processed through the Gemini API and chatbot service.
- If offline, the app responds from cached knowledge or local FAQ data when possible.
- Chat history and feedback can be stored for future improvement.

## Multilingual Support

The UI supports:

- English
- Hindi
- Punjabi

Translation files are maintained in the `lang/` directory.

## Deployment Notes

- Set the production `.env` values correctly.
- Run `composer install --no-dev` and `npm run build` for production.
- Configure the web server to point to the `public/` directory.
- Ensure MySQL, queue workers, and scheduler are configured if background jobs are used.

## Future Enhancements

- Better real-time notifications through WebSockets
- More advanced AI personalization
- Improved recommendation engine for courses
- Stronger analytics and reporting dashboards
- Dedicated mobile app wrapper

## License

This project is for academic and demonstration purposes.
