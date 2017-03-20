<?php
require_once '../app/views/header.php';

$match_league_matchday_previous = $data['predictions']['0']['league_matchday'] - 1;
$match_league_matchday_next = $data['predictions']['0']['league_matchday'] + 1;

if($data['predictions']['0']['league_matchday'] == "1")
{
	$match_league_matchday_previous = $data['predictions']['0']['league_matchday'];
}

if($data['predictions']['0']['league_matchday'] > $data['league']['league_matchday_current'] + 2)
{
	$match_league_matchday_next = $data['predictions']['0']['league_matchday'];
}

if($data['predictions']['0']['league_matchday'] >= $data['league']['league_matchday_total'])
{
	$match_league_matchday_next = $data['predictions']['0']['league_matchday'];
}

if(isset($data['user']))
{
	$user_id = $data['user']['user_id'];
	$user_link = "Pronostiek van <a href=\"users/profile/" . $data['user']['user_id'] . "\">" . $data['user']['user_username'] . "</a>";
}
else
{
	$user_id = "";
	$user_link = "Mijn pronostiek";
}
?>



<h3><?= $user_link; ?>: <?= $data['league']['league_name']; ?></h3>
<table>
	<tr>
		<th colspan="6">
			<div style="float: left; text-align: left"><a href="predictions/index/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_previous; ?>/<?= $user_id; ?>" class="align-left">(vorige speeldag)</a></div>
			Speeldag <?= $data['predictions']['0']['league_matchday']; ?>
			<div style="float: right; text-align: right"><a href="predictions/index/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_next; ?>/<?= $user_id; ?>">(volgende speeldag)</a></div>
		</th>
	</tr>

	<tr>
		<th>Datum</th>
		<th class="align-right">Thuisploeg</th>
		<th>Prono</th>
		<th>Uitslag</th>
		<th class="align-left">Uitploeg</th>
		<th>Punten</th>
	</tr>

	<?php
	$matchday_prediction_score_counter = "0";

	foreach($data['predictions'] as $match)
	{
		?>

		<tr>
			<td><?= strftime("%a %e %b %G %H:%M", strtotime($match['match_datetime'])); ?></td>
			<td class="align-right"><a href="teams/single/<?= $match['home_team_id']; ?>"><?= $match['home_team_tag']; ?></a></td>

			<?php
			$prediction_score = "-";
			if(isset($match['prediction_id']))
			{
				$prediction_score = $match['prediction_home_team_score'] . " - " . $match['prediction_away_team_score'];
			}
			?>

			<td><?= $prediction_score; ?></td>

			<?php
			$match_score = "-";
			if ($match['match_status'] <= 4)
			{
				$match_score = $match['home_team_score'] . " - " . $match['away_team_score'];
			}
			?>

			<td><?= $match_score; ?></td>
			<td class="align-left"><a href="teams/single/<?= $match['away_team_id']; ?>"><?= $match['away_team_tag']; ?></a></td>

			<?php
			$prediction_points = "";
			if($match['match_status'] <= 4 && $match['prediction_id'] != null)
			{
				$prediction_points = $this->prediction_points->get_prediction_points_by_scores($match['home_team_score'], $match['away_team_score'], $match['prediction_home_team_score'], $match['prediction_away_team_score']);
				$matchday_prediction_score_counter = $matchday_prediction_score_counter + $prediction_points;
			}
			?>

			<td><?= $prediction_points; ?></td>
		</tr>

		<?php
	}
	?>

	<tr>
		<td colspan="5" class="align-right">Totaal speeldag</td>
		<th><?= $matchday_prediction_score_counter; ?></th>
	</tr>
</table>

<?php
require_once '../app/views/footer.php';
?>
