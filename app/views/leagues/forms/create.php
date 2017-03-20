<?php
require_once '../app/views/header.php';
?>

<form action="leagues_admin/create" method="post">
	<table>
		<tr>
			<th colspan="2">Nieuwe competitie</th>
		</tr>
		<tr>
			<td>Competitie naam</td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td>Competitie tag</td>
			<td><input type="text" name="tag" /></td>
		</tr>
		<tr>
			<td>Total # speeldagen</td>
			<td><input type="text" name="matchday_total" /></td>
		</tr>
		<tr>
			<td>Competitie verband</td>
			<td>
				<select name="parent_id">
					<option value="NULL">Geen verband</option>
					<?php
					foreach($data['leagues'] as $league)
					{
						?>
						<option value="<?php echo $league['league_id']; ?>"><?php echo $league['league_name']; ?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="create" value="Nieuwe competitie" /></th>
		</tr>
	</table>
</form>


<?php
require_once '../app/views/footer.php';
?>
