<?php
require_once '../app/views/header.php';

$match_league_playday_previous = $data['matches']['0']['league_playday'] - 1;
$match_league_playday_next = $data['matches']['0']['league_playday'] + 1;

if($data['matches']['0']['league_playday'] == "1")
{
	$match_league_playday_previous = $data['matches']['0']['league_playday'];
}

if($data['matches']['0']['league_playday'] > $data['league']['league_playday_current'] + 2)
{
	$match_league_playday_next = $data['matches']['0']['league_playday'];
}

if($data['matches']['0']['league_playday'] >= $data['league']['league_playday_total'])
{
	$match_league_playday_next = $data['matches']['0']['league_playday'];
}

?>

<form action="predictions/edit/<?= $data['matches']['0']['league_id']; ?>/<?= $data['matches']['0']['league_playday']; ?>" method="post">

	<h3>Mijn pronostiek: <?= $data['league']['league_name']; ?></h3>
	<table>
		<tr>
			<th colspan="6">
				<div style="float: left; text-align: left"><a href="predictions/index/<?= $data['league']['league_id']; ?>/<?= $match_league_playday_previous; ?>" class="align-left">(vorige speeldag)</a></div>
				Speeldag <?= $data['matches']['0']['league_playday']; ?>
				<div style="float: right; text-align: right"><a href="predictions/index/<?= $data['league']['league_id']; ?>/<?= $match_league_playday_next; ?>">(volgende speeldag)</a></div>
			</th>
		</tr>
		<tr>
			<th>Datum</th>
			<th class="align-right">Thuisploeg</th>
			<th>Pronostiek</th>

			<?php
			if($data['matches']['0']['match_status'] < 5)
			{
				?>
				<th>Uitslag</th>
				<?php
			}
			?>

			<th class="align-left">Uitploeg</th>
			<th>Punten</th>
		</tr>

		<?php
		foreach($data['matches'] as $match)
		{
			?>
			<tr>
				<td><?= strftime("%a %e %b %G %H:%M", strtotime($match['match_datetime'])); ?></td>
				<td class="align-right"><a href="teams/single/<?= $match['home_team_id']; ?>"><?= $match['home_team_tag']; ?></a></td>

				<?php
				if($match['match_status'] == 6)
				{
					?>

					<td>
						<input type="hidden" name="prediction_id[]" value="<?= $match['prediction_id']; ?>" />
						<input type="text" name="prediction_home_team_score[]" value="<?= $match['prediction_home_team_score']; ?>" size="2" tabindex="1" />
						&nbsp; - &nbsp;
						<input type="text" name="prediction_away_team_score[]" value="<?= $match['prediction_away_team_score']; ?>" size="2" tabindex="1" />
					</td>

					<?php
				}
				else
				{
					?>

					<td><?= $match['prediction_home_team_score']; ?>&nbsp; - &nbsp;<?= $match['prediction_away_team_score']; ?></td>

					<?php
				}

				$match_score = "-";
				if($data['matches']['0']['match_status'] < 5)
				{

					if($match['match_status'] < 5)
					{
						$match_score = $match['home_team_score'] . "&nbsp; - &nbsp;" . $match['away_team_score'];
					}
					?>

					<td><?= $match_score; ?></td>

					<?php
				}
				?>

				<td class="align-left"><a href="teams/single/<?= $match['away_team_id']; ?>"><?= $match['away_team_tag']; ?></a></td>

				<?php
				$prediction_points = "";
				if($match['match_status'] <= 4 && $match['prediction_id'] != null)
				{
					$prediction_points = $this->prediction_points->get_prediction_points_by_scores($match['home_team_score'], $match['away_team_score'], $match['prediction_home_team_score'], $match['prediction_away_team_score']);
				}
				?>

				<td><?= $prediction_points; ?></td>
			</tr>

			<?php
		}
		?>
		<tr>
			<th colspan="6"><input type="submit" name="edit" value="Bewerk pronostiek" tabindex="1" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
