<?php

class SearchController extends BaseController
{
    public function index()
    {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $version = isset($_GET['version']) ? $_GET['version'] : 'ARA';
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $resultsPerPage = 10;

        $this->loadModel('SearchModel');
        $searchResults = $this->model->search($query, $version);

        $totalResults = count($searchResults);
        $totalPages = ceil($totalResults / $resultsPerPage);
        $offset = ($currentPage - 1) * $resultsPerPage;
        $paginatedResults = array_slice($searchResults, $offset, $resultsPerPage);

        $this->loadTemplate('SearchResults', [
            'searchResults' => $paginatedResults,
            'query' => $query,
            'version' => $version,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }
}
