<?php
  require_once '../app/views/header.php';
?>

<form action="users/profile" method="post" name="user_edit_form">
	<table>
		<tr>
			<th colspan="2">Zoek gebruiker</th>
		</tr>
		<tr>
			<td>Naam</td>
			<td><input type="text" name="username" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="search" value="Zoek gebruiker" /></th>
		</tr>
	</table>
</form>

<?php
  require_once '../app/views/footer.php';
?>
