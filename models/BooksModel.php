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

  public function all() {
    // Primeiro, buscar todos os livros
    $sql = "SELECT * FROM {$this->table} ORDER BY id";
    $stmt = $this->db->query($sql);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Depois, para cada livro, buscar a contagem de versículos por capítulo
    foreach ($books as &$book) {
      $sql = "SELECT capitulo, COUNT(*) as total 
              FROM versiculos 
              WHERE livro_id = :book_id 
              GROUP BY capitulo 
              ORDER BY capitulo";
      
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':book_id', $book['id']);
      $stmt->execute();
      
      $versiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $book['versiculos'] = array_column($versiculos, 'total');
    }

    return $books;
  }

  public function getByAcronym($acronym) {
    // Primeiro, buscar o livro
    $sql = "SELECT * FROM {$this->table} WHERE sigla = :acronym LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':acronym', $acronym);
    $stmt->execute();
    
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($book) {
      // Depois, buscar a contagem de versículos por capítulo
      $sql = "SELECT capitulo, COUNT(*) as total 
              FROM versiculos 
              WHERE livro_id = :book_id 
              GROUP BY capitulo 
              ORDER BY capitulo";
      
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':book_id', $book['id']);
      $stmt->execute();
      
      $versiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $book['versiculos'] = array_column($versiculos, 'total');
    }
    
    return $book;
  }

  public function getChapterVerses($bookId, $chapter) {
    $sql = "SELECT COUNT(*) as total 
            FROM versiculos 
            WHERE livro_id = :book_id 
            AND capitulo = :chapter";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':book_id', $bookId);
    $stmt->bindValue(':chapter', $chapter);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['total'] : 0;
  }
}
