<?php

class SearchModel extends BaseModel
{
    protected $db;
    protected $table = 'versiculos';
    protected $primaryKey = 'id';

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    public function searchVerses($query, $resultsPerPage, $offset)
    {
        $query = '%' . $query . '%';
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE texto LIKE :query LIMIT :resultsPerPage OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':query', $query, PDO::PARAM_STR);
        $stmt->bindParam(':resultsPerPage', $resultsPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
