<?php
require_once '../app/views/header.php';
?>

<?php

$match_league_matchday_previous = $data['matches']['0']['league_matchday'] - 1;
$match_league_matchday_next = $data['matches']['0']['league_matchday'] + 1;

if($data['matches']['0']['league_matchday'] == "1")
{
	$match_league_matchday_previous = $data['matches']['0']['league_matchday'];
}

if($data['matches']['0']['league_matchday'] > $data['league']['league_matchday_current'] + 2)
{
	$match_league_matchday_next = $data['matches']['0']['league_matchday'];
}

if($data['matches']['0']['league_matchday'] >= $data['league']['league_matchday_total'])
{
	$match_league_matchday_next = $data['matches']['0']['league_matchday'];
}

?>

<h3><?= $data['league']['league_name']; ?></h3>
<table>
	<tr>
		<th colspan="6">
			<div style="float: left; text-align: left"><a href="matches/index/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_previous; ?>" class="align-left">(vorige speeldag)</a></div>
			Speeldag <?= $data['matches']['0']['league_matchday']; ?>
			<div style="float: right; text-align: right"><a href="matches/index/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_next; ?>">(volgende speeldag)</a></div>
		</th>
	</tr>

	<tr>
		<th>Datum</th>
		<th class="align-right">Thuisploeg</th>
		<th>Uitslag</th>
		<th class="align-left">Uitploeg</th>
		<th>Info</th>
	</tr>

	<?php
	$matchday_prediction_score_counter = "0";

	foreach($data['matches'] as $match)
	{
		?>

		<tr>
			<td><?= strftime("%a %e %b %G %H:%M", strtotime($match['match_datetime'])); ?></td>
			<td class="align-right"><a href="teams/single/<?= $match['home_team_id']; ?>"><?= $match['home_team_tag']; ?></a></td>

			<?php
			$match_score = "-";
			if ($match['match_status'] <= 4)
			{
				$match_score = $match['home_team_score'] . " - " . $match['away_team_score'];
			}
			?>

			<td><?= $match_score; ?></td>
			<td class="align-left"><a href="teams/single/<?= $match['away_team_id']; ?>"><?= $match['away_team_tag']; ?></a></td>

			<td>

				<?php
				if($data['user_loggedin']['user_rank'] == "9")
				{
					?>

					<a href="matches_admin/edit/<?= $match['match_id']; ?>">Bewerk wedstrijd</a>

					<?php
				}
				else
				{
					?>

					<a href="matches/single/<?= $match['match_id']; ?>">Info</a>

					<?php
				}
				?>
			</td>
		</tr>

		<?php
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
