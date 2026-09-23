# MusicCulture

MusicCulture is a web platform designed for musicians to connect with each other, share their musical interests, and develop their musical knowledge and skills.

The project combines two main areas:

- A social platform for musicians
- A music development and training platform

The application is built as a server-side PHP application with a MySQL database and runs locally using Docker.


## Overview

MusicCulture allows users to create an account, build a musician profile, connect with other musicians, publish posts, comment on content, send messages, and receive notifications.

The platform also provides music-development features such as music theory quizzes, music games, skill progression, and Ear Training.

The goal of the project is to combine social interaction with continuous musical development in a single platform.


## Features

### Social Platform

- User registration and login
- Session-based authentication
- Musician profiles
- Instrument information
- Musical style selection
- Profile avatars
- Create and view posts
- Comments
- Likes
- Follow relationships
- Private messaging
- Notifications
- User search

### Music Development

- Music level assessment
- Music theory quizzes
- Quiz score tracking
- Skill-level progression
- Music Games
- Level-based music exercises
- Melody reproduction exercises
- Music Training area
- Ear Training
- Interval recognition
- Training results and accuracy tracking

## Technologies

### Backend

- PHP 8.2
- Apache
- MySQL 8.0
- PDO
- PHP Sessions

### Frontend

- HTML5
- CSS3
- SCSS
- JavaScript
- Web Audio API

### Development and Infrastructure

- Docker
- Docker Compose
- Git
- GitHub


## Architecture

MusicCulture uses a server-side PHP architecture.

The application is organized into feature-based directories. PHP pages handle application logic and database communication, while shared components provide common navigation and frontend assets.

The main application flow is:

```text
Browser
   │
   ▼
Apache / PHP 8.2
   │
   ├── Authentication middleware
   │       └── PHP session
   │
   ├── Application logic
   │       ├── Social features
   │       ├── User profiles
   │       ├── Quizzes
   │       └── Music Games / Training
   │
   ├── Shared components
   │       ├── Header
   │       ├── Navigation
   │       └── JavaScript
   │
   └── PDO
          │
          ▼
      MySQL 8.0
```

## Project Structure

```text

musicculture/
├── assets/
│   ├── css/
│   └── js/
├── auth/
├── comments/
├── config/
├── dashboard/
├── includes/
├── messages/
├── middleware/
├── music_games/
│   ├── assets/
│   ├── level4/
│   └── training/
├── notifications/
├── posts/
├── profile/
├── quiz/
├── search/
├── uploads/
│   └── avatars/
└── users/

```

## How the Technologies Work Together

MusicCulture combines PHP, MySQL, HTML, CSS, JavaScript, and Docker.
1. The browser sends an HTTP request to Apache.
2. Apache executes the requested PHP page.
3. Authentication middleware checks the PHP session when authentication is required.
4. PHP performs the application logic.
5. PDO is used to communicate with MySQL.
6. Data retrieved from MySQL is used to generate the HTML response.
7. Shared components such as the navigation and common assets are loaded from the includes/ and assets/ directories.
8. CSS and JavaScript provide styling and frontend interaction.
9. Music Training features can use JavaScript and the Web Audio API for browser-based audio interaction.

## Security

MusicCulture includes several basic security measures:

- User passwords are securely hashed using PHP's `password_hash()` with `PASSWORD_DEFAULT`.
- Passwords are verified with `password_verify()` during login.
- Database operations use PDO prepared statements to reduce SQL injection risks.
- User email addresses are validated before registration.
- Authentication state is stored in PHP sessions.
- The session ID is regenerated after successful login to reduce session fixation risks.
- Protected pages use authentication middleware to verify that a user is logged in.
- User-facing values are escaped with `htmlspecialchars()` where they are rendered as HTML.
- Database credentials are provided through environment variables rather than being hard-coded in the application source.
- Avatar uploads are restricted to selected image MIME types and a maximum file size of 2 MB.

### Current security limitations

This project is a portfolio application and does not yet implement every security layer expected in a production system. For example:

- CSRF protection has not yet been implemented for state-changing forms.
- File upload validation could be strengthened by verifying the actual image contents and handling uploaded files more defensively.
- Production deployment would require additional server-level security configuration, HTTPS, secure cookie settings, rate limiting, and other hardening measures.

## Database

MusicCulture uses MySQL 8.0 as its relational database.

The database currently contains 13 main tables:

- `users` — user accounts and profile information
- `music_styles` — available musical styles
- `user_music_styles` — relationship between users and their selected musical styles
- `posts` — social posts created by users
- `comments` — comments associated with posts
- `likes` — post likes
- `follows` — relationships between users
- `messages` — private user messages
- `notifications` — user notifications
- `quiz_questions` — music theory quiz questions
- `quiz_results` — users' quiz results
- `music_games` — music game questions and exercises
- `game_results` — results from music games and training activities

The users table acts as the central entity of the application. User IDs connect users with social features such as posts, comments, follows, and messages, as well as music-development features.
The application accesses MySQL through PDO. Database credentials are provided through environment variables.

### Main Relationships

The application connects related records through identifier columns:

- A `users` record can have many `posts`.
- A `post` can have many `comments`.
- A `user` can create many `comments`.
- Users can follow other users through the `follows` table.
- Users can send and receive messages through the `messages` table.
- Users can select multiple musical styles through `user_music_styles`.
- A musical style can be associated with multiple users through `user_music_styles`.

The application uses user IDs to connect records across these tables.

## Local Installation

### Requirements

- Docker
- Docker Compose
- Git

### 1. Clone the repository

```bash
git clone https://github.com/Worksalohy/musician-social-platform.git
cd musician-social-platform
```

### 2. Create the environment file

Copy the example environment file:
```bash
cp .env.example .env
```

Update the values in .env with your local database credentials.
Example:

```env
MYSQL_ROOT_PASSWORD=change_me
MYSQL_DATABASE=musician_social_platform
MYSQL_USER=change_me
MYSQL_PASSWORD=change_me
```

### 3. Build and start the containers
docker compose up --build

The PHP application will be available at:
http://localhost:8000

The MySQL container exposes port 3307 on the host.
Inside the Docker network, the PHP application connects to MySQL using the hostname:
mysql

and the MySQL container port:
3306

### Database setup
The repository currently does not include an SQL schema or database dump.
The required MySQL database and tables therefore need to be provisioned separately before using the complete application.

## Usage

After starting the application:
1. Open the application in a browser.
2. Create a MusicCulture account.
3. Log in.
4. Complete your musician profile.
5. Select your musical styles.
6. Explore the social features.
7. Take the music quizzes.
8. Progress through the available music games.
9. Unlock Music Training after meeting the required progression.
10. Practice using the available Ear Training exercises.

### Music Training

Music Training is designed as a reusable skill-development area rather than a final game level.
The current training area includes Ear Training and provides interval recognition exercises with different difficulty levels.
The training system tracks:
- Selected difficulty
- Generated challenges
- User answers
- Score
- Interval accuracy
- Training results
Additional training activities can be added to the same area as the project evolves.

### Current Limitations
MusicCulture is currently a portfolio project rather than a production service.
Some areas are intentionally simplified or still under development, including:
- The repository does not currently contain a database schema/dump file.
- Some Music Training activities are placeholders.
- Production-level security hardening has not yet been completed.
- The application currently focuses on core functionality and portfolio-quality implementation.

## Future Improvements

Possible future improvements include:
- Add a database migration or SQL schema system
- Implement CSRF protection
- Strengthen file upload validation
- Improve session and cookie security
- Add more music training activities
- Expand music theory and Ear Training exercises
- Improve automated testing
- Add more frontend interaction
- Improve deployment and production infrastructure


## Author

Salohiniaina RAKOTOVAOARY

Full-Stack Web Developer focused on frontend development, with a background in IT maintenance and web development.

GitHub:
https://github.com/Worksalohy
