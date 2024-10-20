<?php
class VersesModel extends BaseModel
{
  protected $db;
  protected $table = 'versiculos';
  protected $primaryKey = 'id';

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
}


// 1	id Primária	int(10)		UNSIGNED	Não	Nenhum		AUTO_INCREMENT	Alterar Alterar	Eliminar Eliminar	
// 2	versao_id Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 3	livro_id Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 4	capitulo Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 5	versiculo Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 6	texto Índice	text	utf8_general_ci		Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
