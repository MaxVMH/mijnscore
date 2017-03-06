<?php
// this hardly needs any explanation.
// TODO? merg register controller with home cotnroller?
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
		elseif($this->user->create_user($this->db_con, $_POST['username'], $_POST['password'], $_POST['email'], $_SERVER['REMOTE_ADDR']))
		{
			$this->view_data['notice'] = "Uw nieuwe account is aangemaakt. U kan onmiddellijk inloggen. U zal geen e-mail ontvangen.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Er is iets fout gegaan tijdens het maken van uw nieuwe account.";
			$this->view('register/form', $this->view_data);
		}
	}

}
?>
