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

	// this method selects matches and predictions, calculates the points for the predictions and updates the database
	public function set_prediction_points_by_league_id($db_con, $league_id)
	{
		// select the matches that have a score but have not yet been calculated
		$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_status=4 ORDER BY league_playday');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$matches = $query->fetchAll();

		foreach($matches as $match)
		{
			// select the predictions for each match
			$query = $db_con->prepare('SELECT * FROM predictions WHERE match_id=:match_id ORDER BY user_id');
			$query->bindValue(':match_id', $match['match_id'], PDO::PARAM_STR);
			$query->execute();
			$predictions = $query->fetchAll();

			foreach($predictions as $prediction)
			{
				// calculate the points
				$prediction_points = $this->get_prediction_points_by_scores($match['home_team_score'], $match['away_team_score'], $prediction['home_team_score'], $prediction['away_team_score']);
				// put the points in the database
				$query = $db_con->prepare('UPDATE predictions SET prediction_points=:prediction_points WHERE prediction_id=:prediction_id');
				$query->bindValue(':prediction_points', $prediction_points, PDO::PARAM_STR);
				$query->bindValue(':prediction_id', $prediction['prediction_id'], PDO::PARAM_STR);
				$query->execute();
			}

			// the matches have been calculated so they need a status update
			$query = $db_con->prepare('UPDATE matches SET match_status=3 WHERE match_id=:match_id');
			$query->bindValue(':match_id', $match['match_id'], PDO::PARAM_STR);
			$query->execute();
		}

		$this->set_playday_prediction_points_by_league_id($db_con, $league_id);
		$this->set_user_ranking_by_league_id($db_con, $league_id);
	}

	// this method selects matches and predictions, calculates playday totals and updates the database
	public function set_playday_prediction_points_by_league_id($db_con, $league_id)
	{
		// if the league has a parent league, we have to add half of the points of the parent league
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_id=:league_id');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$league = $query->fetch();

		if($league['league_parent_id'] != null)
		{
			$query = $db_con->prepare('SELECT * FROM leagues WHERE league_id=:league_id');
			$query->bindValue(':league_id', $league['league_parent_id'], PDO::PARAM_STR);
			$query->execute();
			$parent_league = $query->fetch();
		}

		// select the matches that have been calculated
		$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_status=3 ORDER BY league_playday');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$matches = $query->fetchAll();

		foreach($matches as $match)
		{
			// select the predictions for each match
			$query = $db_con->prepare('SELECT * FROM predictions WHERE match_id=:match_id ORDER BY user_id');
			$query->bindValue(':match_id', $match['match_id'], PDO::PARAM_STR);
			$query->execute();
			$predictions = $query->fetchAll();

			// calculate the points per playday and collect it in array $predictions_points
			// playday 0 is the league total
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
				$predictions_points[$prediction['user_id']][0] = $predictions_points[$prediction['user_id']][0] + $prediction['prediction_points'];
				$predictions_points[$prediction['user_id']][$match['league_playday']] = $predictions_points[$prediction['user_id']][$match['league_playday']] + $prediction['prediction_points'];
			}
		}

		// put the points in the database
		foreach($predictions_points as $user_id => $points_playdays)
		{
			foreach($points_playdays as $league_playday => $points_amount)
			{
				$parent_league_points_amount = 0;

				if(!empty($parent_league))
				{
					$query = $db_con->prepare('SELECT * FROM predictions_points WHERE user_id=:user_id AND league_id=:league_id AND league_playday=0');
					$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
					$query->bindValue(':league_id', $parent_league['league_id'], PDO::PARAM_STR);
					$query->execute();
					$parent_league_prediction_points = $query->fetch();
					$parent_league_points_amount = $parent_league_prediction_points['points_amount'] / 2;
				}
				$points_corrected_amount = $points_amount + ($parent_league_points_amount);

				$query = $db_con->prepare('SELECT * FROM predictions_points WHERE user_id=:user_id AND league_id=:league_id AND league_playday=:league_playday');
				$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
				$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
				$query->bindValue(':league_playday', $league_playday, PDO::PARAM_STR);
				$query->execute();

				if($points = $query->fetch())
				{
					if($points['points_amount'] != $points_corrected_amount)
					{
						$query = $db_con->prepare('UPDATE predictions_points SET points_amount=:points_amount WHERE points_id=:points_id');
						$query->bindValue(':points_id', $points['points_id'], PDO::PARAM_STR);
						$query->bindValue(':points_amount', $points_corrected_amount, PDO::PARAM_STR);
						$query->execute();
					}
				}
				else
				{
					$query = $db_con->prepare('INSERT INTO predictions_points(league_id, league_playday, user_id, points_amount) VALUES(:league_id, :league_playday, :user_id, :points_amount)');
					$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
					$query->bindValue(':league_playday', $league_playday, PDO::PARAM_STR);
					$query->bindValue(':user_id', $user_id, PDO::PARAM_STR);
					$query->bindValue(':points_amount', $points_corrected_amount, PDO::PARAM_STR);
					$query->execute();
				}
			}
		}
	}

	public function set_user_ranking_by_league_id($db_con, $league_id)
	{
		$query = $db_con->prepare('SELECT * FROM predictions_points WHERE league_id=:league_id AND league_playday>0 ORDER BY league_playday ASC, points_amount DESC');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$predictions_points = $query->fetchAll();

		$ranking = 0;
		$ranking_share = 0;
		$previous_playday = 0;
		$previous_points = 0;
		$previous_ranking_corrected = 0;

		foreach($predictions_points as $points)
		{
			// if someone has the same amount of points in the ranking and they are in the top 3, they share their place
			if($points['points_amount'] == $previous_points && ($previous_ranking_corrected == 1 || $previous_ranking_corrected == 2 || $previous_ranking_corrected == 3) && $previous_playday == $points['league_playday'])
			{
				$ranking_share = $previous_ranking_corrected;
				$ranking = $ranking + 1;
			}
			elseif($previous_playday != $points['league_playday'])
			{
				$ranking = 1;
			}
			else
			{
				$ranking = $ranking + 1;
			}

			if($ranking_share != 0)
			{
				$ranking_corrected = $ranking_share;
			}
			else
			{
				$ranking_corrected = $ranking;
			}

			if($points['league_user_ranking'] != $ranking_corrected)
			{
				$query = $db_con->prepare('UPDATE predictions_points SET league_user_ranking=:ranking WHERE points_id=:points_id');
				$query->bindValue(':ranking', $ranking_corrected, PDO::PARAM_STR);
				$query->bindValue(':points_id', $points['points_id'], PDO::PARAM_STR);
				$query->execute();
			}

			$previous_playday = $points['league_playday'];
			$previous_points = $points['points_amount'];
			$previous_ranking_corrected = $ranking_corrected;
			$ranking_share = 0;
		}

		// for the season total amount of points, we calculate the ranking a little different
		// in case of the same amount of points, the person with highest amount of correct predictions gets the highest place
		$query = $db_con->prepare('SELECT *, (
			SELECT COUNT(*) FROM predictions p
			INNER JOIN matches m
			ON m.match_id = p.match_id
			WHERE p.user_id=p_p.user_id
			AND p.prediction_points>=3
		) AS correctpredictions_count
		FROM
		predictions_points p_p
		WHERE p_p.league_id=:league_id AND p_p.league_playday=0
		ORDER BY p_p.points_amount DESC, correctpredictions_count DESC');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$predictions_points = $query->fetchAll();

		$ranking = 0;

		foreach($predictions_points as $points)
		{
			if($ranking == 0)
			{
				$ranking = 1;
			}
			else
			{
				$ranking = $ranking + 1;
			}

			$query = $db_con->prepare('UPDATE predictions_points SET league_user_ranking=:ranking WHERE points_id=:points_id');
			$query->bindValue(':ranking', $ranking, PDO::PARAM_STR);
			$query->bindValue(':points_id', $points['points_id'], PDO::PARAM_STR);
			$query->execute();
		}
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
