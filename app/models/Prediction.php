<?php
class Prediction
{
	public function create_prediction($db_con, $match_id, $user_id, $home_team_score, $away_team_score)
	{
		$query_matchcheck = $db_con->prepare('SELECT * FROM matches WHERE match_id=:match_id AND match_status=6');
		$query_matchcheck->bindValue(':match_id', $match_id, PDO::PARAM_STR);
		$query_matchcheck->execute();

		$query_predictioncheck = $db_con->prepare('SELECT * FROM predictions WHERE match_id=:match_id AND user_id=:user_id');
		$query_predictioncheck->bindValue(':match_id', $match_id, PDO::PARAM_STR);
		$query_predictioncheck->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query_predictioncheck->execute();

		if($query_predictioncheck->fetch())
		{
			return false;
		}
		elseif($query_matchcheck->fetch())
		{
			$query = $db_con->prepare('INSERT INTO predictions(match_id, user_id, home_team_score, away_team_score) VALUES(:match_id, :user_id, :home_team_score, :away_team_score)');
			$query->bindValue(':match_id', $match_id, PDO::PARAM_STR);
			$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
			$query->bindValue(':home_team_score', $home_team_score, PDO::PARAM_STR);
			$query->bindValue(':away_team_score', $away_team_score, PDO::PARAM_STR);
			return $query->execute();
		}
		else
		{
			return false;
		}
	}

	public function create_predictions($db_con, $matches_ids, $user_id, $home_team_scores, $away_team_scores)
	{
		$result = false;

		foreach($matches_ids as $key => $match_id)
		{
			if($this->create_prediction($db_con, $match_id, $user_id, $home_team_scores[$key], $away_team_scores[$key]))
			{
				$result = true;
			}
			else
			{
				$result = false;
			}
		}

		return $result;
	}

	public function edit_prediction($db_con, $prediction_id, $home_team_score, $away_team_score)
	{
		$query = $db_con->prepare('
		UPDATE
		predictions AS p
		INNER JOIN
		matches AS m
		ON
		p.match_id = m.match_id
		SET
		p.home_team_score=:home_team_score, p.away_team_score=:away_team_score
		WHERE
		p.prediction_id=:prediction_id AND m.match_status=6
		');
		$query->bindValue(':prediction_id', $prediction_id, PDO::PARAM_STR);
		$query->bindValue(':home_team_score', $home_team_score, PDO::PARAM_STR);
		$query->bindValue(':away_team_score', $away_team_score, PDO::PARAM_STR);
		return $query->execute();
	}

	public function edit_prediction_by_user_id_and_prediction_id($db_con, $user_id, $prediction_id, $home_team_score, $away_team_score)
	{

		$query = $db_con->prepare('
		UPDATE
		predictions AS p
		INNER JOIN
		matches AS m
		ON
		p.match_id = m.match_id
		SET
		p.home_team_score=:home_team_score, p.away_team_score=:away_team_score
		WHERE
		p.prediction_id=:prediction_id AND user_id=:user_id AND m.match_status=6
		');
		$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query->bindValue(':prediction_id', $prediction_id, PDO::PARAM_STR);
		$query->bindValue(':home_team_score', $home_team_score, PDO::PARAM_STR);
		$query->bindValue(':away_team_score', $away_team_score, PDO::PARAM_STR);
		return $query->execute();
	}

	public function edit_predictions_by_user_id_and_prediction_ids($db_con, $user_id, $prediction_ids, $home_team_scores, $away_team_scores)
	{
		$result = false;

		foreach($prediction_ids as $key => $prediction_id)
		{
			if($this->edit_prediction_by_user_id_and_prediction_id($db_con, $user_id, $prediction_id, $home_team_scores[$key], $away_team_scores[$key]))
			{
				$result = true;
			}
			else
			{
				$result = false;
				return $result;
			}
		}

		return $result;
	}

	public function get_prediction_by_id($db_con, $prediction_id)
	{
		$query = $db_con->prepare('SELECT
		predictions.*,
		m.match_status as match_status
		FROM
		predictions
		INNER JOIN
		matches m
		ON
		predictions.match_id = m.match_id
		WHERE
		prediction_id=:prediction_id
		');
		$query->bindValue(':prediction_id', $prediction_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}

	public function get_prediction_by_id_and_match_id($db_con, $prediction_id)
	{
		$query = $db_con->prepare('SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name,
		predictions.prediction_id as prediction_id,
		predictions.home_team_score as prediction_home_team_score,
		predictions.away_team_score as prediction_away_team_score
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
		INNER JOIN
		predictions
		ON
		matches.match_id = predictions.match_id

		WHERE predictions.prediction_id=:prediction_id AND match_id=:match_id
		ORDER BY match_datetime ASC');
		$query->bindValue(':prediction_id', $prediction_id, PDO::PARAM_STR);
		$query->bindValue(':match_id', $prediction_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}

	public function get_prediction_by_user_id_and_match_id($db_con, $user_id, $match_id)
	{
		$query = $db_con->prepare('SELECT * FROM predictions WHERE user_id=:user_id AND match_id=:match_id');
		$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query->bindValue(':match_id', $match_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}

	public function get_predictions_by_user_id($db_con, $user_id)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name,
		predictions.prediction_id as prediction_id,
		predictions.home_team_score as prediction_home_team_score,
		predictions.away_team_score as prediction_away_team_score
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
		INNER JOIN
		predictions
		ON
		matches.match_id = predictions.match_id

		WHERE predictions.user_id=:user_id
		ORDER BY match_datetime ASC');
		$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_predictions_by_league_id_and_user_id_and_matchday($db_con, $league_id, $user_id, $matchday)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name,
		predictions.prediction_id as prediction_id,
		predictions.home_team_score as prediction_home_team_score,
		predictions.away_team_score as prediction_away_team_score
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
		INNER JOIN
		predictions
		ON
		matches.match_id = predictions.match_id

		WHERE matches.league_id=:league_id AND matches.league_matchday=:matchday AND predictions.user_id=:user_id
		ORDER BY match_datetime ASC
		');
		$query->bindValue('league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue('user_id', $user_id, PDO::PARAM_STR);
		$query->bindValue('matchday', $matchday, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_with_predictions_by_league_id_and_user_id_and_matchday($db_con, $league_id, $user_id, $matchday)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name,
		predictions.prediction_id as prediction_id,
		predictions.home_team_score as prediction_home_team_score,
		predictions.away_team_score as prediction_away_team_score
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
		LEFT OUTER JOIN
		predictions
		ON
		matches.match_id = predictions.match_id
		AND predictions.user_id=:user_id

		WHERE matches.league_id=:league_id AND matches.league_matchday=:matchday
		ORDER BY match_datetime, match_id
		');
		$query->bindValue('league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue('user_id', $user_id, PDO::PARAM_STR);
		$query->bindValue('matchday', $matchday, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_matches_with_predictions_by_league_id_and_team_id_and_user_id($db_con, $league_id, $team_id, $user_id)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name,
		predictions.prediction_id as prediction_id,
		predictions.home_team_score as prediction_home_team_score,
		predictions.away_team_score as prediction_away_team_score
		FROM
		(
		SELECT * FROM matches
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
		LEFT OUTER JOIN
		predictions
		ON
		matches.match_id = predictions.match_id AND predictions.user_id = :user_id

		ORDER BY match_datetime ASC
		');
		$query->bindValue('league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue('team_id', $team_id, PDO::PARAM_STR);
		$query->bindValue('team_id2', $team_id, PDO::PARAM_STR);
		$query->bindValue('user_id', $user_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_all_matches_with_predictions_by_league_id_and_team_id_and_user_id($db_con, $league_id, $team_id, $user_id)
	{
		$query = $db_con->prepare('
		SELECT
		matches.*,
		home_team.team_name as home_team_name,
		home_team.team_tag as home_team_tag,
		away_team.team_name as away_team_name,
		away_team.team_tag as away_team_tag,
		match_league.league_name as league_name,
		predictions.prediction_id as prediction_id,
		predictions.home_team_score as prediction_home_team_score,
		predictions.away_team_score as prediction_away_team_score
		FROM
		(
		SELECT * FROM matches
		WHERE (matches.home_team_id=:team_id OR matches.away_team_id=:team_id2) AND matches.league_id=:league_id
		ORDER BY match_datetime DESC
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
		LEFT OUTER JOIN
		predictions
		ON
		matches.match_id = predictions.match_id AND predictions.user_id = :user_id

		ORDER BY match_datetime ASC
		');
		$query->bindValue('league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue('team_id', $team_id, PDO::PARAM_STR);
		$query->bindValue('team_id2', $team_id, PDO::PARAM_STR);
		$query->bindValue('user_id', $user_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function get_last_prediction_by_user_id_and_league_id($db_con, $user_id, $league_id)
	{
		$query = $db_con->prepare('SELECT
		predictions.*,
		m.league_id as league_id,
		m.match_datetime as match_datetime
		FROM
		predictions
		INNER JOIN
		matches m
		ON
		predictions.match_id = m.match_id AND league_id=:league_id
		WHERE
		user_id=:user_id
		ORDER BY match_datetime DESC
    LIMIT 1
		');
		$query->bindValue('user_id', $user_id, PDO::PARAM_STR);
		$query->bindValue('league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetch();
	}
}
