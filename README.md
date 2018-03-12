# mijnscore

An unfinished soccer prediction web app written in PHP & HTML.

# Requirements

* Apache with mod_rewrite enabled and support for .htaccess

# Installation instructions

* Unzip and move the files to an appropriate folder

* Set the mijnscore /public_html/ folder set as document root in Apache

* Edit /app/init.php (enter your database information and ip / url)

* Open install.php in your browser (eg. www.example.com/install.php)

* Open the website in your browser (eg. www.example.com) and register an account

* Remove install.php

* Access your database (with something like phpmyadmin), go to the users table and edit the user_rank field to '9' for your account (that gives you admin rights)

# Getting started

* Open the website in your browser, log in with the admin account and create a new league.

* Leagues have an option to be hidden for users, enabled by default. Make sure to edit the league and set it to active.

* Add your first matches.

* When a match has finished, enter the score (make sure to check the score checkbox) and let mijnscore calculate the user points and rankings from the administrator menu. You can also let mijnscore calculate the teams points and rankings if necessary.

# For your information

* Every time the predictions page gets loaded, it automatically checks if a match has begun or not. To confirm your server time, simply go to your profile page and look at the last activity date/time (refresh if necessary).

* If you want to use a play-offs system that includes half of the users points of the regular season: create a new league for the play-offs and set 'relationship' to the regular season league.
