<?php
// this hardly needs any explanation.
// TODO? merg messages_create with messages controller?
class messages extends Controller
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

	public function single($message_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($message = $this->message->get_message($this->db_con, $message_id, $this->user_loggedin['user_id']))
		{
			$this->view_data['message'] = $message;

			if($message['message_receiver_user_id'] == $this->user_loggedin['user_id'] && $message['message_receiver_status'] == 2)
			{
				$this->message->set_message_read($this->db_con, $message_id, $this->user_loggedin['user_id']);
			}

			$this->view('messages/single', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "We konden het bericht niet vinden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function inbox()
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view_data['messages_box'] = "Inkomend";
			$this->view_data['messages'] = $this->message->get_inbox($this->db_con, $this->user_loggedin['user_id']);
			$this->view('messages/multiple', $this->view_data);
		}
	}

	public function outbox()
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view_data['messages_box'] = "Uitgaand";
			$this->view_data['messages'] = $this->message->get_outbox($this->db_con, $this->user_loggedin['user_id']);
			$this->view('messages/multiple', $this->view_data);
		}
	}

	public function delete($message_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($this->message->set_message_deleted($this->db_con, $message_id, $this->user_loggedin['user_id']))
		{
			$this->view_data['notice'] = "Het bericht werd verwijderd.";
			$this->view_data['messages_box'] = "Inkomend";
			$this->view_data['messages'] = $this->message->get_inbox($this->db_con, $this->user_loggedin['user_id']);
			$this->view('messages/multiple', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "We konden het bericht niet vinden.";
			$this->view_data['messages_box'] = "Uitgaand";
			$this->view_data['messages'] = $this->message->get_inbox($this->db_con, $this->user_loggedin['user_id']);
			$this->view('messages/multiple', $this->view_data);
		}
	}
}
?>
