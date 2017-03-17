<?php
// this hardly needs any explanation.
// TODO? merg register controller with home controller?
class register extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->db_con = $this->db_con();

		$this->user = $this->model('User');

		$this->view_data = [];
	}

	public function form()
	{
		$this->view('register/form', ['notice' => ""]);
	}

	public function submit()
	{
		if(empty($_POST['username']))
		{
			$this->view_data['notice'] = "Vul een gebruikersnaam in.";
			$this->view('register/form', $this->view_data);
		}
		elseif(empty($_POST['password']))
		{
			$this->view_data['notice'] = "Vul een wachtwoord in.";
			$this->view('register/form', $this->view_data);
		}
		elseif(empty($_POST['password_repeat']))
		{
			$this->view_data['notice'] = "Herhaal uw wachtwoord.";
			$this->view('register/form', $this->view_data);
		}
		elseif(empty($_POST['email']))
		{
			$this->view_data['notice'] = "Vul uw e-mailadres in.";
			$this->view('register/form', $this->view_data);
		}
		elseif($this->validate_input("username", $_POST['username']) != true)
		{
			$this->view_data['notice'] = "Vul een geldige gebruikersnaam in (minstens 2 letters).";
			$this->view('register/form', $this->view_data);
		}
		elseif($this->validate_input("password", $_POST['password']) != true)
		{
			$this->view_data['notice'] = "Vul een geldig wachtwoord in (minstens 6 letters/cijfers).";
			$this->view('register/form', $this->view_data);
		}
		elseif($this->validate_input("email", $_POST['email']) != true)
		{
			$this->view_data['notice'] = "Vul een geldig e-mail adres in.";
			$this->view('register/form', $this->view_data);
		}
		elseif($this->user->get_user_by_username($this->db_con, $_POST['username']) != false)
		{
			$this->view_data['notice'] = "Deze gebruikersnaam is al in gebruik. Vul een nieuwe gebruikersnaam in.";
			$this->view('register/form', $this->view_data);
		}
		elseif($this->user->get_user_by_email($this->db_con, $_POST['email']) != false)
		{
			$this->view_data['notice'] = "Dit e-mail adres is al in gebruik.";
			$this->view('register/form', $this->view_data);
		}
		else
		{
			$last_registered_user = $this->user->get_last_user_by_user_registration_datetime($this->db_con);

			$last_registered_user_datetime = new DateTime($last_registered_user['user_registration_datetime']);
			$last_registered_user_expiration_datetime = new DateTime('-5 MINUTE');

			$registered_users_amount = $this->user->count_users($this->db_con);

			if($last_registered_user_datetime >= $last_registered_user_expiration_datetime)
			{
				$this->view_data['notice'] = "Ons registratiesysteem is tijdelijk afgesloten om grote hoeveelheden nieuwe gebruikers tegen te gaan. Probeer opnieuw binnen enkele minuten.";
				$this->view('register/form', $this->view_data);
			}
			elseif($registered_users_amount['users_amount'] >= 100)
			{
				$this->view_data['notice'] = "Ons registratiesysteem is afgesloten omdat we de kaap van 100 gebruikers hebben bereikt.";
				$this->view('register/form', $this->view_data);
			}
			elseif($this->user->create_user($this->db_con, $_POST['username'], $_POST['password'], $_POST['email'], $_SERVER['REMOTE_ADDR']))
			{
				$this->view_data['notice'] = "Uw nieuwe account is aangemaakt. U kan onmiddellijk inloggen.";
				$this->view('home/index', $this->view_data);
			}
			else
			{
				$this->view_data['notice'] = "Er is iets fout gegaan tijdens het maken van uw nieuwe account.";
				$this->view('register/form', $this->view_data);
			}
		}
	}
}
?>
