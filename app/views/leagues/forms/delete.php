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
			<th colspan="2"><input type="submit" name="edit" value="Verwijder competitie" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
