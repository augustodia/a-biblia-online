<?php

abstract class BaseModel
{
  protected $db;
  protected $table;
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $hidden = ['created_at', 'updated_at'];
  protected $resultsPerPage = 50;

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }

  public function all()
  {
    $query = $this->db->query("SELECT * FROM {$this->table}");
    return $this->processResults($query->fetchAll());
  }

  public function find($id)
  {
    $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
    $query->execute(['id' => $id]);
    return $this->processResult($query->fetch());
  }

  public function create(array $data)
  {
    $data = $this->filterFillable($data);
    $fields = array_keys($data);
    $placeholders = array_map(function($field) { return ":{$field}"; }, $fields);

    $query = $this->db->prepare(
      "INSERT INTO {$this->table} (" . implode(', ', $fields) . ")
       VALUES (" . implode(', ', $placeholders) . ")"
    );

    $query->execute($data);
    return $this->find($this->db->lastInsertId());
  }

  public function update($id, array $data)
  {
    $data = $this->filterFillable($data);
    $fields = array_map(function($field) { return "{$field} = :{$field}"; }, array_keys($data));
    
    $query = $this->db->prepare(
      "UPDATE {$this->table}
       SET " . implode(', ', $fields) . "
       WHERE {$this->primaryKey} = :id"
    );

    $data['id'] = $id;
    return $query->execute($data);
  }

  public function delete($id)
  {
    $query = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
    return $query->execute(['id' => $id]);
  }

  public function paginate($page = 1, $conditions = '')
  {
    $offset = ($page - 1) * $this->resultsPerPage;
    $where = $conditions ? "WHERE {$conditions}" : '';

    $query = $this->db->prepare(
      "SELECT SQL_CALC_FOUND_ROWS *
       FROM {$this->table}
       {$where}
       LIMIT :limit OFFSET :offset"
    );

    $query->bindValue(':limit', $this->resultsPerPage, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();

    $results = $this->processResults($query->fetchAll());
    $total = $this->db->query('SELECT FOUND_ROWS()')->fetchColumn();

    return [
      'data' => $results,
      'pagination' => [
        'total' => $total,
        'per_page' => $this->resultsPerPage,
        'current_page' => $page,
        'total_pages' => ceil($total / $this->resultsPerPage)
      ]
    ];
  }

  protected function filterFillable(array $data)
  {
    return array_intersect_key($data, array_flip($this->fillable));
  }

  protected function processResult($result)
  {
    if (!$result)  {
      return null;
    }

    foreach ($this->hidden as $field) {
      unset($result[$field]);
    }
    return $result;
  }

  protected function processResults(array $results)
  {
    return array_map([$this, 'processResult'], $results);
  }

  protected function beginTransaction()
  {
    return $this->db->beginTransaction();
  }

  protected function commit()
  {
    return $this->db->commit();
  }

  protected function rollBack()
  {
    return $this->db->rollBack();
  }

  protected function cache($key, $callback, $duration = null)
  {
    if (!CACHE_ENABLED) {
      return $callback();
    }
    
    $duration = $duration ?? CACHE_DURATION;
    $cacheKey = $this->table . '_' . $key;
    
    if (apc_exists($cacheKey)) {
      return apc_fetch($cacheKey);
    }

    $result = $callback();
    apc_store($cacheKey, $result, $duration);
    return $result;
  }
}
