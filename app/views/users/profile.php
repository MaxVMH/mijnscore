<?php
require_once '../app/views/header.php';
?>

<h3>Profiel van <?= $data['profile']['user_username']; ?></h3>

<table>
  <tr>
    <th colspan="2"><?= $data['profile']['user_username']; ?></th>
  </tr>
  <tr>
    <td class="align-right">Lid sinds</td>
    <td class="align-left"><?= $data['profile']['user_registration_datetime']; ?></td>
  </tr>
  <tr>
    <td class="align-right">Laatst online</td>
    <td class="align-left"><?= $data['profile']['user_lastlogin_datetime']; ?></td>
  </tr>
  <tr>
    <td class="align-right">Pronostiek</td>
    <td class="align-left">

      <?php
      foreach($data['prediction_points'] as $points)
      {
        ?>
        <?= $points['league_user_ranking']; ?>e in <a href="predictions/score/<?= $points['league_id']; ?>"><?= $points['league_name']; ?></a><br />

        <a href="predictions/index/<?= $points['league_id']; ?>/<?= $points['league_matchday_current']; ?>/<?= $data['profile']['user_id']; ?>">Bekijk pronostiek</a><br />

        <?php
      }
      ?>

    </td>
    <tr>
      <td colspan="2"><a href="messages_create/form/0/<?= $data['profile']['user_id']; ?>">Verzend bericht</a></td>
    </tr>
  </tr>
</table>

<?php
require_once '../app/views/footer.php';
?>
