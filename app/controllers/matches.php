<?php
class matches extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->user = $this->model('User');
		$this->league = $this->model('League');
		$this->match = $this->model('Match');

		$this->db_con = $this->db_con();

		$this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
		$this->view_data = [];
		$this->view_data['user_loggedin'] = $this->user_loggedin;
		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);
	}

	public function index($league_id='1', $playday='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			if(empty($playday))
			{
				$playday = $league['league_playday_current'];
			}
			if($playday==0)
			{
				$playday++;
			}

			if($matches = $this->match->get_matches_by_league_id_and_playday($this->db_con, $league_id, $playday))
			{
				$this->view_data['league'] = $league;
				$this->view_data['matches'] = $matches;
				$this->view('matches/multiple', $this->view_data);
			}
			else
			{
				$this->view_data['notice'] = "Wedstrijden niet gevonden.";
				$this->view('home/index', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function single($match_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($match = $this->match->get_match_by_id($this->db_con, $match_id))
		{
			$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, $match['league_id']);
			$this->view_data['match'] = $match;
			$this->view('matches/single', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Wedstrijd niet gevonden.";
			$this->view_data['matches'] = $this->match->get_matches_by_status($this->db_con, "5");
			$this->view('matches/multiple', $this->view_data);
		}
	}
}
