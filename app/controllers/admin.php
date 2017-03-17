<?php
// the admin controllers are sections of the website that should only be accessible by the administrator
// it can calculate rankings/points and has a menu (admin/index) with links to the other admin controllers (leagues_admin, teams_admin, matches_admin)
class admin extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->db_con = $this->db_con();

		$this->user = $this->model('User');
		$this->mail = $this->model('Mail');
		$this->league = $this->model('League');
		$this->prediction = $this->model('Prediction');
		$this->team_points = $this->model('Team_Points');
		$this->prediction_points = $this->model('Prediction_Points');

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
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
			$this->view('admin/index', $this->view_data);
		}
	}

	public function set_team_points($league_id=1)
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			if($this->team_points->set_team_points_by_league_id($this->db_con, $league['league_id']))
			{
				$this->view_data['notice'] = "Ploeg punten geüpdatet: " . $league['league_name'];
				$this->view_data['league'] = $league;
				$this->view_data['teams'] = $this->team_points->get_teams_and_points_by_league_id($this->db_con, $league_id);
				$this->view('teams/score', $this->view_data);
			}
			else
			{
				$this->view_data['notice'] = "Er is iets fout gegaan tijdens het tellen van de punten.";
				$this->view('home/index', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function set_prediction_points($league_id=1)
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->prediction_points->set_prediction_points_by_league_id($this->db_con, $league['league_id']);
			$this->view_data['notice'] = "Pronostiek punten geüpdatet.";
			$this->view_data['users'] = $this->prediction_points->get_users_and_points_by_league_id_and_playday($this->db_con, $league['league_id'], 0);
			$this->view_data['score_league'] = $league;
			$this->view_data['score_playday'] = 0;
			$this->view('predictions/score', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function send_notifications($league_id='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		else
		{
			$users_email_notifications = $this->user->get_users_email_notifications($this->db_con);
			foreach($users_email_notifications as $user_email_notification)
			{
				if($this->prediction->get_last_prediction_by_user_id_and_league_id($this->db_con, $user_email_notification['user_id'], $league_id))
				{
					$this->mail->send_notification_email($user_email_notification['user_email'], $user_email_notification['user_username']);
				}
			}
			$this->view_data['notice'] = "De herinneringen zijn verzonden.";
			$this->view('home/index', $this->view_data);
		}
	}
}
?>
