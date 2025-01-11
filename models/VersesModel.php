<?php
class VersesModel extends BaseModel
{
  protected $db;
  protected $table = 'versiculos';
  protected $primaryKey = 'id';
  protected $resultsPerPage = 50;
  private const BASE_SELECT = 'SELECT v.*, l.sigla as book, l.nome as book_name FROM versiculos v INNER JOIN livros l ON v.livro_id = l.id';

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }

  public function getAllVerses(String $version, String $bookAcronym, int $chapterNumber)
  {
    $query = $this->db->prepare(self::BASE_SELECT . ' WHERE v.capitulo = :chapterNumber AND l.sigla = :bookAcronym AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)');
    $query->execute(['chapterNumber' => $chapterNumber, 'bookAcronym' => $bookAcronym, 'version' => $version]);
    return $query->fetchAll();
  }

  public function searchVerses(String $version, String $searchTerm, int $page = 1)
  {
    $countQuery = $this->db->prepare('SELECT COUNT(*) as total FROM ' . $this->table . ' v INNER JOIN livros l ON v.livro_id = l.id WHERE v.texto LIKE :searchTerm AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)');
    
    $countQuery->execute([
      'searchTerm' => '%' . $searchTerm . '%',
      'version' => $version
    ]);
    
    $totalResults = $countQuery->fetch()['total'];
    $totalPages = ceil($totalResults / $this->resultsPerPage);
    $offset = ($page - 1) * $this->resultsPerPage;

    $query = $this->db->prepare(self::BASE_SELECT . ' WHERE v.texto LIKE :searchTerm AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version) ORDER BY l.id, v.capitulo, v.versiculo LIMIT :limit OFFSET :offset');
    
    $query->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $query->bindValue(':version', $version, PDO::PARAM_STR);
    $query->bindValue(':limit', $this->resultsPerPage, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    
    $results = $query->fetchAll();
    
    // Log para debug
    error_log("Resultados da busca: " . print_r($results, true));
    
    return [
      'results' => $results,
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
    $query = $this->db->prepare(self::BASE_SELECT . ' WHERE v.capitulo = :chapter AND v.versiculo = :verse AND l.sigla = :bookAcronym AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version)');
    
    $query->execute([
      'chapter' => $chapter,
      'verse' => $verse,
      'bookAcronym' => $bookAcronym,
      'version' => $version
    ]);
    
    $currentVerse = $query->fetch();

    $previousQuery = $this->db->prepare(self::BASE_SELECT . ' WHERE v.capitulo = :chapter AND v.versiculo < :verse AND l.sigla = :bookAcronym AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version) ORDER BY v.versiculo DESC LIMIT 3');
    
    $previousQuery->execute([
      'chapter' => $chapter,
      'verse' => $verse,
      'bookAcronym' => $bookAcronym,
      'version' => $version
    ]);
    
    $previousVerses = array_reverse($previousQuery->fetchAll());

    $nextQuery = $this->db->prepare(self::BASE_SELECT . ' WHERE v.capitulo = :chapter AND v.versiculo > :verse AND l.sigla = :bookAcronym AND v.versao_id = (SELECT id FROM versoes WHERE sigla = :version) ORDER BY v.versiculo ASC LIMIT 3');
    
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
