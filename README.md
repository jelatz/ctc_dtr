# CallTek Daily Time Record (DTR) System

A robust and professional Daily Time Record (DTR) system designed for streamlined attendance tracking. This system integrates seamlessly with an external QIS database to ensure synchronized employee data and work schedules.

## 🚀 Tech Stack

- **Backend**: Laravel **11**
- **Frontend**: Vue.js 3 (via Inertia.js)
- **Styling**: Tailwind CSS
- **Build Tool**: Vite
- **Database**: MySQL (Local) & MySQL (External QIS)

## ✨ Key Features

- **Time In/Out Tracking**: Simple and intuitive interface for employees to log their daily attendance.
- **Automated Data Synchronization**:
    - **User Sync**: Specialized scheduler to fetch and update employee profiles from the `mysql_qis` database.
    - **Schedule Sync**: Automated fetching of employee work schedules, including shift start/end times and day-off statuses.
- **Reliable QIS Integration**:
    - Real-time synchronization of DTR entries to the QIS `dtr_data` and `AttendanceHistoryLogs` tables.
    - **Resilient Logging**: Failed sync attempts are stored locally in `dtr_logs` with an `is_imported` flag.
    - **Retry Mechanism**: A console command automatically retries syncing pending logs to ensure data integrity.
- **Employee Verification**: Real-time employee lookup with photo integration via the CHRIS system.

## 📊 Database Schema

### Core Tables

| Table | Description | Key Columns |
| :--- | :--- | :--- |
| **users** | Local cache of employee profiles. | `employee_id` (UK), `name` |
| **schedules** | Employee work schedules synced from QIS. | `employee_id`, `sched_date`, `sched_start`, `sched_end`, `day_type` |
| **dtr** | Primary record of daily attendance. | `employee_id`, `dtr_date`, `time_in`, `time_out` |
| **dtr_logs** | Internal audit trail and sync tracking. | `employee_id`, `dtr_date`, `type`, `is_imported` |

## 🛠️ Installation & Setup

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd calltek_dtr
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   Duplicate `.env.example` to `.env` and configure your database connections:
   - `DB_CONNECTION`: Local database for DTR operations.
   - `DB_CONNECTION_QIS`: Connection details for the external QIS database.

4. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

5. **Initialize Sync**:
   ```bash
   php artisan app:sync-user
   php artisan app:sync-schedule
   ```

6. **Start Development Server**:
   ```bash
   php artisan serve
   npm run dev
   ```

## 📖 Usage

### For Employees
1. **Identify**: Enter your **Employee ID** on the terminal screen.
2. **Verify**: The system will display your name, photo and schedules for verification.
3. **Log**: Click **Confirm Login** or **Confirm Logout**. The system will automatically detect the correct schedule date (handling overnight shifts up to 6 hours after the shift end).
4. **Confirm**: A success message will appear once the log is recorded.

### For Administrators
Attendance data is automatically synced to the QIS system in the background. If a network interruption occurs, the system will mark the record as pending and retry via the `app:sync-dtr-to-qis` command.

---
*Developed for CallTek*
