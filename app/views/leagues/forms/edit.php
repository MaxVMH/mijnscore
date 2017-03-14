<?php
require_once '../app/views/header.php';
?>

<form action="leagues_admin/edit/<?= $data['league']['league_id']; ?>" method="post">
	<table>
		<tr>
			<th colspan="2">Bewerk competitie</th>
		</tr>
		<tr>
			<td class="align-right">Competitie naam</td>
			<td class="align-left"><input type="text" name="name" value="<?= $data['league']['league_name']; ?>" size="35" /></td>
		</tr>
		<tr>
			<td class="align-right">Competitie tag</td>
			<td class="align-left"><input type="text" name="tag" value="<?= $data['league']['league_tag']; ?>" size="20" /></td>
		</tr>
		<tr>
			<td class="align-right">Huidige speeldag</td>
			<td class="align-left"><select name="playday_current"><?php
			foreach(range(0,$data['league']['league_playday_total']) as $i)
			{
				if($i == $data['league']['league_playday_current'])
				{
					echo "<option value=\"" . $i . "\" selected=\"selected\">speeldag " .$i . " (huidig)</option>";
				}
				else
				{
					echo "<option value=\"" . $i . "\">speeldag " .$i . "</option>";
				}
			}
			?></select></td>
		</tr>
		<tr>
			<td class="align-right">Totaal # speeldagen</td>
			<td class="align-left"><input type="text" name="playday_total" size="10" value="<?= $data['league']['league_playday_total']; ?>" /></td>
		</tr>
		<tr>
			<td class="align-right">Status</td>
			<td class="align-left">
				<select name="league_status">
					<option value="1"<?php if($data['league']['league_status'] == "1"){ echo " selected=\"selected\""; } ?>>Actief</option>
					<option value="0"<?php if($data['league']['league_status'] == "0"){ echo " selected=\"selected\""; } ?>>Verborgen</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="align-right">Competitie verband</td>
			<td class="align-left">
				<select name="parent_id">
					<option value="NULL">Geen verband</option>
					<?php
					foreach($data['leagues'] as $league)
					{
						?>
						<option value="<?php echo $league['league_id']; ?>"<?php if($data['league']['league_parent_id'] == $league['league_id']){ echo " selected=\"selected\""; } ?>><?php echo $league['league_name']; ?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="edit" value="Bewerk competitie" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
