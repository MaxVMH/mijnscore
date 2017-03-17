<?php
class Mail
{
	public function __construct()
	{
		require '../app/libraries/PHPMailer/PHPMailerAutoload.php';
	}

	public function send_email($receiver, $subject, $body)
	{

		$mail = new PHPMailer();
		$mail->IsSMTP();

		$mail->Host = "smtp.gmail.com";
		$mail->Port = 587;

		$mail->SMTPSecure = "tls";
		$mail->SMTPAuth = true;

		$mail->Username = GMAIL_ADDR;
		$mail->Password = GMAIL_PASS;

		$mail->SetFrom(GMAIL_ADDR);
		$mail->AddAddress($receiver);
		$mail->Subject = $subject;
		$mail->Body = $body;

		$mail->IsHTML (true);

		if($mail->send())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function send_verification_email($receiver_email, $receiver_username, $verification_hash)
	{
		$subject = "Bevestig uw e-mailadres";
		$body = "<h3>E-mail adres bevestigen op mijnscore.be</h3>
		<p>
		Dag " . $receiver_username . ", <br />
		<br />
		Gelieve op <a href='http://mijnscore.be/users/email_verification/" .$verification_hash . "'>deze link</a> te klikken om uw e-mail adres te bevestigen. Indien u deze e-mail niet gevraagd heeft, mag u deze e-mail negeren.<br />
		<br />
		Met vriendelijke groeten, <br />
		mijnscore.be
		</p>";

		if($this->send_email($receiver_email, $subject, $body))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function send_notification_emails($db_con)
	{
		$result = false;
		
		$query = $db_con->prepare('SELECT * FROM leagues WHERE league_status=1');
		$query->execute();
		$active_leagues = $query->fetchAll();
		foreach($active_leagues as $active_league)
		{
			$query = $db_con->prepare('SELECT * FROM matches WHERE league_id=:league_id AND league_playday=:league_playday_current ORDER BY match_datetime ASC LIMIT 1');
			$query->bindValue(':league_id', $active_league['league_id'], PDO::PARAM_STR);
			$query->bindValue(':league_playday_current', $active_league['league_playday_current'], PDO::PARAM_STR);
			$query->execute();
			$league_next_match = $query->fetch();

			$league_next_match_notification_datetime = new DateTime($league_next_match['match_datetime']);
			$league_next_match_notification_datetime->sub(new DateInterval("P1DT4H"));
			$now_datetime = new DateTime();

			if($now_datetime->format('Y-m-d H:i:s') < $league_next_match['match_datetime'] && $league_next_match_notification_datetime->format('Y-m-d H:i:s') < $now_datetime->format('Y-m-d H:i:s') && $active_league['league_last_notification_datetime'] < $league_next_match_notification_datetime->format('Y-m-d H:i:s'))
			{
				$query = $db_con->prepare('UPDATE leagues SET league_last_notification_datetime=NOW() WHERE league_id=:league_id');
				$query->bindValue(':league_id', $active_league['league_id'], PDO::PARAM_STR);
				$query->execute();

				$query = $db_con->prepare('SELECT * FROM users WHERE user_email_notification=1');
				$query->execute();
				$users_email_notifications = $query->fetchAll();
				foreach($users_email_notifications as $user_email_notification)
				{
					$query = $db_con->prepare('
					SELECT
					predictions.*,
					m.league_id as league_id,
					m.match_datetime as match_datetime
					FROM
					predictions
					INNER JOIN
					matches m
					ON
					predictions.match_id = m.match_id AND league_id=:league_id
					WHERE
					user_id=:user_id
					ORDER BY match_datetime DESC
					LIMIT 1');
					$query->bindValue('user_id', $user_email_notification['user_id'], PDO::PARAM_STR);
					$query->bindValue('league_id', $active_league['league_id'], PDO::PARAM_STR);
					$query->execute();
					if($query->fetch())
					{
						$this->send_notification_email($user_email_notification['user_email'], $user_email_notification['user_username']);
						$result = true;
					}
				}
			}
		}

		return $result;
	}

	public function send_notification_email($receiver_email, $receiver_username)
	{
		$subject = "Herinnering van mijnscore.be";
		$body = "<h3>Herinnering van mijnscore.be</h3>
		<p>
		Dag " . $receiver_username . ", <br />
		<br />
		Dit is een herinnering om uw pronostiek in te vullen op <a href='http://mijnscore.be'>mijnscore.be</a>.<br />
		<br />
		Met vriendelijke groeten, <br />
		" . WEBSITE_TITLE . "
		</p>";

		if($this->send_email($receiver_email, $subject, $body))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
