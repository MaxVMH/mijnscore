<?php
require_once '../app/views/header.php';
?>

<h3>Scorebord <?= $data['score_league']['league_name']; ?>: <a href="predictions/score/<?= $data['score_league']['league_id']; ?>">eindklassement</a> / <a href="predictions/score/<?= $data['score_league']['league_id']; ?>/<?= $data['score_league']['league_playday_current']; ?>">per speeldag</a> / <a href="teams/score/<?= $data['score_league']['league_id']; ?>">ploegen</a></h3>

<table>

	<?php
	if($data['score_playday'] != 0)
	{

		$score_playday_previous = $data['score_playday'] - 1;
		$score_playday_next = $data['score_playday'] + 1;

		if($data['score_playday'] <= 1)
		{
			$score_playday_previous = 1;
		}

		if($data['score_playday'] >= $data['score_league']['league_playday_current'])
		{
			$score_playday_next = $data['score_league']['league_playday_current'];
		}

		if($data['score_playday'] > $data['score_league']['league_playday_current'])
		{
			$score_playday_previous = $data['score_league']['league_playday_current'];
		}
		?>
		<tr>
			<th colspan="3">
				<div style="float: left; text-align: left"><a href="predictions/score/<?= $data['score_league']['league_id']; ?>/<?= $score_playday_previous; ?>" class="align-left">(vorige speeldag)</a></div>
				&nbsp; &nbsp;
				Speeldag <?= $data['score_playday']; ?>
				&nbsp; &nbsp;
				<div style="float: right; text-align: right"><a href="predictions/score/<?= $data['score_league']['league_id']; ?>/<?= $score_playday_next; ?>">(volgende speeldag)</a></div>
			</th>
		</tr>

		<?php
	}
	else
	{
		?>

		<tr>
			<th colspan="3">
				<?= $data['score_league']['league_name']; ?>
			</th>
		</tr>

		<?php
	}
	?>

</tr>
<tr>
	<th>#</th>
	<th>Gebruikersnaam</th>
	<th>Score</th>
</tr>

<?php
foreach($data['users'] as $user)
{
	?>
	<tr>
		<td>
			<?php
			if($user['league_user_ranking'] == 1)
			{
				if($data['score_playday'] == 0)
				{
					echo "<img src=\"img/league_gold.png\" height=\"50%\" />";
				}
				else
				{
					echo "<img src=\"img/playday_gold.png\" height=\"50%\" />";
				}
			}
			elseif($user['league_user_ranking'] == 2)
			{
				if($data['score_playday'] == 0)
				{
					echo "<img src=\"img/league_silver.png\" height=\"40%\" />";
				}
				else
				{
					echo "<img src=\"img/playday_silver.png\" height=\"40%\" />";
				}
			}
			elseif($user['league_user_ranking'] == 3)
			{
				if($data['score_playday'] == 0)
				{
					echo "<img src=\"img/league_bronze.png\" height=\"30%\" />";
				}
				else
				{
					echo "<img src=\"img/playday_bronze.png\" height=\"30%\" />";
				}
			}
			else
			{
				echo $user['league_user_ranking'];
			}

			?>
		</td>
		<td><a href="users/profile/<?= $user['user_id']; ?>"><?= $user['user_username']; ?></a></td>
		<td><a href="predictions/index/<?= $data['score_league']['league_id']; ?>/<?= $data['score_playday']; ?>/<?= $user['user_id']; ?>"><?= $user['points_amount']; ?></a></td>
	</tr>
	<?php
}
?>

</table>

<?php
require_once '../app/views/footer.php';
?>
