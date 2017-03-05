<?php
require_once '../app/views/header.php';
?>

<form action="users/edit/password" method="post">
	<table>
		<tr>
			<th colspan="2">Bewerk wachtwoord</th>
		</tr>
		<tr>
			<td>Huidig wachtwoord</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>Nieuw wachtwoord</td>
			<td><input type="password" name="password_new" /></td>
		</tr>
		<tr>
			<td>Nieuw wachtwoord (herhaal)</td>
			<td><input type="password" name="password_new_repeat" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="edit_password" value="Bewerk wachtwoord" /></th>
		</tr>
	</table>
</form>


<?php
require_once '../app/views/footer.php';
?>
