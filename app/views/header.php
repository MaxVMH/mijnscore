<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<base href="<?= WEBSITE_URL; ?>" />
	<title><?= WEBSITE_TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="css/default.css" />
</head>
<body>

	<?php
	if(!empty($data['user_loggedin']))
	{
		require_once '../app/views/menu_loggedin.php';
	}
	else
	{
		require_once '../app/views/menu_loggedout.php';
	}

	if(!empty($data['notice']))
	{
		echo "<div class=\"noticemenu\"><h3>" . $data['notice'] . "</h3></div>";
	}
	?>
	<div class="body">
