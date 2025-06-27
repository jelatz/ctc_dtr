<h1>CALLTEK DTR SYSTEM</h1>

<p>Table of Contents</p>

- Overview
- Features
- Tech Stack
- Installation
- Configuration
- Usage

<h1>Overview</h1>

<p>This is an enhanced internal web-based system used for tracking employee attendance of Calltek. </p>

<h1>Features</h1>

- Employee Login and time in/out.

<h1>Tech Stack</h1>

- Backend: PHP (LARAVEL)
- Frontend: Vue3 with Inertia JS and Tailwind
- Database: Sqlite (For now)
- Other: Axios

<h1>Installation</h1>

- git clone git@github.com:jelatz/ctc_dtr.git
- cd ctc_dtr
- composer i or composer install
- composer require tightenco/ziggy
- npm install axios
- composer run dev (To run the project)

<h1>Configuration</h1>

- DB_CONNECTION=sqlite

Run Migrations

- php artisan migrate
- after running migration create a user in sqlite database
- php artisan db:seed --class=ScheduleSeeder

<h1>Usage</h1>

- Visit: http://localhost/8000
- Enter created employee ID
- login / logout
