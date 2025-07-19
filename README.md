# Memory Match Laundry Game

## Overview

The **Memory Match Laundry Game** is an interactive game built to engage users while they wait for their laundry orders. It consists of three main files:

- **memory_game.php**: Displays the game grid and handles the card-flipping and matching logic.
- **award_points.php**: Awards loyalty points based on the user’s performance.
- **log_game_play.php**: Logs the game activity (score and time) into the database.

## Files Overview

### 1. **memory_game.php**

This file contains the game interface and basic logic for the Memory Match game.

- **Game Setup**: Creates a grid of cards for the player to flip.
- **Matching Logic**: Checks for matching pairs and updates the score.

### 2. **award_points.php**

After the game, this file calculates the points earned based on the user’s score and time.

- **Points Awarding**: Points are awarded depending on the game result (higher score = more points).
- **Update User Profile**: Adds the points to the user’s account.

### 3. **log_game_play.php**

This file logs the user’s game performance (score and time) into the database.

- **Game Logging**: Saves the user’s score and time for tracking future game activity.

## How to Run

1. **Set up the Environment**: Ensure you have a working PHP environment (e.g., **XAMPP** or **WAMP**).
   
2. **Database Setup**: Create a database (`laundry_game`) and necessary tables:
   - **users**: Stores user data and points.
   - **game_logs**: Logs game results (score, time).

3. **Deploy the Code**: Place the PHP files in your server’s root directory and configure the database connection.

4. **Play the Game**: Open `memory_game.php` in your browser, and the game will track your performance.

5. **Award Points**: Points are awarded through `award_points.php` after the game ends.

6. **Log Game Data**: Game sessions are logged by `log_game_play.php`.

---

**Developed by**: Amar Zamani Azzim Bin Ahmad Zaidi  
**Email**: amarzamani227@gmail.com  
**LinkedIn**: [amarzamani](https://www.linkedin.com/in/amarzamani)
