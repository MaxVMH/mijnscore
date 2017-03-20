<?php
require_once '../app/views/header.php';
?>

<?php
$match_score = "-";
$match_home_team_score = "N/A";
$match_away_team_score = "N/A";

if($data['match']['match_status']<5)
{
	$match_score = $data['match']['home_team_score'] . " - " .$data['match']['away_team_score'];
	$match_home_score = $data['match']['home_team_score'];
	$match_away_score = $data['match']['away_team_score'];
}
?>

<table>
	<tr>
		<th colspan="2"><?= $data['match']['home_team_tag']; ?> &nbsp; <?= $match_score; ?> &nbsp; <?= $data['match']['away_team_tag']; ?></th>
	</tr>
	<tr>
		<td colspan="2"><?= $data['league']['league_name']; ?> Speeldag <?= $data['match']['league_matchday']; ?></td>
	</tr>
	<tr>
		<td>Thuisploeg</td>
		<td><a href="teams/single/<?= $data['match']['home_team_id']; ?>"><?= $data['match']['home_team_name']; ?></a></td>
	</tr>
	<tr>
		<td>Uitslag</td>
		<td><?= $match_score; ?></td>
	</tr>
	<tr>
		<td>Uitploeg</td>
		<td><a href="teams/single/<?= $data['match']['away_team_id']; ?>"><?= $data['match']['away_team_name']; ?></a></td>
	</tr>
	<tr>
		<td>Datum</td>
		<td><?= $data['match']['match_datetime']; ?></td>
	</tr>
	<tr>
		<td>Info</td>
		<td><?= $data['match']['match_info']; ?></td>
	</tr>
	<?php
	if($data['user_loggedin']['user_rank'] == "9")
	{
		echo "<tr><th colspan=\"2\"><a href=\"matches_admin/edit/" .$data['match']['match_id'] ."\">Bewerk wedstrijd</a></th></tr>";
	}
	?>

</table>

<?php
require_once '../app/views/footer.php';
?>
