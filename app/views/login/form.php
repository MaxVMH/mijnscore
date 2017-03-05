<?php
require_once '../app/views/header.php';
?>

<form method="post" action="login/submit">
	<table>
		<tr>
			<th colspan="2">Log in</th>
		</tr>
		<tr>
			<td>Gebruikersnaam</td>
			<td><input type="text" name="username" /></td>
		</tr>
		<tr>
			<td>Wachtwoord</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="login" value="Log in" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
