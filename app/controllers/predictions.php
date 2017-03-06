<?php
// this is the controller everyone uses to input and edit predictions (duh)
class predictions extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->db_con = $this->db_con();

		$this->user = $this->model('User');
		$this->match = $this->model('Match');
		$this->prediction = $this->model('Prediction');
		$this->prediction_points = $this->model('Prediction_Points');
		$this->league = $this->model('League');

		$this->user_loggedin = $this->user->get_user_loggedin($this->db_con);
		$this->view_data = [];
		$this->view_data['user_loggedin'] = $this->user_loggedin;
		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);

		$this->match->set_matches_status_auto($this->db_con);
		$this->league->set_leagues_playday_auto($this->db_con);
		$this->league->set_leagues_status_auto($this->db_con);
	}

	public function index($league_id='', $playday='', $user_id='')
	{
		if(empty($league_id))
		{
			$league_id = 1;
		}

		if($this->user_loggedin == false)
		{

			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->view_data['league'] = $league;


			if(empty($playday))
			{
				$playday = $league['league_playday_current'];
			}

			if($playday == "0")
			{
				$playday = $league['league_playday_current'] + 1;
			}

			if($user = $this->user->get_user_by_id($this->db_con, $user_id))
			{
				$this->view_data['user'] = $user;

				if($predictions = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $user['user_id'], $playday))
				{
					$this->view_data['predictions'] = $predictions;
					$this->view('predictions/view', $this->view_data);
				}
				else
				{
					$this->view_data['notice'] = "Geen wedstrijden gevonden.";
					$this->view('home/index', $this->view_data);
				}
			}
			elseif($playday < $league['league_playday_current'] || $league['league_status'] < 1)
			{
				if($predictions = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday))
				{
					$this->view_data['predictions'] = $predictions;
					$this->view('predictions/view', $this->view_data);
				}
				else
				{
					$this->view_data['notice'] = "Geen wedstrijden gevonden.";
					$this->view('home/index', $this->view_data);
				}
			}
			elseif($playday >= $league['league_playday_current'])
			{
				if($predictions = $this->prediction->get_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday))
				{
					$predictions_editable = FALSE;
					foreach($predictions as $prediction)
					{
						if($prediction['match_status'] >= 6)
						{
							$predictions_editable = TRUE;
						}
					}

					if($predictions_editable == TRUE)
					{
						$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/forms/edit', $this->view_data);
					}
					else
					{
						$this->view_data['predictions'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/view', $this->view_data);
					}

				}
				elseif($matches = $this->match->get_matches_by_league_id_and_playday($this->db_con, $league_id, $playday))
				{
					$predictions_creatable = FALSE;
					foreach($matches as $match)
					{
						if($match['match_status'] >= 6)
						{
							$predictions_creatable = TRUE;
						}
					}

					if($predictions_creatable == TRUE)
					{
						$this->view_data['matches'] = $matches;
						$this->view('predictions/forms/create', $this->view_data);
					}
					else
					{
						$this->view_data['predictions'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/view', $this->view_data);
					}
				}
				else
				{
					$this->view_data['notice'] = "Geen pronostieken of wedstrijden gevonden.";
					$this->view('home/index', $this->view_data);
				}
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

	public function score($league_id=1, $playday=0)
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->view_data['users'] = $this->prediction_points->get_users_and_points_by_league_id_and_playday($this->db_con, $league_id, $playday);
			$this->view_data['score_league'] = $league;
			$this->view_data['score_playday'] = $playday;
			$this->view('predictions/score', $this->view_data);
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function edit($league_id='', $playday='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($this->league->get_league_by_id($this->db_con, $league_id) == false)
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
		elseif($playday < $this->league->get_league_by_id($this->db_con, $league_id)['league_playday_current'])
		{
			$this->view_data['notice'] = "Pronostiek niet bewerkbaar.";
			$this->view('home/index', $this->view_data);
		}
		elseif($predictions = $this->prediction->get_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday))
		{
			$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, $league_id);
			$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);

			if(isset($_POST['edit']))
			{
				if(empty($_POST['prediction_id']))
				{
					$this->view_data['notice'] = "Pronostiek niet gevonden.";
					$this->view('predictions/forms/edit', $this->view_data);
				}
				elseif(empty($_POST['prediction_home_team_score']))
				{
					$this->view_data['notice'] = "Uitslagen van de thuisploegen niet gevonden.";
					$this->view('predictions/forms/edit', $this->view_data);
				}
				elseif(empty($_POST['prediction_away_team_score']))
				{
					$this->view_data['notice'] = "Uitslagen van de uitploegen niet gevonden.";
					$this->view('predictions/forms/edit', $this->view_data);
				}
				else
				{
					$predictions_ids = $_POST['prediction_id'];
					$predictions_home_team_scores = $_POST['prediction_home_team_score'];
					$predictions_away_team_scores = $_POST['prediction_away_team_score'];

					foreach($predictions_ids as $key => $prediction_id)
					{
						if($this->validate_input("match_score", $predictions_home_team_scores[$key]) != true)
						{
							$predictions_home_team_scores[$key] = "0";
						}
						if($this->validate_input("match_score", $predictions_away_team_scores[$key]) != true)
						{
							$predictions_away_team_scores[$key] = "0";
						}
						if($this->prediction->get_prediction_by_id($this->db_con, $prediction_id)['match_status'] != 6)
						{
							$predictions_toolate = true;
						}
					}

					if(!empty($predictions_toolate))
					{
						$this->view_data['notice'] = "Minstens 1 pronostiek werd te laat ontvangen.";
						$this->view('predictions/forms/edit', $this->view_data);
					}
					elseif($this->prediction->edit_predictions_by_user_id_and_prediction_ids($this->db_con, $this->user_loggedin['user_id'], $predictions_ids, $predictions_home_team_scores, $predictions_away_team_scores))
					{
						$this->view_data['notice'] = "Pronostiek bijgewerkt.";
						$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/forms/edit', $this->view_data);
					}
					else
					{
						$this->view_data['notice'] = "Er is iets fout gelopen tijdens het bijwerken van de pronostiek.";
						$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/forms/edit', $this->view_data);
					}
				}
			}
			else
			{
				$this->view('predictions/forms/edit', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Pronostiek niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function create($league_id='', $playday='')
	{
		if($this->user_loggedin == false)
		{
			$this->view_data['notice'] = "U bent niet ingelogd.";
			$this->view('home/index', $this->view_data);
		}
		elseif($this->league->get_league_by_id($this->db_con, $league_id) == false)
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
		elseif($predictions = $this->prediction->get_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday))
		{
			$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, $league_id);
			$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
			$this->view('predictions/forms/edit', $this->view_data);
		}
		elseif($matches = $this->match->get_matches_by_league_id_and_playday($this->db_con, $league_id, $playday))
		{
			$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, $league_id);

			if(isset($_POST['create']))
			{
				if(empty($_POST['prediction_match_id']))
				{
					$this->view_data['notice'] = "We konden uw pronostiek niet vinden.";
					$this->view('home/index', $this->view_data);
				}
				elseif(empty($_POST['prediction_home_team_score']))
				{
					$this->view_data['notice'] = "We konden de scores van de thuisploegen niet vinden.";
					$this->view('home/index', $this->view_data);
				}
				elseif(empty($_POST['prediction_away_team_score']))
				{
					$this->view_data['notice'] = "We konden de scores van de uitploegen niet vinden.";
					$this->view('home/index', $this->view_data);
				}
				else
				{
					$predictions_match_ids = $_POST['prediction_match_id'];
					$predictions_home_team_scores = $_POST['prediction_home_team_score'];
					$predictions_away_team_scores = $_POST['prediction_away_team_score'];

					foreach($predictions_match_ids as $key => $prediction_match_id)
					{
						if($this->validate_input("match_score", $predictions_home_team_scores[$key]) != true)
						{
							$predictions_home_team_scores[$key] = "0";
						}
						if($this->validate_input("match_score", $predictions_away_team_scores[$key]) != true)
						{
							$predictions_away_team_scores[$key] = "0";
						}
						if($this->match->get_match_by_id($this->db_con, $prediction_match_id)['match_status'] != 6)
						{
							$predictions_toolate = true;
						}
					}

					if(!empty($predictions_toolate))
					{
						$this->view_data['notice'] = "Minstens 1 pronostiek werd te laat ontvangen.";
						$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/view', $this->view_data);
					}
					elseif($this->prediction->create_predictions($this->db_con, $predictions_match_ids, $this->user_loggedin['user_id'], $predictions_home_team_scores, $predictions_away_team_scores))
					{
						$this->view_data['notice'] = "Nieuwe pronostiek ontvangen.";
						$this->view_data['matches'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/forms/edit', $this->view_data);
					}
					else
					{
						$this->view_data['notice'] = "Er is iets fout gegaan tijdens het maken van de pronostiek.";
						$this->view_data['predictions'] = $this->prediction->get_matches_with_predictions_by_league_id_and_user_id_and_playday($this->db_con, $league_id, $this->user_loggedin['user_id'], $playday);
						$this->view('predictions/view', $this->view_data);
					}
				}
			}
			else
			{
				$this->view_data['matches'] = $matches;
				$this->view('predictions/forms/create', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Geen wedstrijden gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}
}
