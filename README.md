
# Team Project Management System

## Overview

The **Team Project Management System** is a robust application designed to manage team projects, assign tasks to users, track task statuses, and manage user roles within projects. The system supports authentication, authorization, and allows administrators to manage projects, tasks, and users effectively.

### Features:
- User registration and authentication (JWT-based).
- Admin role for managing users and projects.
- Project creation and assignment of users to projects.
- Task management within projects (create, update, filter tasks).
- View tasks assigned to specific users within a project.
- Track task status (e.g., completed, pending).
- Role-based access control for admin and regular users.

## Table of Contents

- [Installation](#installation)
- [Environment Variables](#environment-variables)
- [API Endpoints](#api-endpoints)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [License](#license)

## Installation

To set up the project locally, follow the steps below:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/zzeeii/Team_Project_Management_System.git
   cd Team_Project_Management_System
   ```

2. **Install Dependencies:**
   Make sure you have [Composer](https://getcomposer.org/) installed, then run:
   ```bash
   composer install
   ```

3. **Set Up Environment:**
   Copy the `.env.example` to create your environment configuration:
   ```bash
   cp .env.example .env
   ```

4. **Configure the `.env` file:**
   Set your database credentials and other environment variables:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=Team_Project_Management_System
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run Database Migrations:**
   Migrate the database tables:
   ```bash
   php artisan migrate
   ```

6. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

7. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   The application will run on [http://localhost:8000](http://localhost:8000).

## Environment Variables

Ensure you configure the following variables in your `.env` file:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Team_Project_Management_System
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your_jwt_secret
```

You can generate a new JWT secret using:
```bash
php artisan jwt:secret
```

## API Endpoints

Below is a list of available API endpoints:

### Authentication
- **POST** `/login` - User login.
- **POST** `/users` - Register a new user.
- **POST** `/logout` - Logout user.
- **POST** `/refresh` - Refresh JWT token.

### Projects
- **GET** `/projects` - Get all projects.
- **POST** `/projects` - Create a new project.
- **GET** `/projects/{project}` - Get a specific project.
- **PUT** `/projects/{project}` - Update a specific project.
- **DELETE** `/projects/{project}` - Delete a project (Admin only).

### Tasks
- **GET** `/projects/{project}/tasks` - Get tasks for a project.
- **POST** `/projects/{project}/tasks` - Create a task for a project.
- **PUT** `/projects/{project}/tasks/{id}/status` - Update the status of a task.
- **POST** `/projects/{project}/tasks/{id}/note` - Add a note to a task.
- **GET** `/projects/{project}/tasks/filter` - Filter tasks based on priority/status.

### Users
- **POST** `/projects/{project}/users` - Add a user to a project.
- **GET** `/users/{user}/projects/{project}/tasks` - Get tasks assigned to a specific user in a project.
- **DELETE** `/users/{id}` - Delete a user (Admin only).

### Task Priority & Time Tracking
- **GET** `/projects/{project}/tasks/latest` - Get the latest task in the project.
- **GET** `/projects/{project}/tasks/oldest` - Get the oldest task in the project.
- **GET** `/projects/{project}/tasks/highest-priority` - Get the task with the highest priority.

## Project Structure

The project's key directories and files are structured as follows:

```bash
app/
├── Http/
│   ├── Controllers/   # API Controllers
│   ├── Requests/      # Form Requests for validation
├── Models/            # Eloquent Models
├── Services/          # Business logic services

routes/
├── api.php            # API routes
```

## Usage

1. **Admin User**: The initial user registering becomes the Admin.
2. **Adding Users to Projects**: Once projects are created, users can be added to the projects.
3. **Task Management**: Each project can have multiple tasks, which can be filtered, updated, or assigned to users.
4. **JWT Authentication**: All routes (except login/register) require a valid JWT token to access.

