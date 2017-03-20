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

if($data['matches']['0']['match_status'] < 5)
{
  $predictions_tables_colspan = 6;
}
else
{
  $predictions_tables_colspan = 5;
}
?>

<form action="predictions/create/<?= $data['matches']['0']['league_id']; ?>/<?= $data['matches']['0']['league_matchday']; ?>" method="post">

  <h3>Mijn pronostiek: <?= $data['league']['league_name']; ?></h3>
  <table>
    <tr>
      <th colspan="<?= $predictions_tables_colspan; ?>">
        <span style="float: left; text-align: left"><a href="predictions/index/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_previous; ?>" class="align-left">(vorige speeldag)</a></span>
        Speeldag <?= $data['matches']['0']['league_matchday']; ?>
        <span style="float: right; text-align: right"><a href="predictions/index/<?= $data['league']['league_id']; ?>/<?= $match_league_matchday_next; ?>">(volgende speeldag)</a></span>
      </th>
    </tr>
    <tr>
      <th>Datum</th>
      <th class="align-right">Thuisploeg</th>
      <th>Pronostiek</th>

      <?php
      if($predictions_tables_colspan == 6)
      {
        ?>

        <th>Uitslag</th>

        <?php
      }
      ?>

      <th class="align-left">Uitploeg</th>
      <th>Punten</th>
    </tr>

    <?php
    foreach($data['matches'] as $match)
    {
      ?>

      <tr>
        <td><?= strftime("%a %e %b %G %H:%M", strtotime($match['match_datetime'])); ?></td>
        <td class="align-right"><a href="teams/single/<?= $match['home_team_id']; ?>"><?= $match['home_team_tag']; ?></a></td>

        <?php
        if($match['match_status'] == 6)
        {
          ?>

          <td>
            <input type="hidden" name="prediction_match_id[]" value="<?= $match['match_id']; ?>" />
            <input type="text" name="prediction_home_team_score[]" value="" size="2" tabindex="1" />
            &nbsp; - &nbsp;
            <input type="text" name="prediction_away_team_score[]" value="" size="2" tabindex="1" />
          </td>

          <?php
        }
        else
        {
          ?>

          <td> - </td>

          <?php
        }

        if($predictions_tables_colspan == 6)
        {
          $match_score = "-";
          if($match['match_status'] < 5)
          {
            $match_score = $match['home_team_score'] . "&nbsp; - &nbsp" . $match['away_team_score'];
          }
          ?>

          <td><?= $match_score; ?></td>

          <?php
        }
        ?>
        <td class="align-left"><a href="teams/single/<?= $match['away_team_id']; ?>"><?= $match['away_team_tag']; ?></a></td>
        <td></td>
      </tr>

      <?php
    }
    ?>

    <tr>
      <th colspan="<?= $predictions_tables_colspan; ?>"><input type="submit" name="create" value="Nieuwe pronostiek" tabindex="1" /></th>
    </tr>
  </table>
</form>

<?php
require_once '../app/views/footer.php';
?>
