<?php
class Match
{
	public function create_match($db_con, $datetime, $info, $league, $playday, $home, $away)
	{
		$query = $db_con->prepare('INSERT INTO matches(match_status, match_datetime, match_info, league_id, league_playday, home_team_id, away_team_id) VALUES(9, :datetime, :info, :league, :playday, :home, :away)');
		$query->bindValue(':datetime', $datetime, PDO::PARAM_STR);
		$query->bindValue(':info', $info, PDO::PARAM_STR);
		$query->bindValue(':league', $league, PDO::PARAM_STR);
		$query->bindValue(':playday', $playday, PDO::PARAM_STR);
		$query->bindValue(':home', $home, PDO::PARAM_STR);
		$query->bindValue(':away', $away, PDO::PARAM_STR);
		return $query->execute();
	}

	public function edit_match($db_con, $id, $status, $datetime, $info, $league, $playday, $home_team_id, $home_team_score, $away_team_id, $away_team_score)
	{
		$query = $db_con->prepare('
		UPDATE matches
		SET match_status=:status,
		match_datetime=:datetime,
		match_info=:info,
		league_id=:league,
		league_playday=:playday,
		home_team_id=:home_team_id,
		home_team_score=:home_team_score,
		away_team_id=:away_team_id,
		away_team_score=:away_team_score
		WHERE match_id=:id');
		$query->bindValue('status', $status, PDO::PARAM_STR);
		$query->bindValue('datetime', $datetime, PDO::PARAM_STR);
		$query->bindValue('info', $info, PDO::PARAM_STR);
		$query->bindValue('league', $league, PDO::PARAM_STR);
		$query->bindValue('playday', $playday, PDO::PARAM_STR);
		$query->bindValue('home_team_id', $home_team_id, PDO::PARAM_STR);
		$query->bindValue('home_team_score', $home_team_score, PDO::PARAM_STR);
		$query->bindValue('away_team_id', $away_team_id, PDO::PARAM_STR);
		$query->bindValue('away_team_score', $away_team_score, PDO::PARAM_STR);
		$query->bindValue('id', $id, PDO::PARAM_STR);
		return $query->execute();
	}

	public function get_match_by_id($db_con, $id)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag
		FROM
		matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		WHERE match_id=:id');
		$query->bindValue(':id', $id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}

	public function get_matches_all($db_con)
	{
		//$query = $db_con->prepare('SELECT * FROM matches');
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		leagues.league_name as league_name
		FROM
		matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues
		ON
		matches.league_id = leagues.league_id
		ORDER BY league_id, match_datetime ASC');
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_by_status($db_con, $status)
	{
		//$query = $db_con->prepare('SELECT * FROM matches');
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		leagues.league_name as league_name
		FROM
		matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues
		ON
		matches.league_id = leagues.league_id
		WHERE matches.match_status=:status
		ORDER BY match_datetime ASC');
		$query->bindValue(':status', $status, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_by_league_id($db_con, $league)
	{
		//$query = $db_con->prepare('SELECT * FROM matches');
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name
		FROM
		matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues match_league
		ON
		matches.league_id = match_league.league_id
		WHERE matches.league_id=:league AND matches.match_status<7 AND matches.match_status>0
		ORDER BY match_datetime ASC
		');

		$query->bindValue(':league', $league, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_by_league_id_and_status($db_con, $league, $status)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name
		FROM
		matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues match_league
		ON
		matches.league_id = match_league.league_id
		WHERE matches.league_id=:league AND matches.match_status=:status
		ORDER BY match_datetime ASC
		');
		$query->bindValue(':league', $league, PDO::PARAM_STR);
		$query->bindValue(':status', $status, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_by_league_id_and_team_id($db_con, $league_id, $team_id)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name
		FROM (SELECT * FROM matches
		WHERE (matches.home_team_id=:team_id OR matches.away_team_id=:team_id2) AND matches.league_id=:league_id
		ORDER BY ABS(DATEDIFF(match_datetime, NOW()))
		LIMIT 3
		)
		AS matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues match_league
		ON
		matches.league_id = match_league.league_id
		ORDER BY match_datetime ASC');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
		$query->bindValue(':team_id2', $team_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_by_league_id_and_playday($db_con, $league_id, $playday)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name
		FROM
		matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues match_league
		ON
		matches.league_id = match_league.league_id
		WHERE matches.league_id=:league_id AND matches.league_playday=:playday
		ORDER BY match_datetime ASC');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue(':playday', $playday, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_by_team_id($db_con, $team_id)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name
		FROM(SELECT * FROM matches
		WHERE matches.home_team_id=:team_id OR matches.away_team_id=:team_id2
		ORDER BY ABS(DATEDIFF(match_datetime, NOW()))
		LIMIT 5)
		AS matches
		INNER JOIN
		teams home_team
		ON
		matches.home_team_id = home_team.team_id
		INNER JOIN
		teams away_team
		ON
		matches.away_team_id = away_team.team_id
		INNER JOIN
		leagues match_league
		ON
		matches.league_id = match_league.league_id
		ORDER BY match_datetime ASC');
		$query->bindValue(':team_id', $team_id, PDO::PARAM_STR);
		$query->bindValue(':team_id2', $team_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_last_match_by_league_id($db_con, $league_id)
	{
		$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id ORDER BY league_playday DESC, match_datetime DESC LIMIT 1');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}

	public function set_matches_status_auto($db_con)
	{
		$query = $db_con->prepare('UPDATE matches SET match_status=5 WHERE match_status=6 AND match_datetime<NOW()');
		return $query->execute();
	}

	public function set_match_score_and_status($db_con, $match_id, $home_team_score, $away_team_score, $match_status)
	{
		$query = $db_con->prepare('UPDATE matches SET home_team_score=:home_team_score, away_team_score=:away_team_score, match_status=:match_status WHERE match_id=:match_id');
		$query->bindValue(':home_team_score', $home_team_score, PDO::PARAM_STR);
		$query->bindValue(':away_team_score', $away_team_score, PDO::PARAM_STR);
		$query->bindValue(':match_status', $match_status, PDO::PARAM_STR);
		$query->bindValue(':match_id', $match_id, PDO::PARAM_STR);
		return $query->execute();
	}
}
?>
