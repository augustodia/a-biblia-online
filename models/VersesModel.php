<?php
class VersesModel extends BaseModel
{
  protected $db;
  protected $table = 'versiculos';
  protected $primaryKey = 'id';
  protected $resultsPerPage = 50; // Quantidade de resultados por página

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }

  public function getAllVerses(String $version, String $bookAcronym, int $chapterNumber)
  {
    $query = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE capitulo = :chapterNumber AND livro_id = (SELECT id FROM livros WHERE sigla = :bookAcronym) AND versao_id = (SELECT id FROM versoes WHERE sigla = :version)');
    $query->execute(['chapterNumber' => $chapterNumber, 'bookAcronym' => $bookAcronym, 'version' => $version]);
    return $query->fetchAll();
  }

  public function searchVerses(String $version, String $searchTerm, int $page = 1)
  {
    // Primeiro, vamos pegar o total de resultados
    $countQuery = $this->db->prepare('
      SELECT COUNT(*) as total
      FROM ' . $this->table . ' v
      INNER JOIN livros l ON v.livro_id = l.id
      WHERE v.texto LIKE :searchTerm 
      AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)
    ');
    
    $countQuery->execute([
      'searchTerm' => '%' . $searchTerm . '%',
      'version' => $version
    ]);
    
    $totalResults = $countQuery->fetch()['total'];
    $totalPages = ceil($totalResults / $this->resultsPerPage);
    $offset = ($page - 1) * $this->resultsPerPage;

    // Agora pegamos os resultados da página atual
    $query = $this->db->prepare('
      SELECT v.*, l.sigla as book, l.nome as book_name 
      FROM ' . $this->table . ' v
      INNER JOIN livros l ON v.livro_id = l.id
      WHERE v.texto LIKE :searchTerm 
      AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)
      ORDER BY l.id, v.capitulo, v.versiculo
      LIMIT :limit OFFSET :offset
    ');
    
    $query->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $query->bindValue(':version', $version, PDO::PARAM_STR);
    $query->bindValue(':limit', $this->resultsPerPage, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    
    return [
      'results' => $query->fetchAll(),
      'pagination' => [
        'total' => $totalResults,
        'perPage' => $this->resultsPerPage,
        'currentPage' => $page,
        'totalPages' => $totalPages
      ]
    ];
  }

  public function getVerseWithContext(String $version, String $bookAcronym, int $chapter, int $verse)
  {
    // Buscar o versículo atual com informações do livro
    $query = $this->db->prepare('
      SELECT v.*, l.sigla as book, l.nome as book_name 
      FROM ' . $this->table . ' v
      INNER JOIN livros l ON v.livro_id = l.id
      WHERE v.capitulo = :chapter 
      AND v.versiculo = :verse
      AND l.sigla = :bookAcronym
      AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)
    ');
    
    $query->execute([
      'chapter' => $chapter,
      'verse' => $verse,
      'bookAcronym' => $bookAcronym,
      'version' => $version
    ]);
    
    $currentVerse = $query->fetch();

    // Buscar 3 versículos anteriores
    $previousQuery = $this->db->prepare('
      SELECT v.*, l.sigla as book, l.nome as book_name 
      FROM ' . $this->table . ' v
      INNER JOIN livros l ON v.livro_id = l.id
      WHERE v.capitulo = :chapter 
      AND v.versiculo < :verse
      AND l.sigla = :bookAcronym
      AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)
      ORDER BY v.versiculo DESC
      LIMIT 3
    ');
    
    $previousQuery->execute([
      'chapter' => $chapter,
      'verse' => $verse,
      'bookAcronym' => $bookAcronym,
      'version' => $version
    ]);
    
    $previousVerses = array_reverse($previousQuery->fetchAll());

    // Buscar 3 versículos seguintes
    $nextQuery = $this->db->prepare('
      SELECT v.*, l.sigla as book, l.nome as book_name 
      FROM ' . $this->table . ' v
      INNER JOIN livros l ON v.livro_id = l.id
      WHERE v.capitulo = :chapter 
      AND v.versiculo > :verse
      AND l.sigla = :bookAcronym
      AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)
      ORDER BY v.versiculo ASC
      LIMIT 3
    ');
    
    $nextQuery->execute([
      'chapter' => $chapter,
      'verse' => $verse,
      'bookAcronym' => $bookAcronym,
      'version' => $version
    ]);
    
    $nextVerses = $nextQuery->fetchAll();

    return [
      'currentVerse' => $currentVerse,
      'previousVerses' => $previousVerses,
      'nextVerses' => $nextVerses
    ];
  }
}


// 1	id Primária	int(10)		UNSIGNED	Não	Nenhum		AUTO_INCREMENT	Alterar Alterar	Eliminar Eliminar	
// 2	versao_id Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 3	livro_id Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 4	capitulo Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 5	versiculo Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 6	texto Índice	text	utf8_general_ci		Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
