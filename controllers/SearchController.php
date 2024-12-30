<?php

class SearchController extends BaseController
{
    public function search()
    {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $resultsPerPage = 10;
        $offset = ($page - 1) * $resultsPerPage;

        $searchModel = new SearchModel();
        $results = $searchModel->searchVerses($query, $resultsPerPage, $offset);

        $this->loadTemplate('SearchResults', [
            'results' => $results,
            'query' => $query,
            'currentPage' => $page,
            'resultsPerPage' => $resultsPerPage
        ]);
    }
}
