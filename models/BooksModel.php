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
    $query = $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY id');
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getBook(String $bookAcronym)
  {
    // Primeiro, buscar informações básicas do livro
    $query = $this->db->prepare('SELECT * FROM livros WHERE sigla = :bookAcronym');
    $query->execute(['bookAcronym' => $bookAcronym]);
    $book = $query->fetch(PDO::FETCH_ASSOC);

    if ($book) {
      // Depois, buscar a contagem de versículos por capítulo
      $versesQuery = $this->db->prepare('SELECT capitulo, COUNT(*) as total 
                                        FROM versiculos 
                                        WHERE livro_id = :book_id 
                                        GROUP BY capitulo 
                                        ORDER BY capitulo');
      $versesQuery->execute(['book_id' => $book['id']]);
      $verseCounts = $versesQuery->fetchAll(PDO::FETCH_ASSOC);

      // Criar array com o total de versículos por capítulo
      $book['versiculos'] = array_column($verseCounts, 'total');
    }

    return $book;
  }

  public function getChapterVerses($bookId, $chapter)
  {
    $query = $this->db->prepare('SELECT COUNT(*) as total FROM versiculos WHERE livro_id = :book_id AND capitulo = :chapter');
    $query->execute([
      'book_id' => $bookId,
      'chapter' => $chapter
    ]);
    
    return $query->fetch(PDO::FETCH_ASSOC)['total'];
  }
}
