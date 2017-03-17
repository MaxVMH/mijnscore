<?php
require_once '../app/views/header.php';
?>

<h3>Voeg nieuwe wedstrijden toe</h3>
<form action="matches_admin/create_multiple/<?= $data['league']['league_id']; ?>/<?= $data['playday']; ?>" method="post">
	<table>
		<tr>
				<th colspan="3">
					Speeldag <?= $data['playday']; ?> van <?= $data['league']['league_name']; ?>
				</th>
		</tr>
		<tr>
			<th>Thuisploeg</th>
			<th>Uitploeg</th>
			<th>Datum</th>
		</tr>
		<?php
		$amount_of_matches = count($data['teams']) / 2;
		for($i = 1; $i <=$amount_of_matches; $i++)
		{
			?>
			<tr>
				<td>
					<select name="home_team_id[]">
						<option selected="selected" disabled="disabled">Kies een ploeg</option>
						<?php
						foreach($data['teams'] as $team)
						{
							echo "<option value=\"" . $team['team_id'] . "\">" . $team['team_tag'] . "</option>";
						}
						?>
					</select>
				</td>
				<td>
					<select name="away_team_id[]">
						<option selected="selected" disabled="disabled">Kies een ploeg</option>
						<?php
						foreach($data['teams'] as $team)
						{
							echo "<option value=\"" . $team['team_id'] . "\">" . $team['team_tag'] . "</option>";
						}
						?>
					</select>
				</td>
				<td><input type="text" name="datetime[]" value="JJJJ-MM-DD UU:MM:SS" /></td>
			</tr>
			<?php
		}
		?>
		<tr>
			<th colspan="3"><input type="submit" name="create_multiple" value="Voeg toe" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
