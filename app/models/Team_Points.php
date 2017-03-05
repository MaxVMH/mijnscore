<?php
class Team_Points
{
	public function get_teams_and_points_by_league_id($db_con, $league_id)
	{
		$query = $db_con->prepare('
		SELECT
		teams_points.*,
		teams.team_name as team_name,
		teams.team_tag as team_tag
		FROM
		teams_points
		INNER JOIN
		teams
		ON
		teams_points.team_id = teams.team_id
		WHERE
		league_id=:league_id
		ORDER BY
		points_amount DESC, matches_won DESC, (goals_scored - goals_allowed), goals_scored DESC
		');

		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

	public function set_team_points_by_league_id($db_con, $league_id)
	{
		$result = false;

		$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND match_status<=4');
		$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
		$query->execute();
		$matches = $query->fetchAll();

		$team_points = [];

		foreach($matches as $match)
		{
			if(empty($team_points[$match['home_team_id']]))
			{
				$team_points[$match['home_team_id']] = [];
				$team_points[$match['home_team_id']]['points'] = 0;
			}
			if(empty($team_points[$match['away_team_id']]))
			{
				$team_points[$match['away_team_id']] = [];
				$team_points[$match['away_team_id']]['points'] = 0;
			}



			if(empty($team_points[$match['home_team_id']]['matches_won']))
			{
				$team_points[$match['home_team_id']]['matches_won'] = 0;
			}
			if(empty($team_points[$match['home_team_id']]['matches_draw']))
			{
				$team_points[$match['home_team_id']]['matches_draw'] = 0;
			}
			if(empty($team_points[$match['home_team_id']]['matches_lost']))
			{
				$team_points[$match['home_team_id']]['matches_lost'] = 0;
			}

			if(empty($team_points[$match['away_team_id']]['matches_won']))
			{
				$team_points[$match['away_team_id']]['matches_won'] = 0;
			}
			if(empty($team_points[$match['away_team_id']]['matches_draw']))
			{
				$team_points[$match['away_team_id']]['matches_draw'] = 0;
			}
			if(empty($team_points[$match['away_team_id']]['matches_lost']))
			{
				$team_points[$match['away_team_id']]['matches_lost'] = 0;
			}

			if(empty($team_points[$match['home_team_id']]['goals_scored']))
			{
				$team_points[$match['home_team_id']]['goals_scored'] = 0;
			}
			if(empty($team_points[$match['home_team_id']]['goals_allowed']))
			{
				$team_points[$match['home_team_id']]['goals_allowed'] = 0;
			}

			if(empty($team_points[$match['away_team_id']]['goals_scored']))
			{
				$team_points[$match['away_team_id']]['goals_scored'] = 0;
			}
			if(empty($team_points[$match['away_team_id']]['goals_allowed']))
			{
				$team_points[$match['away_team_id']]['goals_allowed'] = 0;
			}

			$team_points[$match['home_team_id']]['goals_scored'] = $team_points[$match['home_team_id']]['goals_scored'] + $match['home_team_score'];
			$team_points[$match['home_team_id']]['goals_allowed'] = $team_points[$match['home_team_id']]['goals_allowed'] + $match['away_team_score'];

			$team_points[$match['away_team_id']]['goals_scored'] = $team_points[$match['away_team_id']]['goals_scored'] + $match['away_team_score'];
			$team_points[$match['away_team_id']]['goals_allowed'] = $team_points[$match['away_team_id']]['goals_allowed'] + $match['home_team_score'];

			if($match['home_team_score'] > $match['away_team_score'])
			{
				$team_points[$match['home_team_id']]['points'] = $team_points[$match['home_team_id']]['points'] + 3;

				$team_points[$match['home_team_id']]['matches_won'] = $team_points[$match['home_team_id']]['matches_won'] + 1;
				$team_points[$match['away_team_id']]['matches_lost'] = $team_points[$match['away_team_id']]['matches_lost'] + 1;
			}
			elseif($match['home_team_score'] == $match['away_team_score'])
			{
				$team_points[$match['home_team_id']]['points'] = $team_points[$match['home_team_id']]['points'] + 1;
				$team_points[$match['away_team_id']]['points'] = $team_points[$match['away_team_id']]['points'] + 1;

				$team_points[$match['home_team_id']]['matches_draw'] = $team_points[$match['home_team_id']]['matches_draw'] + 1;
				$team_points[$match['away_team_id']]['matches_draw'] = $team_points[$match['away_team_id']]['matches_draw'] + 1;
			}
			elseif($match['home_team_score'] < $match['away_team_score'])
			{
				$team_points[$match['away_team_id']]['points'] = $team_points[$match['away_team_id']]['points'] + 3;

				$team_points[$match['home_team_id']]['matches_lost'] = $team_points[$match['home_team_id']]['matches_lost'] + 1;
				$team_points[$match['away_team_id']]['matches_won'] = $team_points[$match['away_team_id']]['matches_won'] + 1;
			}

		}

		foreach($team_points as $key => $points)
		{
			$points_amount = $points['points'];
			$points_matches_won = $points['matches_won'];
			$points_matches_draw = $points['matches_draw'];
			$points_matches_lost = $points['matches_lost'];

			$points_goals_scored = $points['goals_scored'];
			$points_goals_allowed = $points['goals_allowed'];

			$query = $db_con->prepare('SELECT * FROM teams_points WHERE team_id=:team_id AND league_id=:league_id');
			$query->bindValue(':team_id', $key, PDO::PARAM_STR);
			$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
			$query->execute();
			if($points = $query->fetch())
			{
				$query = $db_con->prepare('UPDATE teams_points SET points_amount=:points_amount, matches_won=:matches_won, matches_draw=:matches_draw, matches_lost=:matches_lost, goals_scored=:goals_scored, goals_allowed=:goals_allowed WHERE team_id=:team_id AND league_id=:league_id');
				$query->bindValue(':team_id', $key, PDO::PARAM_STR);
				$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
				$query->bindValue(':points_amount', $points_amount, PDO::PARAM_STR);
				$query->bindValue(':matches_won', $points_matches_won, PDO::PARAM_STR);
				$query->bindValue(':matches_draw', $points_matches_draw, PDO::PARAM_STR);
				$query->bindValue(':matches_lost', $points_matches_lost, PDO::PARAM_STR);

				$query->bindValue(':goals_scored', $points_goals_scored, PDO::PARAM_STR);
				$query->bindValue(':goals_allowed', $points_goals_allowed, PDO::PARAM_STR);

				$query->execute();
			}
			else
			{
				$query = $db_con->prepare('INSERT INTO teams_points(league_id, team_id, points_amount) VALUES(:league_id, :team_id, :points_amount)');
				$query->bindValue(':team_id', $key, PDO::PARAM_STR);
				$query->bindValue(':league_id', $league_id, PDO::PARAM_STR);
				$query->bindValue(':points_amount', $points_amount, PDO::PARAM_STR);
				$query->execute();
			}
		}

		return true;

	}
}
