<?php

use PHPUnit\Framework\TestCase;

class SearchControllerTest extends TestCase
{
    private $searchController;

    protected function setUp(): void
    {
        $this->searchController = new SearchController();
    }

    public function testSearchReturnsCorrectResults()
    {
        $_GET['query'] = 'love';
        $_GET['page'] = 1;

        ob_start();
        $this->searchController->search();
        $output = ob_get_clean();

        $this->assertStringContainsString('Search results for: <strong>love</strong>', $output);
        $this->assertStringContainsString('<span class="verse-number">', $output);
    }

    public function testSearchPagination()
    {
        $_GET['query'] = 'love';
        $_GET['page'] = 2;

        ob_start();
        $this->searchController->search();
        $output = ob_get_clean();

        $this->assertStringContainsString('Search results for: <strong>love</strong>', $output);
        $this->assertStringContainsString('<a href="http://localhost/mvc/search?query=love&amp;page=1">Previous</a>', $output);
        $this->assertStringContainsString('<a href="http://localhost/mvc/search?query=love&amp;page=3">Next</a>', $output);
    }

    public function testSearchConsidersSelectedBibleVersion()
    {
        $_GET['query'] = 'love';
        $_GET['page'] = 1;
        $_GET['version'] = 'ARA';

        ob_start();
        $this->searchController->search();
        $output = ob_get_clean();

        $this->assertStringContainsString('Search results for: <strong>love</strong>', $output);
        $this->assertStringContainsString('<span class="verse-number">', $output);
        $this->assertStringContainsString('ARA', $output);
    }

    public function testSearchResultsDisplaySelectedBookAndVersion()
    {
        $_GET['query'] = 'love';
        $_GET['page'] = 1;
        $_GET['version'] = 'ARA';

        ob_start();
        $this->searchController->search();
        $output = ob_get_clean();

        $this->assertStringContainsString('Search results for: <strong>love</strong>', $output);
        $this->assertStringContainsString('<span class="verse-number">', $output);
        $this->assertStringContainsString('ARA', $output);
    }
}
