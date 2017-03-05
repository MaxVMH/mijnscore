<?php
class matches_admin extends Controller
{
  protected $user;

  public function __construct()
  {
    $this->user = $this->model('User');
    $this->team = $this->model('Team');
    $this->teams_leagues = $this->model('Teams_Leagues');
    $this->match = $this->model('Match');
    $this->league = $this->model('League');

    $this->db_con = $this->db_con();

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
      $this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id($this->db_con, $league_id);
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
      elseif($this->match->create_match($this->db_con, $_POST['datetime'], $_POST['info'], $_POST['league'], $_POST['playday'], $_POST['home_team_id'], $_POST['away_team_id']))
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
      $this->view_data['teams'] = $this->teams_leagues->get_teams_by_league_id($this->db_con, $league_id);
      $this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
      $this->view('matches/forms/create', $this->view_data);
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
      $this->view_data['teams'] = $this->team->get_teams_all($this->db_con);
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
      elseif($this->match->edit_match($this->db_con, $match_id, $_POST['status'], $_POST['datetime'], $_POST['info'], $_POST['league'], $_POST['playday'], $_POST['home_team_id'], $_POST['home_team_score'], $_POST['away_team_id'], $_POST['away_team_score']))
      {
        $this->view_data['match'] = $this->match->get_match_by_id($this->db_con, $match_id);
        $this->view_data['notice'] = "Wedstrijd bijgewerkt.";
        $this->view('matches/forms/edit', $this->view_data);
      }
      else
      {
        $data['teams'] = $this->team->get_teams_all($this->db_con);
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

  public function edit_multiple($league_id=1, $league_playday='')
  {
    if($this->user_loggedin['user_rank'] != 9)
    {
      $this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
      $this->view('home/index', $this->view_data);
    }
    elseif($league = $this->league->get_league_by_id($this->db_con, $league_id))
    {
      $this->view_data['league'] = $league;

      if(empty($league_playday))
      {
        $league_playday = $league['league_playday_current'];
      }

      if($matches = $this->match->get_matches_by_league_id_and_playday($this->db_con, $league_id, $league_playday))
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
                $this->view_data['notice'] = "Match niet gevonden.";
              }
            }
          }
          $this->view_data['matches'] = $this->match->get_matches_by_league_id_and_playday($this->db_con, $league_id, $league_playday);
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
        $this->view('home/index', $this->view_data);
      }
    }
    else
    {
      $this->view_data['notice'] = "Competitie niet gevonden";
      $this->view('home/index', $this->view_data);
    }
  }
}
