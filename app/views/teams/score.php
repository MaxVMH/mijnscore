<?php
require_once '../app/views/header.php';
?>

<h3>Scorebord <?= $data['league']['league_name']; ?>: <a href="predictions/score/<?= $data['league']['league_id']; ?>">eindklassement</a> / <a href="predictions/score/<?= $data['league']['league_id']; ?>/<?= $data['league']['league_matchday_current']; ?>">per speeldag</a> / <a href="teams/score/<?= $data['league']['league_id']; ?>">ploegen</a></h3>

<table>
	<tr>
		<th colspan="5"><?= $data['league']['league_name']; ?></th>
	</tr>
	<tr>
		<th>#</th>
		<th>Ploeg</th>
		<th>Punten</th>
		<th>W/G/V</th>
		<th>DP (+/-)</th>
	</tr>

	<?php
	$standing = 0;
	foreach($data['teams'] as $team)
	{
		$standing++;
		?>

		<tr>
			<td><?= $standing; ?></td>
			<td><a href="teams/single/<?= $team['team_id']; ?>"><?= $team['team_tag']; ?></a></td>
			<td><?= $team['points_amount']; ?></td>
			<td><?= $team['matches_won']; ?>/<?= $team['matches_draw']; ?>/<?= $team['matches_lost']; ?></td>
			<td><?php echo $team['goals_scored'] - $team['goals_allowed']; ?> (<?= $team['goals_scored']; ?>/<?= $team['goals_allowed']; ?>)</td>
		</tr>

		<?php
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
