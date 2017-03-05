<?php
  class Message
  {
    public function create_message($db_con, $title, $text, $sender, $receiver)
    {
      $query = $db_con->prepare('INSERT INTO messages(message_title, message_text, message_sender_user_id, message_receiver_user_id, message_datetime, message_sender_status, message_receiver_status) VALUES(:title, :text, :sender, :receiver, NOW(), 1,2)');
      $query->bindValue(':title', $title, PDO::PARAM_STR);
      $query->bindValue(':text', $text, PDO::PARAM_STR);
      $query->bindValue(':sender', $sender, PDO::PARAM_STR);
      $query->bindValue(':receiver', $receiver, PDO::PARAM_STR);
      return $query->execute();
    }

    public function get_inbox($db_con, $user_id)
    {
      $query = $db_con->prepare('
        SELECT
          messages.*,
          user_receiver.user_username as message_receiver_username,
          user_sender.user_username as message_sender_username
        FROM
          messages
        INNER JOIN
          users user_receiver ON messages.message_receiver_user_id = user_receiver.user_id
        INNER JOIN
          users user_sender ON messages.message_sender_user_id = user_sender.user_id
        WHERE
          message_receiver_user_id=:user_id
          AND
          message_receiver_status>0
        ORDER BY message_receiver_status DESC, message_datetime DESC
      ');
      $query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
      $query->execute();
      return $query->fetchAll();
    }

    public function get_outbox($db_con, $user_id)
    {
      $query = $db_con->prepare('
        SELECT
          messages.*,
          user_receiver.user_username as message_receiver_username,
          user_sender.user_username as message_sender_username
        FROM
          messages
          INNER JOIN
            users user_receiver
          ON
            messages.message_receiver_user_id = user_receiver.user_id
          INNER JOIN
            users user_sender
          ON
            messages.message_sender_user_id = user_sender.user_id
        WHERE
          message_sender_user_id=:user_id
          AND
          message_sender_status>0
        ORDER BY message_datetime DESC
      ');
      $query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
      $query->execute();
      return $query->fetchAll();
    }

    public function get_message($db_con, $message_id, $user_id)
    {
      $query = $db_con->prepare('
        SELECT
          messages.*,
          user_receiver.user_username as message_receiver_username,
          user_sender.user_username as message_sender_username
        FROM
          messages
          INNER JOIN
            users user_receiver
          ON
            messages.message_receiver_user_id = user_receiver.user_id
          INNER JOIN
            users user_sender
          ON
            messages.message_sender_user_id = user_sender.user_id
        WHERE
          message_id=:message_id
          AND(
            (message_receiver_user_id=:user_id AND message_receiver_status>0)
            OR
            (message_sender_user_id=:user_id2 AND message_sender_status>0)
          )
      ');
      $query->bindValue(':message_id', $message_id, PDO::PARAM_STR);
      $query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
      $query->bindValue(':user_id2', $user_id, PDO::PARAM_STR);
      $query->execute();
      return $query->fetch();
    }

    public function set_message_read($db_con, $message_id, $user_id)
    {
      $query_setread = $db_con->prepare('UPDATE messages SET message_receiver_status=1 WHERE message_id=:message_id AND message_receiver_user_id=:user_id AND message_receiver_status=2');
      $query_setread->bindValue(':message_id', $message_id, PDO::PARAM_STR);
      $query_setread->bindValue(':user_id', $user_id, PDO::PARAM_STR);
      return $query_setread->execute();
    }

    public function set_message_deleted($db_con, $message_id, $user_id)
    {
      $message = $this->get_message($db_con, $message_id, $user_id);

      if($message['message_receiver_user_id'] == $user_id && $message['message_receiver_status'] > 0)
      {
        return $this->set_message_deleted_as_receiver($db_con, $message_id, $user_id);
      }
      elseif($message['message_sender_user_id'] == $user_id && $message['message_sender_status'] > 0)
      {
        return $this->set_message_deleted_as_sender($db_con, $message_id, $user_id);
      }
      else
      {
        return false;
      }
    }

    public function set_message_deleted_as_receiver($db_con, $message_id, $user_id)
    {
      $delete_query = $db_con->prepare('UPDATE messages SET message_receiver_status=0 WHERE message_id=:message_id AND message_receiver_user_id=:user_id AND message_receiver_status>0');
      $delete_query->bindValue(':message_id', $message_id, PDO::PARAM_STR);
      $delete_query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
      return $delete_query->execute();
    }

    public function set_message_deleted_as_sender($db_con, $message_id, $user_id)
    {
      $delete_query = $db_con->prepare('UPDATE messages SET message_sender_status=0 WHERE message_id=:message_id AND message_sender_user_id=:user_id AND message_sender_status>0');
      $delete_query->bindValue(':message_id', $message_id, PDO::PARAM_STR);
      $delete_query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
      return $delete_query->execute();
    }

  }
?>
