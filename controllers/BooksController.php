<?php
class BooksController extends BaseController
{
  public function index(String $versionAcronym)
  {
    $booksModel = new BooksModel();
    $books = $booksModel->all();

    $this->loadTemplate('Books', ['books' => $books, 'version' => $versionAcronym]);
  }
}
