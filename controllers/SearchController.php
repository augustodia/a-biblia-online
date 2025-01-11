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
    
    // Log para debug
    error_log("Resultados da busca no controller: " . print_r($searchResults, true));
    
    // Carregar as versões e livros
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();

    $viewData = [
      'results' => $searchResults['results'],
      'pagination' => $searchResults['pagination'],
      'selectedVersion' => $versionAcronym,
      'searchTerm' => $searchTerm,
      'versions' => $versions,
      'books' => $books
    ];
    
    // Log para debug
    error_log("Dados enviados para a view: " . print_r($viewData, true));

    $this->loadTemplate('Search', $viewData);
  }
}
