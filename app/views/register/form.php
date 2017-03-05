<?php
require_once '../app/views/header.php';
?>
<h3>Nieuw account</h3>
Eerst en vooral: mijnscore.be is een webdevelopment oefening en daarom geen echte website, game of tool. Omdat mijnscore.be nog volop in ontwikkeling is, kan het zijn dat er hier en daar iets niet werkt.
<h4>Accounts waarvan we denken dat de gebruiker niet deelneemt aan de 9lives pronostiek komen in aanmerking voor verwijdering.</h4>
<form action="register/submit" method="post">
	<table>
		<tr>
			<th colspan=2>Nieuw account</th>
		</tr>
		<tr>
			<td class="align-right">Gebruikersnaam</td>
			<td><input type="text" name="username" size="30" /></td>
		</tr>
		<tr>
			<td class="align-right">E-mailadres</td>
			<td><input type="text" name="email" size="30" /></td>
		</tr>
		<tr>
			<td class="align-right">Wachtwoord</td>
			<td><input type="password" name="password" size="30" /></td>
		</tr>
		<tr>
			<td class="align-right">Wachtwoord (herhaal)</td>
			<td><input type="password" name="password_repeat" size="30" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" name="register" value="Maak nieuw account" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
