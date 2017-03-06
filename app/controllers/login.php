<?php
class login extends Controller
{
  protected $user;

  public function __construct()
  {
	  $this->db_con = $this->db_con();
		
    $this->user = $this->model('User');
    $this->league = $this->model('League');

    $this->view_data = [];
		$this->view_data['leagues_current'] = $this->league->get_leagues_by_status($this->db_con, 1);
  }

  public function form()
  {
    $this->view('login/form', $this->view_data);
  }

  public function submit()
  {
    if(empty($_POST['username']))
    {
      $this->view_data['notice'] = "Vul een gebruikersnaam in.";
      $this->view('login/form', $this->view_data);
    }
    elseif(empty($_POST['password']))
    {
      $this->view_data['notice'] = "Vul een wachtwoord in.";
      $this->view('login/form', $this->view_data);
    }
    elseif($user = $this->user->get_user_by_username($this->db_con, $_POST['username']))
    {
      $blocked_since_seconds = time() - strtotime($user['user_lastfail_datetime']);
      $blocked_seconds = 30;
      if($blocked_since_seconds < ($blocked_seconds * $user['user_lastfail_amount']) && $user['user_lastfail_amount'] > 2)
      {
        $blocked_left_seconds = ($blocked_seconds * $user['user_lastfail_amount']) - $blocked_since_seconds;
        $this->view_data['notice'] = "Uw account werd tijdelijk geblokkeerd omdat er " .$user['user_lastfail_amount'] . " foute wachtwoorden werden geprobeerd. Laatste poging was " . $blocked_since_seconds . " seconden geleden. Over " . $blocked_left_seconds . " seconden wordt je account weer vrijgegeven.";
        $this->view('login/form', $this->view_data);
      }
      elseif(password_verify($_POST['password'], $user['user_password_hash']))
      {
        if($this->user->set_user_loggedin($this->db_con, $user['user_id']))
        {
          $this->view_data['user_loggedin'] = $this->user->get_user_loggedin($this->db_con);
          $this->view_data['notice'] = "U bent ingelogd.";
          $this->view('home/index', $this->view_data);
        }
        else
        {
          $this->view_data['notice'] = "Er iets fout gegaan tijdens het inloggen.";
          $this->view('login/form', $this->view_data);
        }
      }
      else
      {
        $this->user->set_user_loggedin_failed($this->db_con, $user['user_id']);
        $this->view_data['notice'] = "Wachtwoord of gebruikersnaam fout.";
        $this->view('login/form', $this->view_data);
      }
    }
    else
    {
      $this->view_data['notice'] = "Wachtwoord of gebruikersnaam fout.";
      $this->view('login/form', $this->view_data);
    }
  }

}
?>
