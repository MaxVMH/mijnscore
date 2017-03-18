<?php
require_once '../app/views/header.php';
?>

<form action="leagues_admin/delete/<?= $data['league']['league_id']; ?>/1" method="post">
	<table>
		<tr>
			<th colspan="2">Verwijder competitie</th>
		</tr>
		<tr>
			<td class="align-right">Competitie naam</td>
			<td class="align-left"><?= $data['league']['league_name']; ?></td>
		</tr>
		<tr>
			<th colspan="2">
				<br />
				<input type="submit" name="delete" value="Verwijder competitie" /><br />
				<br />
				<a href="admin">Ga terug</a>
			</th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
