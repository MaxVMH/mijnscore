<?php
  class User
  {
    public function create_user($db_con, $username, $password, $email, $ip)
    {
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      $query = $db_con->prepare('INSERT INTO users(user_username, user_password_hash, user_email, user_active, user_rank, user_registration_datetime, user_registration_ip) VALUES(:username, :password_hash, :email, 1, 1, NOW(), :ip)');
      $query->bindValue('username', $username, PDO::PARAM_STR);
      $query->bindValue('password_hash', $password_hash, PDO::PARAM_STR);
      $query->bindValue('email', $email, PDO::PARAM_STR);
      $query->bindValue('ip', $ip, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_loggedin($db_con, $id)
    {
      $token = hash('sha256', mt_rand());
      $_SESSION['login_token'] = $token;
      $_SESSION['user_id'] = $id;
      $query = $db_con->prepare('UPDATE users SET user_lastlogin_token=:token, user_lastlogin_datetime=NOW(), user_lastfail_amount=0 WHERE user_id=:id');
      $query->bindValue(':token', $token, PDO::PARAM_STR);
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_loggedin_failed($db_con, $id)
    {
      $query = $db_con->prepare('UPDATE users SET user_lastfail_datetime=NOW(), user_lastfail_amount=(user_lastfail_amount+1) WHERE user_id=:id');
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_loggedout($db_con)
    {
      $result = false;

      if(!empty($_SESSION['user_id']) && !empty($_SESSION['login_token']) && ($_SESSION['login_token'] == $this->get_user_loggedin($db_con)['user_lastlogin_token']))
      {
          $query = $db_con->prepare('UPDATE users SET user_lastlogin_token=0 WHERE user_id=:id');
          $query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_STR);
          $result = $query->execute();

          $_SESSION = array();
          session_destroy();
      }

      return $result;
    }

    public function get_user_loggedin($db_con)
    {
      $result = false;

      if(!empty($_SESSION['user_id']) && !empty($_SESSION['login_token']))
      {
        $query = $db_con->prepare('
          SELECT *, (SELECT COUNT(*) FROM messages WHERE message_receiver_user_id=users.user_id AND message_receiver_status=2) as user_unread_messages
          FROM users WHERE user_id=:id AND user_lastlogin_token=:token AND user_lastlogin_datetime>(NOW() - INTERVAL 1 HOUR)
        ');
        $query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_STR);
        $query->bindValue(':token', $_SESSION['login_token'], PDO::PARAM_STR);
        $query->execute();

        if($result = $query->fetch())
        {
          $this->set_user_loggedin($db_con, $_SESSION['user_id']);
        }
      }

      return $result;
    }

    public function get_user_by_id($db_con, $id)
    {
      $query = $db_con->prepare('SELECT * FROM users WHERE user_id=:id');
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      $query->execute();
      return $query->fetch();
    }

    public function get_user_by_username($db_con, $username)
    {
      $query = $db_con->prepare('SELECT * FROM users WHERE user_username=:username');
      $query->bindValue(':username', $username, PDO::PARAM_STR);
      $query->execute();
      return $query->fetch();
    }

    public function get_users_email_notifications($db_con)
    {
      $query = $db_con->prepare('SELECT * FROM users WHERE user_email_notification=1');
      $query->execute();
      return $query->fetchAll();
    }

    public function get_user_by_email($db_con, $email)
    {
      $query = $db_con->prepare('SELECT * FROM users WHERE user_email=:email');
      $query->bindValue(':email', $email, PDO::PARAM_STR);
      $query->execute();
      return $query->fetch();
    }

    public function set_user_email($db_con, $id, $email)
    {
      $query = $db_con->prepare('UPDATE users SET user_email=:email WHERE user_id=:id');
      $query->bindValue(':email', $email, PDO::PARAM_STR);
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_email_verification_hash($db_con, $id, $email_hash)
    {
      $query = $db_con->prepare('UPDATE users SET user_email_verification_hash=:email_hash WHERE user_id=:id');
      $query->bindValue(':email_hash', $email_hash, PDO::PARAM_STR);
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_email_verification($db_con, $id, $email_verification)
    {
      $query = $db_con->prepare('UPDATE users SET user_email_verification=:email_verification WHERE user_id=:id');
      $query->bindValue(':email_verification', $email_verification, PDO::PARAM_STR);
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_email_notification($db_con, $id, $email_notification)
    {
      $query = $db_con->prepare('UPDATE users SET user_email_notification=:email_notification WHERE user_id=:id');
      $query->bindValue(':email_notification', $email_notification, PDO::PARAM_STR);
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

    public function set_user_password($db_con, $id, $password)
    {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $query = $db_con->prepare('UPDATE users SET user_password_hash=:hash WHERE user_id=:id');
      $query->bindValue(':hash', $hash, PDO::PARAM_STR);
      $query->bindValue(':id', $id, PDO::PARAM_STR);
      return $query->execute();
    }

  }
?>
