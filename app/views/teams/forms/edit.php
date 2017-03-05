<?php
require_once '../app/views/header.php';
?>

<form action="teams_admin/edit/<?= $data['team']['team_id']; ?>" method="post">
  <table>
    <tr>
      <th colspan="2">Bewerk ploeg</th>
    </tr>
    <tr class="align-left">
      <td>Ploeg naam</td>
      <td><input type="text" name="name" value="<?= $data['team']['team_name']; ?>" size="40" /></td>
    </tr>
    <tr class="align-left">
      <td>Ploeg tag</td>
      <td><input type="text" name="tag" value="<?= $data['team']['team_tag']; ?>" size="10" /></td>
    </tr>
    <tr class="align-left">
      <td>Competities</td>
      <td>

        <?php
        foreach($data['leagues'] as $league)
        {
          ?>

          <input type="checkbox" name="league_id[]" value="<?= $league['league_id']; ?>" <?php if($league['teams_leagues'] != null){ echo "checked=\"checked\" "; } ?>/> <?= $league['league_name']; ?><br />

          <?php
        }
        ?>

      </td>
    </tr>
    <tr>
      <th colspan="2"><input type="submit" name="edit" value="Bewerk ploeg" /></th>
    </tr>
  </table>
</form>

<?php
require_once '../app/views/footer.php';
?>
