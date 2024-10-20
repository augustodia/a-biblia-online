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
}
