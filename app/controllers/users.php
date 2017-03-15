<?php
// TODO: needs a bit more info
class users extends Controller
{
	protected $user;
	protected $message;

	public function __construct()
	{
		$this->db_con = $this->db_con();

		$this->user = $this->model('User');
		$this->mail = $this->model('Mail');
		$this->league = $this->model('League');
		$this->prediction_points = $this->model('Prediction_Points');

		$this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
		$this->view_data = [];
		$this->view_data['user_loggedin'] = $this->user_loggedin;
		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);
	}

	public function profile($user_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($profile_user = $this->user->get_user_by_id($this->db_con, $user_id))
		{
			$this->view_data['profile'] = $profile_user;
			$this->view_data['prediction_points'] = $this->prediction_points->get_prediction_points_by_user_id($this->db_con, $user_id);
			$this->view('users/profile', $this->view_data);
		}
		elseif(!empty($_POST['username']) && $profile_user = $this->user->get_user_by_username($this->db_con, $_POST['username']))
		{
			$this->view_data['profile'] = $profile_user;
			$this->view('users/profile', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "We konden de gebruiker niet vinden.";
			$this->view('users/forms/search', $this->view_data);
		}
	}

	public function search()
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view('users/forms/search', $this->view_data);
		}
	}

	public function edit($variable='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($variable == "email")
		{
			if(isset($_POST['edit_email']))
			{
				if(empty($_POST['password']))
				{
					$this->view_data['notice'] = "Vul uw wachtwoord in.";
					$this->view('users/forms/email', $this->view_data);
				}
				elseif(empty($_POST['email']))
				{
					$this->view_data['notice'] = "Vul uw e-mailadres in.";
					$this->view('users/forms/email', $this->view_data);
				}
				elseif(password_verify($_POST['password'], $this->user_loggedin['user_password_hash']) == false)
				{
					$this->view_data['notice'] = "Uw wachtwoord is niet correct.";
					$this->view('users/forms/email', $this->view_data);
				}
				elseif($this->validate_input("email", $_POST['email']) != true)
				{
					$this->view_data['notice'] = "Vul een geldig e-mailadres in.";
					$this->view('users/forms/email', $this->view_data);
				}
				elseif($this->user->get_user_by_email($this->db_con, $_POST['email']) != false)
				{
					$this->view_data['notice'] = "Dit e-mail adres is al in gebruik.";
					$this->view('users/forms/email', $this->view_data);
				}
				elseif($this->user->set_user_email($this->db_con, $this->user_loggedin['user_id'], $_POST['email']))
				{
					$this->user->set_user_email_verification_hash($this->db_con, $this->user_loggedin['user_id'], 0);
					$this->user->set_user_email_verification($this->db_con, $this->user_loggedin['user_id'], 0);
					$this->view_data['notice'] = "Uw e-mailadres werd bijgewerkt. Vergeet niet uw nieuw e-mailadres the bevestigen.";
					$this->view_data['user_loggedin'] = $this->user->get_user_loggedin($this->db_con);
					$this->view('users/forms/email', $this->view_data);
				}
				else
				{
					$this->view_data['notice'] = "Er is iets fout gegaan tijdens het bewerken van uw e-mailadres.";
					$this->view_data['user_loggedin'] = $this->user->get_user_loggedin($this->db_con);
					$this->view('users/forms/email', $this->view_data);
				}
			}
			else
			{
				$this->view('users/forms/email', $this->view_data);
			}
		}
		elseif($variable == "password")
		{
			if(isset($_POST['edit_password']))
			{
				if(empty($_POST['password']))
				{
					$this->view_data['notice'] = "Vul uw huidig wachtwoord in.";
					$this->view('users/forms/password', $this->view_data);
				}
				elseif(empty($_POST['password_new']))
				{
					$this->view_data['notice'] = "Vul uw nieuw wachtwoord in.";
					$this->view('users/forms/password', $this->view_data);
				}
				elseif(empty($_POST['password_new_repeat']))
				{
					$this->view_data['notice'] = "Herhaal uw nieuw wachtwoord.";
					$this->view('users/forms/password', $this->view_data);
				}
				elseif($_POST['password_new'] != $_POST['password_new_repeat'])
				{
					$this->view_data['notice'] = "Uw nieuw wachtwoord werd foutief herhaald.";
					$this->view('users/forms/password', $this->view_data);
				}
				elseif($this->validate_input("password", $_POST['password_new']) == false)
				{
					$this->view_data['notice'] = "Vul een geldig wachtwoord in (minstens 6 letters / cijfers / karakters).";
					$this->view('users/forms/password', $this->view_data);
				}
				elseif(password_verify($_POST['password'], $this->user_loggedin['user_password_hash']) == false)
				{
					$this->view_data['notice'] = "Uw huidig wachtwoord is niet correct.";
					$this->view('users/forms/password', $this->view_data);
				}
				elseif($this->user->set_user_password($this->db_con, $this->user_loggedin['user_id'], $_POST['password_new']))
				{
					$this->view_data['notice'] = "Uw wachtwoord is bijgewerkt.";
					$this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
					$this->view('users/forms/password', $this->view_data);
				}
				else
				{
					$this->view_data['notice'] = "Er is iets fout gegaan tijdens het bewerken van uw wachtwoord.";
					$this->view('users/forms/password', $this->view_data);
				}
			}
			else
			{
				$this->view('users/forms/password', $this->view_data);
			}
		}
		else
		{
			$this->view('home/index', $this->view_data);
		}
	}

	public function email_notification($email_notification='0')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		if($email_notification == 0)
		{
			$this->user->set_user_email_notification($this->db_con, $this->user_loggedin['user_id'], $email_notification);
			$this->view_data['notice'] = "E-mail herinneringen worden uitgeschakeld.";
			$this->view('home/index', $this->view_data);
		}
		elseif($email_notification == 1)
		{
			$this->user->set_user_email_notification($this->db_con, $this->user_loggedin['user_id'], $email_notification);
			$this->view_data['notice'] = "E-mail herinneringen worden ingeschakeld.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view('home/index', $this->view_data);
		}
	}

	public function email_verification($email_hash='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif(empty($email_hash) && empty($this->user_loggedin['user_email_verification_hash']))
		{
			$new_email_hash = bin2hex(random_bytes(32));

			$email_verification_email = "<h3>E-mail adres bevestigen op mijnscore.be</h3>
			<p>
			Dag " . $this->user_loggedin['user_username'] . ", <br />
			<br />
			Gelieve op <a href='http://mijnscore.be/users/email_verification/" .$new_email_hash . "'>deze link</a> te klikken om uw e-mail adres te bevestigen. Indien u deze e-mail niet gevraagd heeft, mag u deze e-mail negeren.<br />
			<br />
			Met vriendelijke groeten, <br />
			" . WEBSITE_TITLE . "
			</p>";

			$this->user->set_user_email_verification_hash($this->db_con, $this->user_loggedin['user_id'], $new_email_hash);
			$this->mail->send_mail($this->user_loggedin['user_email'], "Bevestig uw e-mailadres", $email_verification_email);

			$this->view_data['notice'] = "We hebben u een bevestigings e-mail gestuurd. Volg de link in de e-mail om uw e-mail adres te bevestigen.";
			$this->view('home/index', $this->view_data);
		}
		elseif($email_hash == $this->user_loggedin['user_email_verification_hash'])
		{
			$this->user->set_user_email_verification($this->db_con, $this->user_loggedin['user_id'], 1);

			$this->view_data['notice'] = "Uw e-mail adres werd bevestigd.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Uw bevestigings e-mail werd in het verleden al eens verstuurd.";
			$this->view('home/index', $this->view_data);
		}
	}
}
?>
