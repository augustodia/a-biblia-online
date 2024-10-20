<?php

abstract class BaseModel
{
  protected $db;
  protected $table;
  protected $primaryKey = 'id';

  public function __construct(String $table)
  {
    global $db;
    $this->db = $db;
  }

  public function all()
  {
    $query = $this->db->prepare('SELECT * FROM ' . $this->table);
    $query->execute();
    $versions = $query->fetchAll();
    foreach ($versions as &$version) {
      if (isset($version['sigla'])) {
        $version['sigla'] = strtolower($version['sigla']);
      }
    }

    return $versions;
  }

  public function find($id)
  {
    $query = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id');
    $query->execute(['id' => $id]);
    return $query->fetch();
  }

  public function create($data)
  {
    $fields = array_keys($data);
    $values = array_values($data);

    $query = $this->db->prepare('INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', array_fill(0, count($fields), '?')) . ')');
    $query->execute($values);
    return $this->db->lastInsertId();
  }

  public function update($id, $data)
  {
    $fields = array_keys($data);
    $values = array_values($data);

    $query = $this->db->prepare('UPDATE ' . $this->table . ' SET ' . implode(' = ?, ', $fields) . ' = ? WHERE ' . $this->primaryKey . ' = ?');
    $values[] = $id;
    $query->execute($values);
  }

  public function delete($id)
  {
    $query = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id');
    $query->execute(['id' => $id]);
  }
}
