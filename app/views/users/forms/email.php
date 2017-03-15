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

<p>
	<?php
	if($data['user_loggedin']['user_email_verification'] == 0)
	{
		?>
		Uw e-mail adres werd nog niet bevestigd.<br />
		<br />
		<a href="users/email_verification">Klik hier om uw e-mail adres te bevestigen.</a>
		<?php
	}
	elseif($data['user_loggedin']['user_email_notification'] == 0)
	{
		?>
		<a href="users/email_notification/1">Klik hier om e-mail herinneringen in te schakelen.</a>
		<?php
	}
	elseif($data['user_loggedin']['user_email_notification'] == 1)
	{
		?>
		<a href="users/email_notification/0">Klik hier om e-mail herinneringen uit te schakelen.</a>
		<?php
	}
	?>
</p>

<?php
require_once '../app/views/footer.php';
?>
