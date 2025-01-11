
<?php
class SearchController extends BaseController
{
  public function index(String $versionAcronym)
  {
    $searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    if ($currentPage < 1) {
        $currentPage = 1;
    }

    $versesModel = new VersesModel();
    $searchResults = $versesModel->searchVerses($versionAcronym, $searchTerm, $currentPage);
    
    // Carregar as versões e livros
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();

    $this->loadTemplate('Search', [
      'results' => $searchResults['results'],
      'pagination' => $searchResults['pagination'],
      'selectedVersion' => $versionAcronym,
      'searchTerm' => $searchTerm,
      'versions' => $versions,
      'books' => $books
    ]);
  }
}
