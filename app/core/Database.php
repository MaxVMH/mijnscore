<?php
// this class is the database connection, the DB_HOST, DB_NAME, DB_USER, DB_PASS parameters are in app/init.php
class Database
{
	public $db_con = false;

	public function __construct()
	{
		try
		{
			$this->db_con = new PDO('mysql:host='. DB_HOST .';dbname=' . DB_NAME .';charset=utf8', DB_USER, DB_PASS);
		}
		catch (PDOException $e)
		{
			error_log($e->getMessage(), 0);
		}

		if($this->db_con != false)
		{
			$this->db_con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}
}
?>
