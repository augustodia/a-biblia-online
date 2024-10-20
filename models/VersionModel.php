<?php
class VersionModel extends BaseModel
{
  protected $db;
  protected $table = 'versoes';
  protected $primaryKey = 'id';

  public function __construct()
  {
    global $db;
    $this->db = $db;
  }
}
