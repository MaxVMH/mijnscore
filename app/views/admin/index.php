<?php
require_once '../app/views/header.php';
?>

<h3>Beheer</h3>

<ul>
  <li><a href="leagues_admin/create">Nieuwe competitie</a></li>
  <li><a href="teams_admin/index">Bekijk alle ploegen</a></li>
</ul>

<?php
foreach($data['leagues'] as $league)
{
  ?>

  <table>
    <tr>
      <th colspan="4"><?= $league['league_name']; ?> (speeldag <?= $league['league_playday_current']; ?> van <?= $league['league_playday_total']; ?>)</th>
    </tr>
    <tr>
      <th>Competitie</th>
      <th>Ploegen</th>
      <th>Wedstrijden</th>
      <th>Pronostieken</th>
    </tr>
    <tr class="align-left">
      <td>
        <ul>
          <li><a href="leagues_admin/edit/<?= $league['league_id']; ?>">Bewerk competitie</a></li>
        </ul>
      </td>
      <td>
        <ul>
          <li><a href="teams/index/<?= $league['league_id']; ?>">Bekijk ploegen</a></li>
          <li><a href="teams_admin/create">Nieuwe ploeg</a></li>
        </ul>
        <ul>
          <li><a href="teams/score/<?= $league['league_id']; ?>">Bekijk punten</a></li>
          <li><a href="admin/set_team_points/<?= $league['league_id']; ?>">Bereken punten</a></li>
        </ul>
      </td>
      <td>
        <ul>
          <li><a href="matches/index/<?= $league['league_id']; ?>">Bekijk wedstrijden</a></li>
          <li><a href="matches_admin/create/<?= $league['league_id']; ?>">Nieuwe wedstrijd</a></li>
        </ul>
        <ul>
          <li><a href="matches_admin/edit_multiple/<?= $league['league_id']; ?>">Geef scores in</a></li>
        </ul>
      </td>
      <td>
        <ul>
          <li><a href="predictions/score/<?= $league['league_id']; ?>">Bekijk punten</a></li>
          <li><a href="admin/set_prediction_points/<?= $league['league_id']; ?>">Bereken punten</a></li>
        </ul>
      </td>
    </tr>
  </table>
  <br />

  <?php
}
?>

<?php
require_once '../app/views/footer.php';
?>
