<?php
require_once '../app/views/header.php';
?>

<form action="matches_admin/create/<?= $data['teams']['0']['league_id']; ?>" method="post">
	<table>
		<tr>
			<th colspan="2">
				Nieuwe wedstrijd
			</th>
		</tr>

		<tr>
			<td>Competitie</td>
			<td><select name="league">
				<?php
				foreach($data['leagues'] as $league)
				{
					echo $data['teams']['0']['league_id'];
					if($league['league_id'] == $data['teams']['0']['league_id'])
					{
						echo "<option value=\"" . $league['league_id'] . "\" selected=\"selected\">" .$league['league_name'] . "</option>";
					}
					else
					{
						echo "<option value=\"" . $league['league_id'] . "\">" .$league['league_name'] . "</option>";
					}
				}
				?>

			</select></td>
		</tr>

		<tr>
			<td>Speeldag</td>
			<td><select name="playday">
				<?php
				foreach(range(1,30) as $i)
				{
					echo "<option value=\"" . $i . "\">Speeldag " .$i . "</option>";
				}
				?>
			</select></td>
		</tr>

		<tr>
			<td>Thuisploeg</td>
			<td><select name="home_team_id">
				<option selected="selected" disabled="disabled">Kies een ploeg</option>
				<?php
				foreach($data['teams'] as $team)
				{
					echo "<option value=\"" . $team['team_id'] . "\">" . $team['team_tag'] . "</option>";
				}
				?>
			</select></td>
		</tr>

		<tr>
			<td>Uitploeg</td>
			<td><select name="away_team_id">
				<option selected="selected" disabled="disabled">Kies een ploeg</option>
				<?php
				foreach($data['teams'] as $team)
				{
					echo "<option value=\"" . $team['team_id'] . "\">" . $team['team_tag'] . "</option>";
				}
				?>
			</select></td>
		</tr>

		<tr>
			<td>Datum</td>
			<td><input type="text" name="datetime" value="2016-08-XX 20:00:00" /></td>
		</tr>

		<tr>
			<td>Info</td>
			<td><input type="text" name="info" /></td>
		</tr>

		<tr>
			<th colspan="2"><input type="submit" name="create" value="Nieuwe wedstrijd" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
