<?php
require_once '../app/views/header.php';
?>

<form action="users/edit/email" method="post">
	<table>
		<tr>
			<th colspan="2">Bewerk e-mailadres</th>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>E-mailadres</td>
			<td><input type="text" name="email" value="<?= $data['user_loggedin']['user_email']; ?>" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="edit_email" value="Bewerk e-mailadres" /></th>
		</tr>
	</table>
</form>


<?php
require_once '../app/views/footer.php';
?>
