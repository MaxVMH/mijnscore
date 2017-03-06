<?php
class teams_admin extends Controller
{
  protected $user;

  public function __construct()
  {
		$this->db_con = $this->db_con();
		
    $this->user = $this->model('User');
    $this->league = $this->model('League');
    $this->team = $this->model('Team');
    $this->teams_leagues = $this->model('Teams_Leagues');

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
      $this->view_data['teams'] = $this->team->get_teams($this->db_con);
      $this->view('teams/multiple', $this->view_data);
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
        $this->view_data['notice'] = "Vul de naam van de ploeg in.";
        $this->view('teams/forms/create', $this->view_data);
      }
      elseif(empty($_POST['tag']))
      {
        $this->view_data['notice'] = "Vul een tag voor de ploeg in.";
        $this->view('teams/forms/create', $this->view_data);
      }
      elseif(empty($_POST['league_id']))
      {
        $this->view_data['notice'] = "Kies een competitie waarin de ploeg zal spelen.";
        $this->view('teams/forms/edit', $this->view_data);
      }
      elseif($team_id = $this->team->create_team($this->db_con, $_POST['name'], $_POST['tag'], $_POST['league_id']))
      {
        foreach($_POST['league_id'] as $league_id)
        {
          $this->teams_leagues->set_team_league($this->db_con, $team_id, $league_id);
        }
        $this->view_data['notice'] = "Nieuwe ploeg gemaakt.";
        $this->view_data['team'] = $this->team->get_team_by_id($this->db_con, $team_id);
        $this->view_data['leagues'] = $this->teams_leagues->get_leagues_by_team_id($this->db_con, $team_id);
        $this->view('teams/forms/edit', $this->view_data);
      }
      else
      {
        $this->view_data['notice'] = "Er is iets fout gegaan tijdens het maken van de ploeg.";
        $this->view('teams/forms/create', $this->view_data);
      }
    }
    else
    {
      $this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
      $this->view('teams/forms/create', $this->view_data);
    }
  }

  public function edit($team_id='')
  {
    if($this->user_loggedin['user_rank'] != 9)
    {
      $this->view_data['notice'] = "U heeft geen toegang tot deze pagina.";
      $this->view('home/index', $this->view_data);
    }
    elseif(isset($_POST['edit']) && $team = $this->team->get_team_by_id($this->db_con, $team_id))
    {
      $this->view_data['team'] = $team;
      $this->view_data['leagues'] = $this->teams_leagues->get_leagues_by_team_id($this->db_con, $team_id);

      if(empty($_POST['name']))
      {
        $this->view_data['notice'] = "Vul de naam van de ploeg in.";
        $this->view('teams/forms/edit', $this->view_data);
      }
      elseif(empty($_POST['tag']))
      {
        $this->view_data['notice'] = "Vul een tag voor de ploeg in.";
        $this->view('teams/forms/edit', $this->view_data);
      }
      elseif(empty($_POST['league_id']))
      {
        $this->view_data['notice'] = "Kies een competitie waarin de ploeg zal spelen.";
        $this->view('teams/forms/edit', $this->view_data);
      }
      elseif($this->team->edit_team($this->db_con, $team_id, $_POST['name'], $_POST['tag']))
      {
        foreach($_POST['league_id'] as $league_id)
        {
          $this->teams_leagues->set_team_league($this->db_con, $team_id, $league_id);
        }
        $this->view_data['notice'] = "Ploeg bijgewerkt.";
        $this->view_data['team'] = $this->team->get_team_by_id($this->db_con, $team_id);
        $this->view_data['leagues'] = $this->teams_leagues->get_leagues_by_team_id($this->db_con, $team_id);
        $this->view('teams/forms/edit', $this->view_data);
      }
      else
      {
        $this->view_data['notice'] = "Er is iets fout gegaan tijdens het bijwerken van de ploeg.";
        $this->view_data['team'] = $this->team->get_team_by_id($this->db_con, $team_id);
        $this->view('teams/forms/edit', $this->view_data);
      }
    }
    elseif($team = $this->team->get_team_by_id($this->db_con, $team_id))
    {
      $this->view_data['team'] = $team;
      $this->view_data['leagues'] = $this->teams_leagues->get_leagues_by_team_id($this->db_con, $team_id);
      $this->view('teams/forms/edit', $this->view_data);
    }
    else
    {
      $this->view_data['notice'] = "Geen ploeg gevonden.";
      $this->view('home/index', $this->view_data);
    }
  }
}
