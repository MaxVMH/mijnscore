<?php
class League
{
	public function create_league($db_con, $name, $tag, $playday_total)
	{
		$query = $db_con->prepare('INSERT INTO leagues(league_name, league_tag, league_status, league_playday_total) VALUES(:name, :tag, 0, :playday_total)');
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		$query->bindValue(':tag', $tag, PDO::PARAM_STR);
		$query->bindValue(':playday_total', $playday_total, PDO::PARAM_STR);
		return $query->execute();
	}

	public function edit_league($db_con, $id, $name, $tag, $playday_current, $playday_total, $league_status)
	{
		$query = $db_con->prepare('UPDATE leagues SET league_name=:name, league_tag=:tag, league_playday_current=:playday_current, league_playday_total=:playday_total, league_status=:league_status WHERE league_id=:id');
		$query->bindValue(':id', $id, PDO::PARAM_STR);
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		$query->bindValue(':tag', $tag, PDO::PARAM_STR);
		$query->bindValue(':playday_current', $playday_current, PDO::PARAM_STR);
		$query->bindValue(':playday_total', $playday_total, PDO::PARAM_STR);
		$query->bindValue(':league_status', $league_status, PDO::PARAM_STR);
		return $query->execute();
	}

	public function get_leagues_all($db_con)
	{
		$query = $db_con->prepare('SELECT * FROM leagues');
		$query->execute();
		return $query->fetchAll();
	}

	public function get_leagues_by_status($db_con, $status)
	{
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_status=:status');
		$query->bindValue(':status', $status, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_league_by_id($db_con, $id)
	{
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_id=:id');
		$query->bindValue(':id', $id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}

	public function set_leagues_playday_auto($db_con)
	{
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_playday_current<league_playday_total');
		$query->execute();
		foreach($query->fetchAll() as $league)
		{
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_datetime>(NOW() - INTERVAL 1 DAY) ORDER BY match_datetime ASC LIMIT 1');
			$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
			$query->execute();
			if($match = $query->fetch())
			{
				if($match['league_playday'] > $league['league_playday_current'])
				{
					$query = $db_con->prepare('UPDATE leagues SET league_playday_current=:league_playday WHERE league_id=:league_id');
					$query->bindValue(':league_playday', $match['league_playday'], PDO::PARAM_STR);
					$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
					$query->execute();
				}
			}
		}
	}

	public function set_leagues_status_auto($db_con)
	{
		$query = $db_con->prepare('SELECT * FROM leagues WHERE (league_playday_current=0 OR league_playday_current=league_playday_total) AND league_status!=0');
		$query->execute();
		
		foreach($query->fetchAll() as $league)
		{
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_datetime>(NOW() - INTERVAL 1 MONTH) ORDER BY match_datetime DESC LIMIT 1');
			$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
			$query->execute();
			$match = $query->fetch();

			if(empty($match))
			{
				$query = $db_con->prepare('UPDATE leagues SET league_status=0 WHERE league_id=:league_id');
				$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
				$query->execute();
			}
		}

		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_playday_current!=0 AND league_playday_current<league_playday_total AND league_status=0');
		$query->execute();
		foreach($query->fetchAll() as $league)
		{
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_datetime<(NOW() + INTERVAL 1 MONTH) ORDER BY match_datetime DESC LIMIT 1');
			$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
			$query->execute();
			if($match = $query->fetch())
			{
				$query = $db_con->prepare('UPDATE leagues SET league_status=1 WHERE league_id=:league_id');
				$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
				$query->execute();
			}
		}
	}
}
?>
