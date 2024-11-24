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

    public function searchVerses($query, $version)
    {
        $query = '%' . $query . '%';
        $sql = 'SELECT v.*, l.nome AS livro_nome, l.sigla AS livro_sigla, v.capitulo
                FROM versiculos v
                JOIN livros l ON v.livro_id = l.id
                WHERE v.texto LIKE :query
                AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)
                ORDER BY l.nome, v.capitulo, v.versiculo';

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':query', $query);
        $stmt->bindParam(':version', $version);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $groupedResults = [];
        foreach ($results as $result) {
            $book = $result['livro_nome'];
            $chapter = $result['capitulo'];
            if (!isset($groupedResults[$book])) {
                $groupedResults[$book] = [];
            }
            if (!isset($groupedResults[$book][$chapter])) {
                $groupedResults[$book][$chapter] = [];
            }
            $groupedResults[$book][$chapter][] = $result;
        }

        return $groupedResults;
    }
}
