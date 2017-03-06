<?php
  class messages_create extends Controller
  {
    protected $user;
    protected $message;

    public function __construct()
    {
			$this->db_con = $this->db_con();

      $this->user = $this->model('User');
      $this->league = $this->model('League');
      $this->message = $this->model('Message');

      $this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
      $this->view_data = [];
      $this->view_data['user_loggedin'] = $this->user_loggedin;
  		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);
    }

    public function form($reply_id='', $receiver_id='')
    {
      if($this->user_loggedin == false)
      {
        $this->view_data['notice'] = "U bent niet ingelogd.";
        $this->view('home/index', $this->view_data);
      }
      elseif($reply_message = $this->message->get_message($this->db_con, $reply_id, $this->user_loggedin['user_id']))
      {
        $this->view_data['message_title'] = "RE: " .$reply_message['message_title'];
        $this->view_data['message_receiver_username'] = $reply_message['message_sender_username'];
        $this->view('messages/form', $this->view_data);
      }
      elseif($message_receiver = $this->user->get_user_by_id($this->db_con, $receiver_id))
      {
        $this->view_data['message_receiver_username'] = $message_receiver['user_username'];
        $this->view('messages/form', $this->view_data);
      }
      else
      {
        $this->view('messages/form', $this->view_data);
      }
    }

    public function submit()
    {
      if($this->user_loggedin == false)
      {
        $this->view_data['notice'] = "U bent niet ingelogd.";
        $this->view('home/index', $this->view_data);
      }
      elseif(empty($_POST['receiver_username']))
      {
        $this->view_data['notice'] = "Vul een gebruikersnaam in.";
        $this->view('messages/form', $this->view_data);
      }
      elseif(empty($_POST['title']))
      {
        $this->view_data['notice'] = "Vul een onderwerp in.";
        $this->view('messages/form', $this->view_data);
      }
      elseif(empty($_POST['text']))
      {
        $this->view_data['notice'] = "Vul een bericht in.";
        $this->view('messages/form', $this->view_data);
      }
      elseif($this->user->get_user_by_username($this->db_con, $_POST['receiver_username']) == false)
      {
        $this->view_data['notice'] = "De opgegeven gebruikersnaam bestaat niet.";
        $this->view('messages/form', $this->view_data);
      }
      elseif($this->message->create_message($this->db_con, $_POST['title'], $_POST['text'], $this->user_loggedin['user_id'], $this->user->get_user_by_username($this->db_con, $_POST['receiver_username'])['user_id']))
      {
        $this->view_data['notice'] = "Uw bericht werd verzonden.";
        $this->view_data['messages_box'] = "Uitgaand";
        $this->view_data['messages'] = $this->message->get_outbox($this->db_con, $this->user_loggedin['user_id']);
        $this->view('messages/multiple', $this->view_data);
      }
      else
      {
        $this->view_data['notice'] = "Er is iets fout gegaan tijdens het verzenden van het bericht.";
        $this->view('messages/form', $this->view_data);
      }
    }
  }
?>
