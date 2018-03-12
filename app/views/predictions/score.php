<?php
require_once '../app/views/header.php';
?>

<h3>Scorebord <?= $data['score_league']['league_name']; ?>: <a href="predictions/score/<?= $data['score_league']['league_id']; ?>">eindklassement</a> / <a href="predictions/score/<?= $data['score_league']['league_id']; ?>/<?= $data['score_league']['league_matchday_current']; ?>">per speeldag</a> / <a href="teams/score/<?= $data['score_league']['league_id']; ?>">ploegen</a></h3>

<table>

	<?php
	if($data['score_matchday'] != 0)
	{

		$score_matchday_previous = $data['score_matchday'] - 1;
		$score_matchday_next = $data['score_matchday'] + 1;

		if($data['score_matchday'] <= 1)
		{
			$score_matchday_previous = 1;
		}

		if($data['score_matchday'] >= $data['score_league']['league_matchday_current'])
		{
			$score_matchday_next = $data['score_league']['league_matchday_current'];
		}

		if($data['score_matchday'] > $data['score_league']['league_matchday_current'])
		{
			$score_matchday_previous = $data['score_league']['league_matchday_current'];
		}
		?>
		<tr>
			<th colspan="3">
				<div style="float: left; text-align: left"><a href="predictions/score/<?= $data['score_league']['league_id']; ?>/<?= $score_matchday_previous; ?>" class="align-left">(vorige speeldag)</a></div>
				&nbsp; &nbsp;
				Speeldag <?= $data['score_matchday']; ?>
				&nbsp; &nbsp;
				<div style="float: right; text-align: right"><a href="predictions/score/<?= $data['score_league']['league_id']; ?>/<?= $score_matchday_next; ?>">(volgende speeldag)</a></div>
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
				if($data['score_matchday'] == 0)
				{
					echo "<img src=\"img/league_gold.png\" height=\"50%\" />";
				}
				else
				{
					echo "<img src=\"img/matchday_gold.png\" height=\"50%\" />";
				}
			}
			elseif($user['league_user_ranking'] == 2)
			{
				if($data['score_matchday'] == 0)
				{
					echo "<img src=\"img/league_silver.png\" height=\"40%\" />";
				}
				else
				{
					echo "<img src=\"img/matchday_silver.png\" height=\"40%\" />";
				}
			}
			elseif($user['league_user_ranking'] == 3)
			{
				if($data['score_matchday'] == 0)
				{
					echo "<img src=\"img/league_bronze.png\" height=\"30%\" />";
				}
				else
				{
					echo "<img src=\"img/matchday_bronze.png\" height=\"30%\" />";
				}
			}
			else
			{
				echo $user['league_user_ranking'];
			}

			?>
		</td>
		<td><a href="users/profile/<?= $user['user_id']; ?>"><?= $user['user_username']; ?></a></td>
		<td><a href="predictions/index/<?= $data['score_league']['league_id']; ?>/<?= $data['score_matchday']; ?>/<?= $user['user_id']; ?>"><?= (float) $user['points_amount']; ?></a></td>
	</tr>
	<?php
}
?>

</table>

<?php
require_once '../app/views/footer.php';
?>
