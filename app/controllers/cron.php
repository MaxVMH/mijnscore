<?php
// the cron controllers do stuff like e-mail notifications
// they can be called upon from the server shell or by URL
class cron extends Controller
{

  public function __construct()
  {
    $this->db_con = $this->db_con();

    $this->user = $this->model('User');
    $this->mail = $this->model('Mail');
    $this->league = $this->model('League');
    $this->prediction = $this->model('Prediction');
    $this->team_points = $this->model('Team_Points');
    $this->prediction_points = $this->model('Prediction_Points');

    $this->view_data = [];
  }

  public function index()
  {
    $this->view_data['notice'] = "Geen cron jobs uitgevoerd.";
    $this->view('home/index', $this->view_data);
  }

  public function notification_emails()
  {
    if($this->mail->send_notification_emails($this->db_con))
    {
      $this->view_data['notice'] = "E-mail herinneringen verzonden!";
    }
    else
    {
      $this->view_data['notice'] = "Geen e-mail herinneringen verzonden.";
    }

    $this->view_data['leagues'] = $this->league->get_leagues_all($this->db_con);
    $this->view('home/index', $this->view_data);
  }
}
?>
