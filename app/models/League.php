<?php
class League
{
	public function create_league($db_con, $parent_id, $name, $tag, $playday_total)
	{
		$query = $db_con->prepare('INSERT INTO leagues(league_parent_id, league_name, league_tag, league_status, league_playday_total) VALUES(:parent_id, :name, :tag, 0, :playday_total)');
		$query->bindValue(':parent_id', $parent_id, PDO::PARAM_STR);
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		$query->bindValue(':tag', $tag, PDO::PARAM_STR);
		$query->bindValue(':playday_total', $playday_total, PDO::PARAM_STR);
		return $query->execute();
	}

	public function edit_league($db_con, $id, $parent_id, $name, $tag, $playday_current, $playday_total, $league_status)
	{
		$query = $db_con->prepare('UPDATE leagues SET league_parent_id=:parent_id, league_name=:name, league_tag=:tag, league_playday_current=:playday_current, league_playday_total=:playday_total, league_status=:league_status WHERE league_id=:id');
		$query->bindValue(':id', $id, PDO::PARAM_STR);
		$query->bindValue(':parent_id', $parent_id, PDO::PARAM_STR);
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		$query->bindValue(':tag', $tag, PDO::PARAM_STR);
		$query->bindValue(':playday_current', $playday_current, PDO::PARAM_STR);
		$query->bindValue(':playday_total', $playday_total, PDO::PARAM_STR);
		$query->bindValue(':league_status', $league_status, PDO::PARAM_STR);
		return $query->execute();
	}

	public function delete_league_by_id($db_con, $id)
	{
		$query = $db_con->prepare('DELETE FROM leagues WHERE league_id=:id');
		$query->bindValue(':id', $id, PDO::PARAM_STR);
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

	// this method sets the current playday of leagues by checking the date/time of the next match
	public function set_leagues_playday_auto($db_con)
	{
		// search for leagues that have not reached their last playday
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_playday_current<league_playday_total');
		$query->execute();
		foreach($query->fetchAll() as $league)
		{
			// search when the next match will be, but offset the time/date
			// this to allow the current playday some extra time, otherwise it would switch instantly after the start of the last match
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_datetime>(NOW() - INTERVAL 1 DAY) ORDER BY match_datetime ASC LIMIT 1');
			$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
			$query->execute();
			if($match = $query->fetch())
			{
				// compare the match playday to the current playday and if needed, update the current playday
				if($match['league_playday'] != $league['league_playday_current'])
				{
					$query = $db_con->prepare('UPDATE leagues SET league_playday_current=:league_playday WHERE league_id=:league_id');
					$query->bindValue(':league_playday', $match['league_playday'], PDO::PARAM_STR);
					$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
					$query->execute();
				}
			}
		}
	}

	// this method sets the status of leagues (active or inactive) by checking the date of the last match
	// this could be streamlined by just checking the last match of each league and go further from there
	public function set_leagues_status_auto($db_con)
	{
		// active to inactive
		// search for active leagues that are stuck on playday 0 or have reached their last playday
		$query = $db_con->prepare('SELECT * FROM leagues WHERE (league_playday_current=0 OR league_playday_current=league_playday_total) AND league_status!=0');
		$query->execute();
		foreach($query->fetchAll() as $league)
		{
			// then search if the league had a match in the nearby past
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_datetime>(NOW() - INTERVAL 1 MONTH) ORDER BY match_datetime DESC LIMIT 1');
			$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
			$query->execute();
			$match = $query->fetch();
			if(empty($match))
			{
				// the league didn't have a match in a nearby past, so it can be set inactive
				$query = $db_con->prepare('UPDATE leagues SET league_status=0 WHERE league_id=:league_id');
				$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
				$query->execute();
			}
		}

		// inactive to active
		// search for inactive leagues that are not stuck on playday 0 and have not reached their last playday
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_playday_current!=0 AND league_playday_current<league_playday_total AND league_status=0');
		$query->execute();
		foreach($query->fetchAll() as $league)
		{
			// then search if the league has a match in the nearby future
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_datetime<(NOW() + INTERVAL 1 MONTH) ORDER BY match_datetime DESC LIMIT 1');
			$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
			$query->execute();
			if($match = $query->fetch())
			{
				// the league has a match in the nearby future, so it can be set active
				$query = $db_con->prepare('UPDATE leagues SET league_status=1 WHERE league_id=:league_id');
				$query->bindValue(':league_id', $league['league_id'], PDO::PARAM_STR);
				$query->execute();
			}
		}
	}
}
?>
