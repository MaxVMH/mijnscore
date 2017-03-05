<?php
require_once '../app/init.php';

$database = new Database;
$db_con = $database->db_con;

$query = $db_con->prepare("SELECT * FROM users WHERE user_lastlogin_datetime < ADDDATE(NOW(), INTERVAL -2 WEEK)");
$query->execute();
$inactive_users = $query->fetchAll();
foreach($inactive_users as $inactive_user)
{
  echo "Inactieve gebruiker <strong>" . $inactive_user['user_username'] . "</strong> gevonden. <br />";

  $query = $db_con->prepare("SELECT * FROM messages WHERE message_receiver_user_id=:user_id OR message_sender_user_id=:user_id2");
  $query->bindValue(':user_id', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->bindValue(':user_id2', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->execute();
  $inactive_user_messages = $query->fetchAll();
  foreach($inactive_user_messages as $inactive_user_message)
  {
    echo "Bericht gevonden van " . $inactive_user['user_username'] . ".<br />";
  }
  $query = $db_con->prepare("DELETE FROM messages WHERE message_receiver_user_id=:user_id OR message_sender_user_id=:user_id2");
  $query->bindValue(':user_id', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->bindValue(':user_id2', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->execute();



  $query = $db_con->prepare("SELECT * FROM predictions WHERE user_id=:user_id");
  $query->bindValue(':user_id', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->execute();
  $inactive_user_predictions = $query->fetchAll();
  foreach($inactive_user_predictions as $inactive_user_prediction)
  {
    echo "Pronostiek gevonden van " . $inactive_user['user_username'] . ".<br />";
  }
  $query = $db_con->prepare("DELETE FROM predictions WHERE user_id=:user_id");
  $query->bindValue(':user_id', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->execute();



  $query = $db_con->prepare("SELECT * FROM predictions_points WHERE user_id=:user_id");
  $query->bindValue(':user_id', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->execute();
  $inactive_user_predictions_points = $query->fetchAll();
  foreach($inactive_user_predictions_points as $inactive_user_prediction_points)
  {
    echo "Pronostiek punten gevonden van " . $inactive_user['user_username'] . " (competitie " . $inactive_user_prediction_points['league_id'] . " speeldag " . $inactive_user_prediction_points['league_playday'] . ").<br />";
  }
  $query = $db_con->prepare("DELETE FROM predictions_points WHERE user_id=:user_id");
  $query->bindValue(':user_id', $inactive_user['user_id'], PDO::PARAM_STR);
  $query->execute();
}
