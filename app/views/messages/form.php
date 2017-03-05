<?php
require_once '../app/views/header.php';
?>

<h3>Verzend een nieuw bericht</h3>

<form action="messages_create/submit" method="post">
	<table>
		<tr>
			<th colspan="2">
				Nieuw bericht
			</th>
		</tr>
		<tr>
			<td>Onderwerp</td>
			<td class="align-left"><input type="text" name="title" value="<?php if(!empty($data['message_title'])) echo $data['message_title']; ?>" size="50" /></td>
		</tr>
		<tr>
			<td>Naar</td>
			<td class="align-left"><input type="text" name="receiver_username" value="<?php if(!empty($data['message_receiver_username'])) echo $data['message_receiver_username']; ?>" size="50" /></td>
		</tr>
		<tr>
			<td>Bericht</td>
			<td class="align-left"><textarea name="text" rows="5" cols="50"></textarea></td>
		</tr>
		<tr>
			<th colspan="2">
				<input type="submit" name="send" value="Nieuw bericht" />
			</th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
