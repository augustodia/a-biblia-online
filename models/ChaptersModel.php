<?php
class ChaptersModel extends BaseModel
{
  protected $db;
  protected $table = 'versiculos';
  protected $primaryKey = 'id';

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }

  public function getAllChaptersByBook(String $bookAcronym)
  {
    $query = $this->db->prepare('
      SELECT DISTINCT v.capitulo 
      FROM ' . $this->table . ' v
      INNER JOIN livros l ON v.livro_id = l.id
      WHERE l.sigla = :bookAcronym
      ORDER BY v.capitulo
    ');
    $query->execute(['bookAcronym' => $bookAcronym]);
    return $query->fetchAll();
  }
}


// 1	id Primária	int(10)		UNSIGNED	Não	Nenhum		AUTO_INCREMENT	Alterar Alterar	Eliminar Eliminar	
// 2	versao_id Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 3	livro_id Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 4	capitulo Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 5	versiculo Índice	tinyint(3)		UNSIGNED	Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
// 6	texto Índice	text	utf8_general_ci		Não	Nenhum			Alterar Alterar	Eliminar Eliminar	
