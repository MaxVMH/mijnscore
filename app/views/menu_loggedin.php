<ul class="topmenu_parent">
	<li class="topmenu_link"><a href="home/index">Home</a></li>
	<li class="topmenu_link dropdown_button">
		<a href="javascript:void(0)">Account</a>
		<div class="dropdown_links">
			<a href="users/profile/<?= $data['user_loggedin']['user_id']; ?>">Mijn profiel</a>
			<a href="users/edit/email">Bewerk e-mail</a>
			<a href="users/edit/password">Bewerk wachtwoord</a>
			<a href="users/search">Zoek gebruiker</a>
		</div>
	</li>
	<li class="topmenu_link dropdown_button">
		<a href="javascript:void(0)">Berichten</a>
		<div class="dropdown_links">
			<a href="messages/inbox">Inkomend</a>
			<a href="messages/outbox">Uitgaand</a>
			<a href="messages_create/form">Nieuw bericht</a>
		</div>
	</li>
	<li class="topmenu_link dropdown_button">
		<a href="javascript:void(0)">Pronostiek</a>
		<div class="dropdown_links">

			<?php
			foreach($data['leagues_current'] as $league)
			{
				?>

				<a href="predictions/index/<?= $league['league_id']; ?>"><?= $league['league_name']; ?></a>

				<?php
			}
			?>

		</div>
	</li>
	<li class="topmenu_link dropdown_button">
		<a href="javascript:void(0)">Scorebord</a>
		<div class="dropdown_links">

			<?php
			foreach($data['leagues_current'] as $league)
			{
				?>

				<a href="predictions/score/<?= $league['league_id']; ?>"><?= $league['league_name']; ?></a>

				<?php
			}
			?>

		</div>
	</li>
	<li class="topmenu_link"><a href="home/faq">FAQ</a></li>
	<li class="topmenu_link"><a href="home/logout">Log uit</a></li>
</ul>

<div class="leftmenu">
	<h3>Menu</h3>
	<a href="users/profile/<?= $data['user_loggedin']['user_id']; ?>"><?= $data['user_loggedin']['user_username']; ?></a><br />
	<a href="messages/inbox">Berichten</a><?php if(!empty($data['user_loggedin']['user_unread_messages'])) echo " (" . $data['user_loggedin']['user_unread_messages'] ." nieuw)"; ?><br />
	<?php
	if($data['user_loggedin']['user_rank'] == "9")
	{
		echo "<br /><a href=\"admin\">Beheer</a><br />";
	}
	?>
</div>
