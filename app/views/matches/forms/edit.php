<?php
require_once '../app/views/header.php';
?>

<form action="matches_admin/edit/<?= $data['match']['match_id']; ?>" method="post">
	<table>
		<tr>
			<th colspan="2">Bewerk wedstrijd</th>
		</tr>
		<tr>
			<td>Competitie</td>
			<td>
				<select name="league">

					<?php
					foreach($data['leagues'] as $league)
					{
						if($league['league_id'] == $data['match']['league_id'])
						{
							echo "<option value=\"" . $league['league_id'] . "\" selected=\"selected\">" .$league['league_name'] . "</option>";
						}
						else
						{
							echo "<option value=\"" . $league['league_id'] . "\">" .$league['league_name'] . "</option>";
						}

					}
					?>

				</select>
			</td>
		</tr>
		<tr>
			<td>Speeldag</td>
			<td>
				<select name="matchday">
					
					<?php
					foreach(range(1,30) as $i)
					{
						if($data['match']['league_matchday'] == $i)
						{
							echo "<option value=\"" . $i . "\" selected=\"selected\">Speeldag " .$i . "</option>";
						}
						else
						{
							echo "<option value=\"" . $i . "\">Speeldag " .$i . "</option>";
						}
					}
					?>

				</select>
			</td>
		</tr>
		<tr>
			<td>Wedstrijd status</td>
			<td>
				<select name="status">
					<option value="6"<?php if($data['match']['match_status'] == "6"){ echo " selected=\"selected\""; } ?>>Open zonder score</option>
					<option value="5"<?php if($data['match']['match_status'] == "5"){ echo " selected=\"selected\""; } ?>>Gesloten zonder score</option>
					<option value="4"<?php if($data['match']['match_status'] == "4"){ echo " selected=\"selected\""; } ?>>Gesloten met score</option>
					<?php if($data['match']['match_status'] == "3"){ echo "<option value=\"3\" selected=\"selected\">Gesloten met score en punten</option>"; } ?>
					<option value="1"<?php if($data['match']['match_status'] == "1"){ echo " selected=\"selected\""; } ?>>Verborgen</option>
					<option value="0"<?php if($data['match']['match_status'] == "0"){ echo " selected=\"selected\""; } ?>>Verwijderen</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Thuisploeg</td>
			<td>
				<select name="home_team_id">
					<?php
					foreach($data['teams'] as $team)
					{
						if($team['team_id'] == $data['match']['home_team_id'])
						{
							echo "<option value=\"" . $team['team_id'] . "\" selected=\"selected\">" . $team['team_tag'] . "</option>";
						}
						else
						{
							echo "<option value=\"" . $team['team_id'] . "\">" . $team['team_tag'] . "</option>";
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Uitslag</td>
			<td>
				<input type="text" name="home_team_score" value="<?=$data['match']['home_team_score'] ?>" size="2" />
				-
				<input type="text" name="away_team_score" value="<?=$data['match']['away_team_score'] ?>" size="2" />
			</td>
		</tr>
		<tr>
			<td>Uitploeg</td>
			<td>
				<select name="away_team_id">
					<?php
					foreach($data['teams'] as $team)
					{
						if($team['team_id'] == $data['match']['away_team_id'])
						{
							echo "<option value=\"" . $team['team_id'] . "\" selected=\"selected\">" . $team['team_tag'] . "</option>";
						}
						else
						{
							echo "<option value=\"" . $team['team_id'] . "\">" . $team['team_tag'] . "</option>";
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Datum</td>
			<td><input type="text" name="datetime" value="<?= $data['match']['match_datetime']; ?>" /></td>
		</tr>
		<tr>
			<td>Info</td>
			<td><input type="text" name="info" value="<?= $data['match']['match_info']; ?>" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="edit" value="Bewerk wedstrijd" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
