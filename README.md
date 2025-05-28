# Black Stars Solo Tournament System

## Description
Black Stars Solo Tournament System is a web application designed to manage solo football tournaments and leagues. It provides separate roles for administrators and players, enabling efficient management of tournaments, leagues, matches, and player participation.
## Live Demo
Visit the live system: [https://blackstarssolo.unaux.com](https://blackstarssolo.unaux.com)


## Features

### Admin
- Create, view, and manage leagues and tournaments
- Start leagues and generate fixtures automatically
- Manage matches (edit, delete)
- Approve or reject tournament join requests
- Dashboard for quick access to management features

### Player
- Register and login to the system
- Join leagues and solo tournaments
- View joined leagues and tournaments
- Update match results and scores
- View fixtures and standings for leagues and tournaments
- Dashboard for personalized overview and quick actions

## Technology Stack
- PHP for backend logic
- MySQL (MariaDB) for database management
- Tailwind CSS for frontend styling

## Installation
1. Clone or download the repository to your local server.
2. Import the database schema from `efootball_club_db.sql` into your MySQL server.
3. Update the database connection settings in `config/db.php` if necessary.
4. Ensure your web server supports PHP and has access to the project files.
5. Access the application via your web browser at the server URL.

## Usage
- Access the homepage (`index.php`) to choose your role (Admin or Player).
- Admins can log in via `admin/login.php` and manage leagues, tournaments, and matches.
- Players can log in via `player/login.php` to join leagues and tournaments, update match results, and view standings.

## Database Schema Overview
The system uses the following key tables:
- `admins`: Administrator accounts
- `users`: Player accounts
- `leagues`: League information
- `league_matches`: Matches within leagues
- `league_player`: Players participating in leagues
- `league_registrations`: User registrations for leagues
- `matches`: General match records
- `tournaments`: Tournament information
- `tournament_join_requests`: Player requests to join tournaments
- `tournament_matches`: Matches within tournaments
- `tournament_players`: Players participating in tournaments

## File Structure
- `index.php`: Main entry point with role selection and tournament overview
- `config/`: Configuration files including database connection
- `admin/`: Admin panel files for managing leagues, tournaments, matches, and user requests
- `player/`: Player panel files for joining leagues/tournaments, updating results, and viewing standings
- `assets/`: CSS and JavaScript assets
- `includes/`: Common header and footer includes
- `efootball_club_db.sql`: Database schema and initial data dump

## License
This project is provided as-is without any explicit license.
