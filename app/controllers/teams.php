<?php
class teams extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->user = $this->model('User');
		$this->league = $this->model('League');
		$this->team = $this->model('Team');
		$this->teams_leagues = $this->model('Teams_Leagues');
		$this->team_points = $this->model('Team_Points');
		$this->match = $this->model('Match');
		$this->prediction= $this->model('Prediction');

		$this->db_con = $this->db_con();

		$this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
		$this->view_data = [];
		$this->view_data['user_loggedin'] = $this->user_loggedin;
		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);
	}

	public function index($league_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif(!empty($league_id))
		{
			$this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id($this->db_con, $league_id);
			$this->view('teams/multiple', $this->view_data);
		}
		else
		{
			$this->view_data['teams'] = $this->team->get_teams($this->db_con);
			$this->view('teams/multiple', $this->view_data);
		}
	}

	public function single($team_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($team = $this->team->get_team_by_id($this->db_con, $team_id))
		{
			$team_leagues = $this->teams_leagues->get_leagues_by_team_id($this->db_con, $team_id);
			foreach($team_leagues as $key => $team_league)
			{

				$team_leagues[$key]['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_team_id_and_user_id($this->db_con, $team_league['league_id'], $team_id, $this->user_loggedin['user_id']);
			}
			$this->view_data['leagues'] = $team_leagues;
			$this->view_data['team'] = $team;
			$this->view('teams/single', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Ploeg niet gevonden.";
			$this->view_data['teams'] = $this->team->get_teams($this->db_con);
			$this->view('teams/multiple', $this->view_data);
		}
	}

	public function multiple($league_id='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->view_data['league'] = $league;
			$this->view_data['teams'] = $this->team->get_teams_by_league_id($this->db_con, $league_id);
			$this->view('teams/multiple', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function matches($team_id=1, $league_id=1)
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($team = $this->team->get_team_by_id($this->db_con, $team_id))
		{
			if($league = $this->league->get_league_by_id($this->db_con, $league_id))
			{
				$matches = $this->prediction->get_all_matches_with_predictions_by_league_id_and_team_id_and_user_id($this->db_con, $league_id, $team_id, $this->user_loggedin['user_id']);
				$this->view_data['league'] = $league;
				$this->view_data['matches'] = $matches;
				$this->view_data['team'] = $team;
				$this->view('teams/matches', $this->view_data);
			}
			else
			{
				$this->view_data['notice'] = "Competitie niet gevonden.";
				$this->view_data['teams'] = $this->team->get_teams($this->db_con);
				$this->view('teams/multiple', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Ploeg niet gevonden.";
			$this->view_data['teams'] = $this->team->get_teams($this->db_con);
			$this->view('teams/multiple', $this->view_data);
		}
	}

	public function score($league_id=1)
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->view_data['league'] = $league;
			$this->view_data['teams'] = $this->team_points->get_teams_and_points_by_league_id($this->db_con, $league_id);
			$this->view('teams/score', $this->view_data);
		}
	}
}
