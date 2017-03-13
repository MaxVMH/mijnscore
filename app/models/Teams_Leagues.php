<?php
class Teams_Leagues
{
	public function get_leagues_by_team_id($db_con, $team_id)
	{
		$query = $db_con->prepare('
		SELECT
		leagues.*,
		tl.team_id as teams_leagues
		FROM
		leagues
		JOIN
		teams_leagues tl
		ON
		leagues.league_id=tl.league_id
		AND
		tl.team_id=:team_id
		');
		$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_all_leagues_by_team_id($db_con, $team_id)
	{
		$query = $db_con->prepare('
		SELECT
		leagues.*,
		tl.team_id as teams_leagues
		FROM
		leagues
		LEFT JOIN
		teams_leagues tl
		ON
		leagues.league_id=tl.league_id
		AND
		tl.team_id=:team_id
		');
		$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function set_team_league($db_con, $team_id, $league_id)
	{
		$query = $db_con->prepare('SELECT * FROM teams_leagues WHERE team_id=:team_id AND league_id=:league_id');
		$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		if($query->fetch())
		{
			return false;
		}
		else
		{
			$query = $db_con->prepare('INSERT INTO teams_leagues(team_id, league_id) VALUES(:team_id, :league_id)');
			$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
			$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
			return $query->execute();
		}
	}

	public function unset_team_league($db_con, $team_id, $league_id)
	{
		$query = $db_con->prepare('DELETE FROM teams_leagues WHERE team_id=:team_id AND league_id=:league_id');
		$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
	}

	public function get_teams_by_league_id($db_con, $league_id)
	{
		$query = $db_con->prepare('
		SELECT
		teams.*,
		tl.league_id as league_id
		FROM
		teams
		INNER JOIN
		teams_leagues tl
		ON
		teams.team_id=tl.team_id
		AND
		tl.league_id=:league_id
		');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}
}
