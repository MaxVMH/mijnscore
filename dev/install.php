<?php
// this file is the database installer
// before you continue, make sure you filled in your MySQL credentials in /app/init.php
// install the database by moving this file to /app/public_html/ and run it in your browser
// afterwards, move this file back to /app/dev/ (wouldn't be harmfull when you run it again but we like to keep things clean you know)
require_once '../app/init.php';

$database = new Database;
$db_connection = $database->db_con;

$query_users="CREATE TABLE `users` (
  `user_id` INT(8) NOT NULL AUTO_INCREMENT,
  `user_username` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_password_hash` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_active` TINYINT(1) NOT NULL DEFAULT '0',
  `user_rank` TINYINT(1) NOT NULL DEFAULT '0',
  `user_lastlogin_datetime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `user_lastlogin_token` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_lastfail_datetime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `user_lastfail_amount` TINYINT(1) NOT NULL DEFAULT '0',
  `user_registration_datetime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `user_registration_ip` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',

  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_username` (`user_username`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_messages="CREATE TABLE `messages` (
  `message_id` INT(10) NOT NULL AUTO_INCREMENT,
  `message_title` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `message_text` TEXT NOT NULL,
  `message_datetime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `message_sender_user_id` INT(8) NOT NULL,
  `message_sender_status` TINYINT(1) NOT NULL DEFAULT '0',
  `message_receiver_user_id` INT(8) NOT NULL,
  `message_receiver_status` TINYINT(1) NOT NULL DEFAULT '0',

  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_leagues="CREATE TABLE `leagues` (
  `league_id` INT(3) NOT NULL AUTO_INCREMENT,
  `league_parent_id` INT(3),
  `league_tag` VARCHAR(17) COLLATE utf8_unicode_ci NOT NULL,
  `league_name` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `league_playday_current` INT(3) NOT NULL DEFAULT 0,
  `league_playday_total` INT(3) NOT NULL DEFAULT 0,
  `league_status` INT(1) NOT NULL DEFAULT 0,
  `league_type` INT(1) NOT NULL DEFAULT 0,

  PRIMARY KEY (`league_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_teams="CREATE TABLE `teams` (
  `team_id` INT(4) NOT NULL AUTO_INCREMENT,
  `team_tag` VARCHAR(17) COLLATE utf8_unicode_ci NOT NULL,
  `team_name` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `team_status` INT(1) NOT NULL DEFAULT 0,

  PRIMARY KEY (`team_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_teams_leagues="CREATE TABLE `teams_leagues` (
  `teams_leagues_id` INT(4) NOT NULL AUTO_INCREMENT,
  `team_id` INT(4) NOT NULL DEFAULT 0,
  `league_id` INT(3) NOT NULL DEFAULT 0,

  PRIMARY KEY (`teams_leagues_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_matches="CREATE TABLE `matches` (
  `match_id` INT(6) NOT NULL AUTO_INCREMENT,
  `match_status` TINYINT(1) NOT NULL DEFAULT 0,
  `match_datetime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `match_info` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `league_id` INT(3) NOT NULL DEFAULT 0,
  `league_playday` INT(3) NOT NULL DEFAULT 0,
  `home_team_id` INT(4) NOT NULL DEFAULT 0,
  `home_team_score` INT(3) NOT NULL DEFAULT 0,
  `away_team_id` INT(4) NOT NULL DEFAULT 0,
  `away_team_score` INT(3) NOT NULL DEFAULT 0,

  PRIMARY KEY (`match_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_teams_points="CREATE TABLE `teams_points` (
  `points_id` INT(6) NOT NULL AUTO_INCREMENT,
  `league_id` INT(3) COLLATE utf8_unicode_ci NOT NULL,
  `team_id` INT(4) NOT NULL DEFAULT 0,
  `points_amount` INT(3) NOT NULL DEFAULT 0,
	`matches_won` INT(3) NOT NULL DEFAULT 0,
	`matches_draw` INT(3) NOT NULL DEFAULT 0,
  `matches_lost` INT(3) NOT NULL DEFAULT 0,
	`goals_scored` INT(4) NOT NULL DEFAULT 0,
	`goals_allowed` INT(4) NOT NULL DEFAULT 0,

  PRIMARY KEY (`points_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_predictions="CREATE TABLE `predictions` (
  `prediction_id` INT(10) NOT NULL AUTO_INCREMENT,
  `match_id` INT(6) NOT NULL DEFAULT 0,
  `user_id` INT(8) NOT NULL DEFAULT 0,
  `home_team_score` INT(3) NOT NULL DEFAULT 0,
  `away_team_score` INT(3) NOT NULL DEFAULT 0,
  `prediction_points` INT(1) NOT NULL DEFAULT 0,

  PRIMARY KEY (`prediction_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_predictions_points="CREATE TABLE `predictions_points` (
  `points_id` INT(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(8) NOT NULL DEFAULT 0,
  `league_id` INT(3) NOT NULL DEFAULT 0,
  `league_playday` INT(3) NOT NULL DEFAULT 0,
  `league_user_ranking` INT(8) NOT NULL DEFAULT 0,
  `points_amount` INT(5) NOT NULL DEFAULT 0,

  PRIMARY KEY (`points_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";


$create_users = $db_connection->prepare($query_users);
if($create_users->execute())
echo "Table users created. <br />";
else
echo "Table users not created <br />";

$create_messages = $db_connection->prepare($query_messages);
if($create_messages->execute())
echo "Table messages created <br />";
else
echo "Table messages not created <br />";

$create_leagues = $db_connection->prepare($query_leagues);
if($create_leagues->execute())
echo "Table leagues created <br />";
else
echo "Table leagues not created <br />";

$create_teams = $db_connection->prepare($query_teams);
if($create_teams->execute())
echo "Table teams created <br />";
else
echo "Table teams not created <br />";

$create_teams_leagues = $db_connection->prepare($query_teams_leagues);
if($create_teams_leagues->execute())
echo "Table teams_leagues created <br />";
else
echo "Table teams_leagues not created <br />";

$create_matches = $db_connection->prepare($query_matches);
if($create_matches->execute())
echo "Table matches created <br />";
else
echo "Table matches not created <br />";

$create_teams_points = $db_connection->prepare($query_teams_points);
if($create_teams_points->execute())
echo "Table teams_points created <br />";
else
echo "Table teams_points not created <br />";

$create_predictions = $db_connection->prepare($query_predictions);
if($create_predictions->execute())
echo "Table predictions created <br />";
else
echo "Table predictions not created <br />";

$create_predictions_points = $db_connection->prepare($query_predictions_points);
if($create_predictions_points->execute())
echo "Table predictions_points created <br />";
else
echo "Table predictions_points not created <br />";
?>
