<?php
require_once '../app/views/header.php';
?>

<table>
	<tr>
		<th>ID</th>
		<th>Ploeg (tag)</th>
		<th>Naam</th>
	</tr>

	<?php
	foreach($data['teams'] as $team)
	{
		?>

		<tr>
			<td><?= $team['team_id']; ?></td>
			<td><a href="teams/single/<?= $team['team_id']; ?>"><?= $team['team_tag']; ?></a></td>
			<td><?= $team['team_name']; ?></td>
		</tr>

		<?php
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
