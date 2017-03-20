<?php
// the admin controllers are sections of the website that should only be accessible by the administrator
class matches_admin extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->db_con = $this->db_con();

		$this->user = $this->model('User');
		$this->team = $this->model('Team');
		$this->teams_leagues = $this->model('Teams_Leagues');
		$this->match = $this->model('Match');
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
			$this->view_data['league'] = $this->league->get_league_by_id($this->db_con, "1");
			$this->view_data['matches'] = $this->match->get_matches_all($this->db_con);
			$this->view('matches/multiple', $this->view_data);
		}
	}

	public function create($league_id='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($this->league->get_league_by_id($this->db_con, $league_id) != true)
		{
			$this->view_data['notice'] = "Competitie niet gevonden.";
			$this->view('home/index', $this->view_data);
		}
		elseif(isset($_POST['create']))
		{
			$this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id_ordered_by_team_tag_asc($this->db_con, $league_id);
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);

			if(empty($_POST['home_team_id']))
			{
				$this->view_data['notice'] = "Vul een thuisploeg in.";
				$this->view('matches/forms/create', $this->view_data);
			}
			elseif(empty($_POST['away_team_id']))
			{
				$this->view_data['notice'] = "Vul een uitploeg in.";
				$this->view('matches/forms/create', $this->view_data);
			}
			elseif($_POST['home_team_id'] == $_POST['away_team_id'])
			{
				$this->view_data['notice'] = "De thuisploeg blijkt dezelfde ploeg te zijn als de uitploeg.";
				$this->view('matches/forms/create', $this->view_data);
			}
			elseif(empty($_POST['datetime']))
			{
				$this->view_data['notice'] = "Vul een datum in.";
				$this->view('matches/forms/create', $this->view_data);
			}
			elseif(empty($_POST['league']))
			{
				$this->view_data['notice'] = "Vul een competitie in.";
				$this->view('matches/forms/create', $this->view_data);
			}
			elseif($this->match->create_match($this->db_con, $_POST['datetime'], $_POST['info'], $_POST['league'], $_POST['matchday'], $_POST['home_team_id'], $_POST['away_team_id']))
			{
				$this->view_data['notice'] = "Wedstrijd aangemaakt.";
				$this->view('matches/forms/create', $this->view_data);
			}
			else
			{
				$this->view_data['notice'] = "Er is iets fout gegaan tijdens het aanmaken van de wedstrijd.";
				$this->view('matches/forms/create', $this->view_data);
			}
		}
		else
		{
			$this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id_ordered_by_team_tag_asc($this->db_con, $league_id);
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
			$this->view('matches/forms/create', $this->view_data);
		}
	}

	public function create_multiple($league_id='', $matchday='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			if(empty($matchday))
			{
				if($league_last_match = $this->match->get_last_match_by_league_id($this->db_con, $league_id))
				{
					$matchday = $league_last_match['league_matchday'] + 1;
				}
			}

			if($matchday < $league['league_matchday_current'])
			{
				$this->view_data['notice'] = "Geen geldige speeldag gevonden, u kan geen wedstrijden toevoegen voor vorige speeldagen.";
				$this->view('home/index', $this->view_data);
			}
			elseif($matchday > $league['league_matchday_total'])
			{
				$this->view_data['notice'] = "Geen geldige speeldag gevonden, u kan geen wedstrijden toevoegen na de laatste speeldag.";
				$this->view('home/index', $this->view_data);
			}
			else
			{
				$this->view_data['league'] = $league;
				$this->view_data['matchday'] = $matchday;
				$this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id_ordered_by_team_tag_asc($this->db_con, $league_id);

				if(!isset($_POST['create_multiple']))
				{
					$this->view('matches/forms/create_multiple', $this->view_data);
				}
				elseif(empty($_POST['home_team_id']))
				{
					$this->view_data['notice'] = "Geen thuisploegen gevonden.";
					$this->view('matches/forms/create_multiple', $this->view_data);
				}
				elseif(empty($_POST['away_team_id']))
				{
					$this->view_data['notice'] = "Geen uitploegen gevonden.";
					$this->view('matches/forms/create_multiple', $this->view_data);
				}
				elseif(empty($_POST['datetime']))
				{
					$this->view_data['notice'] = "Geen datums gevonden.";
					$this->view('matches/forms/create_multiple', $this->view_data);
				}
				else
				{
					$matches_hometeams = $_POST['home_team_id'];
					$matches_awayteams = $_POST['away_team_id'];
					$matches_datetimes = $_POST['datetime'];

					foreach($matches_hometeams as $key => $home_team_id)
					{
						if($this->team->get_team_by_id($this->db_con, $home_team_id) && $this->team->get_team_by_id($this->db_con, $matches_awayteams[$key]))
						{
							if($this->match->create_match($this->db_con, $matches_datetimes[$key], '', $league_id, $matchday, $home_team_id, $matches_awayteams[$key]))
							{
								$this->view_data['notice'] = "Wedstrijden toegevoegd. Gebruikers kunnen pronostieken indienen nadat u het bewerkingsformulier eenmalig indient.";
								$matches_added = true;
							}
							else
							{
								$this->view_data['notice'] = "Er is iets fout gegaan tijdens het toevoegen van de wedstrijden.";
								$matches_added = false;
							}
						}
						else
						{
							$this->view_data['notice'] = "Er is iets fout gegaan tijdens het toevoegen van de wedstrijden.";
							$matches_added = false;
						}
					}
					if($matches_added == true)
					{
						$this->view_data['matches'] = $this->match->get_matches_by_league_id_and_matchday($this->db_con, $league_id, $matchday);
						$this->view('matches/forms/edit_multiple', $this->view_data);
					}
					else
					{
						$this->view('home/index', $this->view_data);
					}
				}
			}
		}
		else
		{
			$this->view_data['notice'] = "Geen competitie gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function edit($match_id='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($match = $this->match->get_match_by_id($this->db_con, $match_id))
		{
			$this->view_data['match'] = $match;
			$this->view_data['teams'] = $this->team->get_teams_all_ordered_by_team_tag_asc($this->db_con);
			$this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);

			if(!isset($_POST['edit']))
			{
				$this->view('matches/forms/edit', $this->view_data);
			}
			elseif(empty($_POST['home_team_id']))
			{
				$this->view_data['notice'] = "Vul een thuisploeg in.";
				$this->view('matches/forms/edit', $this->view_data);
			}
			elseif(empty($_POST['away_team_id']))
			{
				$this->view_data['notice'] = "Vul een uitploeg in.";
				$this->view('matches/forms/edit', $this->view_data);
			}
			elseif(empty($_POST['datetime']))
			{
				$this->view_data['notice'] = "Vul een datum in.";
				$this->view('matches/forms/edit', $this->view_data);
			}
			elseif($this->match->edit_match($this->db_con, $match_id, $_POST['status'], $_POST['datetime'], $_POST['info'], $_POST['league'], $_POST['matchday'], $_POST['home_team_id'], $_POST['home_team_score'], $_POST['away_team_id'], $_POST['away_team_score']))
			{
				$this->view_data['match'] = $this->match->get_match_by_id($this->db_con, $match_id);
				$this->view_data['notice'] = "Wedstrijd bijgewerkt.";
				$this->view('matches/forms/edit', $this->view_data);
			}
			else
			{
				$data['teams'] = $this->team->get_teams_all_ordered_by_team_tag_asc($this->db_con);
				$this->view_data['notice'] = "Er is iets fout gegaan tijdens het bijwerken van de wedstrijd.";
				$this->view('matches/forms/edit', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Geen wedstrijd gevonden.";
			$this->view('home/index', $this->view_data);
		}
	}

	public function edit_multiple($league_id=1, $league_matchday='')
	{
		if($this->user_loggedin['user_rank'] != 9)
		{
			$this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
			$this->view('home/index', $this->view_data);
		}
		elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
		{
			$this->view_data['league'] = $league;

			if(empty($league_matchday))
			{
				$league_matchday = $league['league_matchday_current'];
			}

			if($matches = $this->match->get_matches_by_league_id_and_matchday($this->db_con, $league_id, $league_matchday))
			{
				$this->view_data['matches'] = $matches;
				if(isset($_POST['edit_multiple']))
				{
					if(empty($_POST['match_id']))
					{
						$this->view_data['notice'] = "We konden de wedstrijden niet vinden.";
					}
					elseif(empty($_POST['home_team_score']))
					{
						$this->view_data['notice'] = "We konden de thuisscores niet vinden.";
					}
					elseif(empty($_POST['away_team_score']))
					{
						$this->view_data['notice'] = "We konden de uitscores niet vinden.";
					}
					else
					{
						$matches_ids = $_POST['match_id'];
						$home_team_scores = $_POST['home_team_score'];
						$away_team_scores = $_POST['away_team_score'];

						if(empty($_POST['match_lock']))
						{
							$matches_locks = false;
						}
						else
						{
							$matches_locks = $_POST['match_lock'];
						}

						if(empty($_POST['match_score']))
						{
							$matches_scores = false;
						}
						else
						{
							$matches_scores = $_POST['match_score'];
						}

						foreach($matches_ids as $key => $match_id)
						{
							if($this->validate_input("match_score", $home_team_scores[$key]) != true)
							{
								$home_team_scores[$key] = "0";
							}

							if($this->validate_input("match_score", $away_team_scores[$key]) != true)
							{
								$away_team_scores[$key] = "0";
							}

							if($match = $this->match->get_match_by_id($this->db_con, $match_id))
							{
								$match_status = $match['match_status'];

								if(empty($matches_locks[$key]) && empty($matches_scores[$key]))
								{
									$match_status = 6;
								}

								if(isset($matches_locks[$key]))
								{
									$match_status = 5;
								}

								if(isset($matches_scores[$key]))
								{
									$match_status = 4;
								}

								if($this->match->set_match_score_and_status($this->db_con, $match_id, $home_team_scores[$key], $away_team_scores[$key], $match_status))
								{
									$this->view_data['notice'] = "Wedstrijden bewerkt.";
								}
								else
								{
									$this->view_data['notice'] = "Er is iets fout gegaan tijdens het bewerken van de wedstrijden.";
								}
							}
							else
							{
								$this->view_data['notice'] = "Wedstrijd niet gevonden.";
							}
						}
					}
					$this->view_data['matches'] = $this->match->get_matches_by_league_id_and_matchday($this->db_con, $league_id, $league_matchday);
					$this->view('matches/forms/edit_multiple', $this->view_data);
				}
				else
				{
					$this->view('matches/forms/edit_multiple', $this->view_data);
				}
			}
			else
			{
				$this->view_data['notice'] = "Wedstrijden niet gevonden";
				$this->view_data['matchday'] = $league_matchday;
				$this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id_ordered_by_team_tag_asc($this->db_con, $league_id);
				$this->view('matches/forms/create_multiple', $this->view_data);
			}
		}
		else
		{
			$this->view_data['notice'] = "Competitie niet gevonden";
			$this->view('home/index', $this->view_data);
		}
	}
}
