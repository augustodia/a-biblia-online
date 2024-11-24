<?php
class VersesController extends BaseController
{
  public function index(String $versionAcronym, String $bookAcronym, int $selectedChapter)
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

  public function show(String $versionAcronym, String $bookAcronym, int $chapterNumber, int $verseNumber)
  {
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();
    $versesModel = new VersesModel();
    $verse = $versesModel->getVerse($versionAcronym, $bookAcronym, $chapterNumber, $verseNumber);

    $this->loadTemplate('VerseDetail', [
      'verse' => $verse,
      'selectedVersion' => $versionAcronym,
      'book' => $bookAcronym,
      'chapter' => $chapterNumber,
      'verseNumber' => $verseNumber,
      'versions' => $versions,
      'books' => $books
    ]);
  }

  public function detail(String $versionAcronym, String $bookAcronym, int $chapterNumber, int $verseNumber)
  {
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();
    $versesModel = new VersesModel();
    $verse = $versesModel->getVerse($versionAcronym, $bookAcronym, $chapterNumber, $verseNumber);

    $this->loadTemplate('VerseDetail', [
      'verse' => $verse,
      'selectedVersion' => $versionAcronym,
      'book' => $bookAcronym,
      'chapter' => $chapterNumber,
      'verseNumber' => $verseNumber,
      'versions' => $versions,
      'books' => $books
    ]);
  }
}
