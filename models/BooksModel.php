<?php
class BooksModel extends BaseModel
{
  protected $db;
  protected $table = 'livros';
  protected $primaryKey = 'id';

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }

  public function all()
  {
    $sql = "SELECT * FROM {$this->table} ORDER BY id";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
  }

  public function getBook($acronym)
  {
    // Busca informações básicas do livro
    $sql = "SELECT l.*, 
            (SELECT COUNT(DISTINCT v.capitulo) FROM versiculos v WHERE v.livro_id = l.id) as total_capitulos,
            (SELECT GROUP_CONCAT(DISTINCT v.capitulo ORDER BY v.capitulo) FROM versiculos v WHERE v.livro_id = l.id) as capitulos
            FROM {$this->table} l 
            WHERE l.sigla = :acronym 
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['acronym' => $acronym]);
    $book = $stmt->fetch();

    if (!$book) {
        return null;
    }

    // Converte a string de capítulos em um array
    $book['capitulos'] = explode(',', $book['capitulos']);

    // Busca o total de versículos por capítulo
    $sql = "SELECT v.capitulo, COUNT(*) as total
            FROM versiculos v
            INNER JOIN livros l ON l.id = v.livro_id
            WHERE l.sigla = :acronym
            GROUP BY v.capitulo
            ORDER BY v.capitulo";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['acronym' => $acronym]);
    $versiculosPorCapitulo = $stmt->fetchAll();

    // Cria um array com o total de versículos por capítulo
    $book['versiculos'] = [];
    foreach ($versiculosPorCapitulo as $capitulo) {
        $book['versiculos'][$capitulo['capitulo'] - 1] = (int)$capitulo['total'];
    }

    return $book;
  }

  public function getChapterVerses($bookAcronym, $chapter)
  {
    $sql = "SELECT COUNT(*) as total FROM versiculos v 
            INNER JOIN livros l ON v.livro_id = l.id 
            WHERE l.sigla = :acronym AND v.capitulo = :chapter";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':acronym' => $bookAcronym,
        ':chapter' => $chapter
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
  }
}
