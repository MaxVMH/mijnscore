<?php
// this is the controller base class, extensions are in app/controllers/
class Controller
{
	private $db_con = false;

	public function model($model)
	{
		require_once '../app/models/' .$model .'.php';
		return new $model();
	}

	public function view($view, $data)
	{
		$this->clean_output($data);
		require_once '../app/views/' .$view .'.php';
	}

	public function db_con()
	{
		if($this->db_con == false)
		{
			$database = new Database();
			$this->db_con = $database->db_con;
		}

		return $this->db_con;
	}

	// this can have a better place in the code but for now it's ok here
	// this mainly checks the data types in any user input
	public function validate_input($variable, $value)
	{
		$result = false;

		if($variable == "email")
		{
			if(!empty($value) && (strlen($value) < 65) && filter_var($value, FILTER_VALIDATE_EMAIL))
			{
				$result = true;
			}
		}
		elseif($variable == "password")
		{
			if(!empty($value) && strlen($value) > 5)
			{
				$result = true;
			}
		}
		elseif($variable == "username")
		{
			if(!empty($value) && preg_match('/^[a-z\d]{2,64}$/i', $value))
			{
				$result = true;
			}
		}
		elseif($variable == "datetime")
		{
			$d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
			$result = $d && $d->format($format) == $date;
		}
		elseif($variable == "match_score")
		{
			if(!empty($value) && preg_match('/^[0-9]{1,2}$/', $value))
			{
				$result = true;
			}
		}

		return $result;
	}

	// this can have a better place in the code but for now it's ok here
	// this is to clean up all data before it goes to the views, as to avoid html injection
	public function clean_output(&$data)
	{
		if(!is_array($data))
		{
			$data = htmlspecialchars($data);
		}
		else
		{
			foreach($data as &$data_1)
			{
				$this->clean_output($data_1);
			}
		}
	}
}
