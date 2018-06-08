<?php
require_once '../app/views/header.php';
?>
<h3>Nieuw account</h3>
Eerst en vooral: mijnscore.be is een webdevelopment oefening en daarom geen echte website, game of tool. Omdat mijnscore.be nog volop in ontwikkeling is, kan het zijn dat er hier en daar iets niet werkt.

<br />
<br />

<h3>Welke data houdt mijnscore bij?</h3>
Van bezoekers die niet ingelogd zijn, wordt er geen data bijgehouden en worden er geen cookies geplaatst. <br />
<br />
Van bezoekers die zich registreren/inloggen wordt de volgende data bijgehouden:
<ul>
	<li>Gebruikersnaam en hash van het wachtwoord (om u te kunnen inloggen).</li>
	<li>Er wordt een cookie op uw computer geplaatst met een ID en token (om u te herkennen en ingelogd te houden voor een beperkte tijd).</li>
	<li>E-mail adres (om, indien u daarvoor kiest in uw accountinstellingen, e-mail herinneringen te kunnen sturen).</li>
	<li>Het tijdstip waarop u het laatst inlogde (om inactieve accounts te kunnen verwijderen).</li>
	<li>IP-adres van de verbinding waarmee u registreert (om misbruik tegen te gaan).</li>
	<li>Het tijdstip waarop iemand een fout wachtwoord voor uw account ingeeft, en het aantal keren dat dit voorvalt (om misbruik tegen te gaan).</li>
</ul>
Indien u uw gegevens wilt laten aanpassen of laten verwijderen, stuur dan een berichtje naar Destel op 9lives of mijnscore. <br />
<h4>U zal geen e-mails ontvangen van mijnscore.be, tenzij u daarvoor kiest in Account -> Bewerk e-mailadres.</h4>


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
