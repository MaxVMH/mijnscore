<?php
class Prediction_Points
{
	public function get_prediction_points_by_user_id($db_con, $user_id)
	{
		$result = false;
		$query = $db_con->prepare('
		SELECT
		p_p.*,
		l.league_name,
		l.league_playday_current
		FROM
		predictions_points p_p
		INNER JOIN
		leagues l
		ON
		p_p.league_id = l.league_id
		WHERE
		p_p.user_id = :user_id AND p_p.league_playday = 0
		ORDER BY
		l.league_id ASC
		');
		$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetchAll();

		return $result;
	}

	public function get_users_and_points_by_league_id_and_playday($db_con, $league_id, $league_playday)
	{
		$result = false;

		$query = $db_con->prepare('
		SELECT
		u.*,
		p_p.points_amount,
		p_p.league_user_ranking
		FROM
		users u
		INNER JOIN
		predictions_points p_p
		ON
		u.user_id = p_p.user_id AND p_p.league_id = :league_id AND p_p.league_playday = :league_playday
		ORDER BY
		p_p.league_user_ranking
		ASC
		');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->bindValue(':league_playday', $league_playday, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetchAll();

		return $result;
	}

	// this method will eventually replace set_prediction_points_by_league_id
	// this function has not been completed yet

	public function set_prediction_points_by_league_id_new($db_con, $league_id)
	{
		$result = false;
		$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_status=4 ORDER BY league_playday');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$matches = $query->fetchAll();

		foreach($matches as $match)
		{
			//
		}
	}

	public function set_prediction_points_by_league_id($db_con, $league_id)
	{
		$result = false;

		try
		{
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_status<=4 ORDER BY league_playday');
			$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
			$query->execute();
			$matches = $query->fetchAll();

			foreach($matches as $match)
			{
				$query = $db_con->prepare('SELECT * FROM predictions WHERE match_id=:match_id ORDER BY user_id');
				$query->bindValue(':match_id', $match['match_id'], PDO::PARAM_STR);
				$query->execute();
				$predictions = $query->fetchAll();

				foreach($predictions as $prediction)
				{
					if(empty($predictions_points[$prediction['user_id']][0]))
					{
						$predictions_points[$prediction['user_id']][0] = 0;
					}

					if(empty($predictions_points[$prediction['user_id']][$match['league_playday']]))
					{
						$predictions_points[$prediction['user_id']][$match['league_playday']] = 0;
					}
					$prediction_points = $this->get_prediction_points_by_scores($match['home_team_score'], $match['away_team_score'], $prediction['home_team_score'], $prediction['away_team_score']);
					$predictions_points[$prediction['user_id']][0] = $predictions_points[$prediction['user_id']][0] + $prediction_points;
					$predictions_points[$prediction['user_id']][$match['league_playday']] = $predictions_points[$prediction['user_id']][$match['league_playday']] + $prediction_points;

					$query = $db_con->prepare('UPDATE predictions SET prediction_points=:prediction_points WHERE prediction_id=:prediction_id');
					$query->bindValue(':prediction_points', $prediction_points, PDO::PARAM_STR);
					$query->bindValue(':prediction_id', $prediction['prediction_id'], PDO::PARAM_STR);
					$query->execute();
				}
			}

			foreach($predictions_points as $user_id => $points_playdays)
			{

				foreach($points_playdays as $league_playday => $points_amount)
				{
					$query = $db_con->prepare('SELECT * FROM predictions_points WHERE user_id=:user_id AND league_id=:league_id AND league_playday=:league_playday');
					$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
					$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
					$query->bindValue(':league_playday', $league_playday, PDO::PARAM_STR);
					$query->execute();

					if($points = $query->fetch())
					{
						$query = $db_con->prepare('UPDATE predictions_points SET points_amount=:points_amount WHERE points_id=:points_id');
						$query->bindValue(':points_id', $points['points_id'], PDO::PARAM_STR);
						$query->bindValue(':points_amount', $points_amount, PDO::PARAM_STR);
						if($query->execute())
						{
							$result = true;
						}
						else
						{
							$result = false;
							throw new Exception("Something went wrong.");
						}
					}
					else
					{
						$query = $db_con->prepare('INSERT INTO predictions_points(league_id, league_playday, user_id, points_amount) VALUES(:league_id, :league_playday, :user_id, :points_amount)');
						$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
						$query->bindValue(':league_playday', $league_playday, PDO::PARAM_STR);
						$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
						$query->bindValue(':points_amount', $points_amount, PDO::PARAM_STR);
						if($query->execute())
						{
							$result = true;
						}
						else
						{
							$result = false;
							throw new Exception("Something went wrong.");
						}
					}
				}
			}
		}
		catch(Exception $e)
		{
			$result = false;
			return $result;
		}

		$query = $db_con->prepare('
		SELECT *, (
			SELECT COUNT(*) FROM predictions p
				INNER JOIN matches m
				ON m.match_id = p.match_id
			WHERE p.user_id=p_p.user_id AND (m.league_playday=p_p.league_playday OR p_p.league_playday=0) AND p.prediction_points>=3
		) AS correctpredictions_count
		FROM
		predictions_points p_p
		WHERE
		p_p.league_id=:league_id
		ORDER BY
		p_p.league_playday ASC, p_p.points_amount DESC, correctpredictions_count DESC, league_user_ranking ASC
		');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();

		$predictions_points = 0;
		$ranking = 0;
		$previous_playday = 0;
		$predictions_points = $query->fetchAll();

		foreach($predictions_points as $prediction_points)
		{
			$ranking = $ranking + 1;

			if($previous_playday != $prediction_points['league_playday'])
			{
				$ranking = 1;
			}

			$query = $db_con->prepare('UPDATE predictions_points SET league_user_ranking=:ranking WHERE points_id=:points_id');
			$query->bindValue(':ranking', $ranking, PDO::PARAM_STR);
			$query->bindValue(':points_id', $prediction_points['points_id'], PDO::PARAM_STR);
			$query->execute();

			$previous_playday = $prediction_points['league_playday'];
		}

		return $result;
	}


	public function get_prediction_points_by_scores($home_score, $away_score, $prediction_home_score, $prediction_away_score)
	{
		$result = 0;

		// 1 punt
		if($home_score > $away_score && $prediction_home_score > $prediction_away_score)
		{
			$result = $result + 1;
		}
		elseif($home_score == $away_score && $prediction_home_score == $prediction_away_score)
		{
			$result = $result + 1;
		}
		elseif($home_score < $away_score && $prediction_home_score < $prediction_away_score)
		{
			$result = $result + 1;
		}

		// 1 punt + 2 punten = 3 punten
		if($home_score == $prediction_home_score && $away_score == $prediction_away_score)
		{
			$result = $result + 2;

			// 3 punten + 0 punten = 3 punten
			if($home_score <= 1 && $away_score <= 1)
			{
				$result = $result;
			}
			elseif($home_score == 2 && $away_score <= 1)
			{
				$result = $result;
			}
			elseif($home_score <= 1 && $away_score == 2)
			{
				$result = $result;
			}
			// 3 punten + 1 punt = 4 punten
			elseif($home_score == 3 && $away_score <= 1)
			{
				$result = $result + 1;
			}
			elseif($home_score <= 1 && $away_score == 3)
			{
				$result = $result + 1;
			}
			elseif($home_score == 0 && $away_score == 0)
			{
				$result = $result + 1;
			}
			// 3 punten + 2 punten = 5 punten
			elseif($home_score == 2 && $away_score == 2)
			{
				$result = $result + 2;
			}
			elseif($home_score == 3 && $away_score == 2)
			{
				$result = $result + 2;
			}
			elseif($home_score == 2 && $away_score == 3)
			{
				$result = $result + 2;
			}
			// 3 punten + 3 punten = 6 punten
			elseif($home_score == 4 && $away_score <=1)
			{
				$result = $result + 3;
			}
			elseif($home_score <=1 && $away_score == 4)
			{
				$result = $result + 3;
			}
			// 3 punten + 4 punten = 7 punten
			else
			{
				$result = $result + 4;
			}

		}

		return $result;
	}
}
