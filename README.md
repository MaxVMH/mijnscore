# mijnscore

An unfinished soccer prediction web app written in PHP & HTML.

# Installation instructions

* Unzip and move the files to an appropiate folder

* Apache needs the mijnscore /public_html/ folder set as document root folder

* Apache needs mod_rewrite enabled

* Edit /app/init.php, enter your database information and ip / url

* Move install.php from /dev/ to /public_html/

* Open install.php in your browser (examples: www.example.com/install.php or http://localhost/install.php)

* Open the website in your browser (examples: www.example.com or http://localhost), register an account and log in

* Move install.php from /public_html/ back to /dev/

* Remove /dev/ and all of its content

* Access your database with something like phpmyadmin, go to the users table, and edit the user_rank field to '9' (instead of '1') for your account (that gives you admin rights)

# Getting started

* Open the website in your browser, log in with the admin account if you aren't already, create a new league, create teams and create the first few matches.

* Leagues have an option to be hidden for users, enabled by default. Make sure to edit the league and set it to active.

* Matches have an option to not allow predictions, enabled by default. This option is hidden. Just edit the match once (you don't have to change anything, but make sure you press the edit button at the bottom of the match edit form). This is to force administrators to check the match details before users can start entering their predictions. This will be changed when the admin match creator gets a rework.

* Every time the predictions page gets loaded, it automatically checks if a match has begun or not. To confirm your server time, simply go to your profile page and look at the last activity date/time (refresh if necessary).

* When a match has finished, enter the score and let mijnscore calculate the user points and rankings from the administrator menu. You can also calculate the teams points and rankings.

* Relationships between leagues is modeled after the Belgian play-offs system. Example: League A is for the regular season. League B is for the play-offs and has a relationship with League A. The season overall user ranking in League B includes half of the points of League A.
