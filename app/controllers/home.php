<?php
  class home extends Controller
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

      $this->view('home/index', $this->view_data);
    }

    public function faq()
    {
      $this->view('home/faq', $this->view_data);
    }

    public function logout()
    {
      if($this->user->set_user_loggedout($this->db_con, $this->user_loggedin['user_id']) != false)
      {
        $this->view_data['notice'] = "U bent uitgelogd.";
        $this->view_data['user_loggedin'] = $this->user->get_user_loggedin($this->db_con);
        $this->view('home/index', $this->view_data);
      }
      else
      {
        $this->view_data['notice'] = "Er is iets fout gegaan tijdens het uitloggen.";
        $this->view_data['user_loggedin'] = $this->user->get_user_loggedin($this->db_con);
        $this->view('home/index', $this->view_data);
      }
    }
  }
?>
