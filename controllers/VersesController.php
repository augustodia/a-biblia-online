<?php
class VersesController extends BaseController
{
  public function index(String $versionAcronym, String $bookAcronym, int $selectedChapter,)
  {
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();
    $versesModel = new VersesModel();
    $verses = $versesModel->getAllVerses($versionAcronym, $bookAcronym, $selectedChapter);
    $chaptersModel = new ChaptersModel();
    $chapters = $chaptersModel->getAllChaptersByBook($bookAcronym);

    $this->loadTemplate('Verses', [
      'verses' => $verses,
      'selectedVersion' => $versionAcronym,
      'book' => $bookAcronym,
      'selectedChapter' => $selectedChapter,
      'versions' => $versions,
      'books' => $books,
      'chapters' => $chapters
    ]);
  }
}
