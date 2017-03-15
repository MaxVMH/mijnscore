<?php
// the admin controllers are sections of the website that should only be accessible by the administrator
class leagues_admin extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->db_con = $this->db_con();

		$this->user = $this->model('User');
		$this->league = $this->model('League');

		$this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
		$this->view_data = [];
		$this->view_data['user_loggedin'] = $this->user_loggedin;
		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);
	}

	public function index()
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$this->view('home/index', $this->view_data);
		}
	}

	public function edit($league_id='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
			$this->view_data['league'] = $league;

			if(isset($_POST['edit']))
			{
				if(empty($_POST['name']))
				{
					$this->view_data['notice'] = "Vul een competitie naam in.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
				elseif(empty($_POST['tag']))
				{
					$this->view_data['notice'] = "Vul een competitie tag in.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
				elseif(empty($_POST['playday_total']))
				{
					$this->view_data['notice'] = "Vul het totale aantal speeldagen in.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
				elseif(empty($_POST['league_status']))
				{
					$this->view_data['notice'] = "Geen status gevonden.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
				elseif(empty($_POST['parent_id']))
				{
					$this->view_data['notice'] = "Vul een competitie verband in.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
				elseif($this->league->edit_league($this->db_con, $league_id, $_POST['parent_id'], $_POST['name'], $_POST['tag'], $_POST['playday_current'], $_POST['playday_total'], $_POST['league_status']))
				{
					$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, $league_id);
					$this->view_data['notice'] = "Competitie bijgewerkt.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
				else
				{
					$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, $league_id);
					$this->view_data['notice'] = "Er is iets fout gegaan tijdens het bijwerken van de competitie.";
					$this->view('leagues/forms/edit', $this->view_data);
				}
			}
			else
			{
				$this->view('leagues/forms/edit', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function create()
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif(isset($_POST['create']))
		{
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
			if(empty($_POST['name']))
			{
				$this->view_data['notice'] = "Vul een competitie naam in.";
				$this->view('leagues/forms/create', $this->view_data);
			}
			elseif(empty($_POST['tag']))
			{
				$this->view_data['notice'] = "Vul een competitie tag in.";
				$this->view('leagues/forms/create', $this->view_data);
			}
			elseif(empty($_POST['playday_total']))
			{
				$this->view_data['notice'] = "Vul het totaal aantal speeldagen in.";
				$this->view('leagues/forms/create', $this->view_data);
			}
			elseif(empty($_POST['parent_id']))
			{
				$this->view_data['notice'] = "Vul een competitie verband in.";
				$this->view('leagues/forms/create', $this->view_data);
			}
			elseif($this->league->create_league($this->db_con, $_POST['parent_id'], $_POST['name'], $_POST['tag'], $_POST['playday_total']))
			{
				$this->view_data['notice'] = "Nieuwe competitie gemaakt.";
				$this->view('home/index', $this->view_data);
			}
			else
			{
				$this->view_data['notice'] = "Er is iets fout gegaan tijdens het maken van de nieuwe competitie.";
				$this->view('home/index', $this->view_data);
			}
		}
		else
		{
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
			$this->view('leagues/forms/create', $this->view_data);
		}
	}

	public function delete($league_id='', $confirm='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif(!empty($league_id))
		{
			if($league = $this->league->get_league_by_id($this->db_con, $league_id))
			{
				if(empty($confirm))
				{
					$this->view_data['league'] = $league;
					$this->view_data['notice'] = "Competitie om te verwijderen gevonden.";
					$this->view('leagues/forms/delete', $this->view_data);
				}
				else
				{
					if($this->league->delete_league_by_id($this->db_con, $league_id))
					{
						$this->view_data['notice'] = "Competitie verwijderd.";
						$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
						$this->view('admin/index', $this->view_data);
					}
					else
					{
						$this->view_data['notice'] = "Er is iets fout gegaan.";
						$this->view('home/index', $this->view_data);
					}
				}
			}
			else
			{
				$this->view_data['notice'] = "Geen competitie gevonden.";
				$this->view('home/index', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Geen competitie gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}
}
