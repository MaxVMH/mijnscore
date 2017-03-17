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
