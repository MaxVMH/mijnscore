<?php
require_once '../app/views/header.php';
?>

<h3>Ploeg: <?= $data['team']['team_name']; ?></h3>
<table>
	<tr>
		<th colspan="2">
			<?= $data['team']['team_name']; ?>
		</th>
	</tr>
	<tr>
		<td>Naam</td>
		<td><?= $data['team']['team_name']; ?></td>
	</tr>
	<tr>
		<td>Tag</td>
		<td><?= $data['team']['team_tag']; ?></td>
	</tr>

	<?php
	if($data['user_loggedin']['user_rank'] == "9")
	{
		?>
		<tr>
			<th colspan="2">
				<a href="teams_admin/edit/<?= $data['team']['team_id']; ?>">Bewerk ploeg</a>
			</th>
		</tr>
		<?php
	}
	?>

</table>

<br />
<h3>Wedstrijden</h3>

<table>
	<?php
	foreach($data['leagues'] as $team_league)
	{
		?>

		<tr>
			<th colspan="5">
				<a href="teams/matches/<?= $data['team']['team_id']; ?>/<?= $team_league['league_id']; ?>"><?= $data['team']['team_tag']; ?> in de <?= $team_league['league_name']; ?></a>
			</th>
		</tr>
		<tr>
			<th>Datum</th>
			<th class="align-right">Thuisploeg</th>
			<th>Prono</th>
			<th>Uitslag</th>
			<th class="align-left">Uitploeg</th>
		</tr>

		<?php
		foreach($team_league['matches'] as $match)
		{
			?>

			<tr>
				<td><?= strftime("%a %e %b %G %H:%M", strtotime($match['match_datetime'])); ?></td>
				<td class="align-right"><a href="teams/single/<?= $match['home_team_id']; ?>"><?= $match['home_team_tag']; ?></a></td>

				<?php
				if(isset($match['prediction_id']))
				{
					?>

					<td><?= $match['prediction_home_team_score']; ?> - <?= $match['prediction_away_team_score']; ?></td>

					<?php
				}
				else
				{
					?>

					<td> - </td>

					<?php
				}
				?>

				<?php
				if($match['match_status'] <= 4)
				{
					?>

					<td><?= $match['home_team_score']; ?> - <?= $match['away_team_score']; ?></td>

					<?php
				}
				else
				{
					?>

					<td> - </td>

					<?php
				}
				?>

				<td class="align-left"><a href="teams/single/<?= $match['away_team_id']; ?>"><?= $match['away_team_tag']; ?></a></td>
			</tr>

			<?php
		}
		?>

		<?php
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
