<?php
require_once '../app/views/header.php';
?>

<table>
	<tr>
		<th>Van</th>
		<th>Naar</th>
		<th>Datum</th>
		<th class="td_wider">Onderwerp</th>
	</tr>

	<tr>
		<td><a href="users/profile/<?= $data['message']['message_sender_user_id']; ?>"><?= $data['message']['message_sender_username']; ?></a></td>
		<td><a href="users/profile/<?= $data['message']['message_receiver_user_id']; ?>"><?= $data['message']['message_receiver_username']; ?></a></td>
		<td><?= $data['message']['message_datetime']; ?></td>
		<td><?= $data['message']['message_title']; ?></td>
	</tr>
	<tr>
		<td colspan="4"><p><?= $data['message']['message_text']; ?></p></td>
	</tr>
	<tr>
		<th colspan="4">
			<a href="messages_create/form/<?= $data['message']['message_id']; ?>">Beantwoord bericht</a>
		</th>
	</tr>
	<tr>
		<th colspan="4">
			<a href="messages/delete/<?= $data['message']['message_id']; ?>">Verwijder bericht</a>
		</th>
	</tr>
</table>

<?php
require_once '../app/views/footer.php';
?>
