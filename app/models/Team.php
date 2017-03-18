<?php
class Team
{
  public function create_team($db_con, $name, $tag)
  {
    $result = false;
    $query = $db_con->prepare('INSERT INTO teams(team_name, team_tag) VALUES(:name, :tag)');
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':tag', $tag, PDO::PARAM_STR);
    if($query->execute())
    {
      $result = $db_con->lastInsertID();
    }
    return $result;
  }

  public function get_team_by_id($db_con, $id)
  {
    $query = $db_con->prepare('SELECT * FROM teams WHERE team_id=:id');
    $query->bindValue(':id', $id, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch();
  }

  public function get_teams_all($db_con)
  {
    $query = $db_con->prepare('SELECT * FROM teams');
    $query->execute();
    return $query->fetchAll();
  }

  public function get_teams_all_ordered_by_team_tag_asc($db_con)
  {
    $query = $db_con->prepare('SELECT * FROM teams ORDER BY team_tag ASC');
    $query->execute();
    return $query->fetchAll();
  }

  public function edit_team($db_con, $id, $name, $tag)
  {
    $query = $db_con->prepare('UPDATE teams SET team_name=:name, team_tag=:tag WHERE team_id=:id');
    $query->bindValue('name', $name, PDO::PARAM_STR);
    $query->bindValue('tag', $tag, PDO::PARAM_STR);
    $query->bindValue('id', $id, PDO::PARAM_STR);
    return $query->execute();
  }
}
?>
