<?php
require_once '../app/views/header.php';
?>

<table>
	<tr>
		<th>ID</th>
		<th>Ploeg (tag)</th>
		<th>Naam</th>

		<?php
		if($data['user_loggedin']['user_rank'] == "9")
		{
			?>

			<th>Bewerk</th>

			<?php
		}
		?>

	</tr>

	<?php
	foreach($data['teams'] as $team)
	{
		?>

		<tr>
			<td><?= $team['team_id']; ?></td>
			<td><a href="teams/single/<?= $team['team_id']; ?>"><?= $team['team_tag']; ?></a></td>
			<td><?= $team['team_name']; ?></td>

			<?php
			if($data['user_loggedin']['user_rank'] == "9")
			{
				?>

				<td><a href="teams_admin/edit/<?= $data['team']['team_id']; ?>">Bewerk ploeg</a></td>

				<?php
			}
			?>

		</tr>

		<?php
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
