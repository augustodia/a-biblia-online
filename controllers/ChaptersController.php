<?php
class ChaptersController extends BaseController
{
  public function index(String $version, String $bookAcronym)
  {
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();
    $chaptersModel = new ChaptersModel();
    $chapters = $chaptersModel->getAllChaptersByBook($bookAcronym);

    $this->loadTemplate('Chapters', ['chapters' => $chapters, 'versions' => $versions, 'selectedVersion' => $version, 'book' => $bookAcronym, 'books' => $books]);
  }
}
