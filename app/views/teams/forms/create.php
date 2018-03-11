<?php
require_once '../app/views/header.php';
?>

<form action="teams_admin/create" method="post">
	<table>
		<tr>
			<th colspan="2">Nieuwe ploeg</th>
		</tr>
		<tr class="align-left">
			<td>Ploeg naam</td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr class="align-left">
			<td>Ploeg tag</td>
			<td><input type="text" name="tag" /></td>
		</tr>
    <tr class="align-left">
      <td>Competities</td>
      <td>

        <?php
        foreach($data['leagues'] as $league)
        {
          ?>

          <input type="checkbox" name="league_id[]" value="<?= $league['league_id']; ?>" /> <?= $league['league_name']; ?><br />

          <?php
        }
        ?>

      </td>
    </tr>
		<tr>
			<th colspan="2"><input type="submit" name="create" value="Nieuwe ploeg" /></th>
		</tr>
	</table>
</form>

<?php
require_once '../app/views/footer.php';
?>
