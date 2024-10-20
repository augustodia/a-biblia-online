<?php
class HomeController extends BaseController
{
  public function index(String $selectedVersion = 'ARA')
  {
    $versionModel = new VersionModel();
    $booksModel = new BooksModel();
    $books = $booksModel->all();
    $versions = $versionModel->all();

    $this->loadTemplate('Home', ['versions' => $versions, 'books' => $books, 'selectedVersion' => $selectedVersion]);
  }
}
