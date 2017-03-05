<?php
require_once '../app/views/header.php';
?>
<h3>Berichten: <?= $data['messages_box']; ?></h3>
<table>
	<tr>
		<th>Van</th>
		<th>Naar</th>
		<th>Datum</th>
		<th class="td_wider">Onderwerp</th>
		<th>Status</th>
	</tr>

	<?php
	// We create a loop for the messages
	foreach ($data['messages'] as $message)
	{
		// Convert message status to readable stuff
		if($message['message_receiver_status']==2)
		{
			$message_status = "unread";
		}
		elseif($message['message_receiver_status']==1)
		{
			$message_status = "read";
		}
		elseif($message['message_receiver_status']==0)
		{
			$message_status = "deleted";
		}
		?>

		<tr>
			<td><a href="users/profile/<?= $message['message_sender_user_id']; ?>"><?= $message['message_sender_username']; ?></a></td>
			<td><a href="users/profile/<?= $message['message_receiver_user_id']; ?>"><?= $message['message_receiver_username']; ?></a></td>
			<td><?= $message['message_datetime']; ?></td>
			<td><a href="messages/single/<?= $message['message_id']; ?>"><?= $message['message_title']; ?></a></td>
			<td><?= $message_status; ?></td>
		</tr>

		<?php
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
