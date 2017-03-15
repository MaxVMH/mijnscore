<?php
class Mail
{
	public function send_mail($receiver, $subject, $body)
	{
		require '../app/libraries/PHPMailer/PHPMailerAutoload.php';

		$mail = new PHPMailer();
		$mail->IsSMTP();

		$mail->Debugoutput = "html";

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

		if(!$mail->send()) {
			echo 'Message was not sent.';
			echo 'Mailer error: ' . $mail->ErrorInfo;
			return false;
		} else {
			return true;
		}
	}
}
