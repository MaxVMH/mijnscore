<?php
require_once '../app/views/header.php';

$match_league_matchday_previous = $data['matches']['0']['league_matchday'] - 1;
$match_league_matchday_next = $data['matches']['0']['league_matchday'] + 1;

if($data['matches']['0']['league_matchday'] == "1")
{
	$match_league_matchday_previous = $data['matches']['0']['league_matchday'];
}

if($data['matches']['0']['league_matchday'] > $data['league']['league_matchday_current'] + 2)
{
	$match_league_matchday_next = $data['matches']['0']['league_matchday'];
}

if($data['matches']['0']['league_matchday'] >= $data['league']['league_matchday_total'])
{
	$match_league_matchday_next = $data['matches']['0']['league_matchday'];
}
?>

<h3>Bewerk wedstrijden van <?= $data['league']['league_name']; ?></h3>

<form action="matches_admin/edit_multiple/<?= $data['league']['league_id']; ?>/<?= $data['matches']['0']['league_matchday']; ?>" method="post">
	<table>
		<tr>
			<th colspan="7">
				<div style="float: left; text-align: left"><a href="matches_admin/edit_multiple/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_previous; ?>" class="align-left">(vorige speeldag)</a></div>
				Speeldag <?= $data['matches']['0']['league_matchday']; ?>
				<div style="float: right; text-align: right"><a href="matches_admin/edit_multiple/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_next; ?>">(volgende speeldag)</a></div>
			</th>
		</tr>
		<tr>
			<th>Datum</th>
			<th>Thuisploeg</th>
			<th>Uitslag</th>
			<th>Uitploeg</th>
			<th>Info</th>
			<th>Lock</th>
			<th>Score</th>
		</tr>

		<?php
		foreach($data['matches'] as $match)
		{
			?>
			<tr>
				<td><?= $match['match_datetime']; ?></td>
				<td>
					<?= $match['home_team_tag']; ?>
				</td>
				<td>
					<input type="hidden" name="match_id[<?= $match['match_id']; ?>]" value="<?= $match['match_id']; ?>" />
					<input type="text" name="home_team_score[<?= $match['match_id']; ?>]" value="<?= $match['home_team_score'] ?>" size="2" tabindex="1" />
					-
					<input type="text" name="away_team_score[<?= $match['match_id']; ?>]" value="<?= $match['away_team_score'] ?>" size="2" tabindex="1" />
				</td>
				<td>
					<?= $match['away_team_tag']; ?>
				</td>
				<td><a href="matches_admin/edit/<?= $match['match_id']; ?>">Bewerk</a></td>
				<td><input type="checkbox" name="match_lock[<?= $match['match_id']; ?>]" value="yes" <?php if($match['match_status'] < 6){ echo "checked=\"checked\""; } ?> /></td>
				<td><input type="checkbox" name="match_score[<?= $match['match_id']; ?>]" value="yes" <?php if($match['match_status'] < 5){ echo "checked=\"checked\""; } ?> /></td>
			</tr>
			<?php
		}
		?>

		<tr>
			<th colspan="7"><input type="submit" name="edit_multiple" value="Bewerk wedstrijden" tabindex="1" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
