<?php
class VerseController extends BaseController
{
  public function show(String $versionAcronym, String $bookAcronym, int $chapter, int $verse, ?int $endVerse = null)
  {
    $versesModel = new VersesModel();
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();
    $book = $booksModel->getByAcronym($bookAcronym);

    // Se não foi especificado um endVerse, mostrar 5 versículos por padrão
    if (!$endVerse) {
      $endVerse = $verse;
    }

    // Buscar os versículos do intervalo
    $verses = array();
    for ($i = $verse; $i <= $endVerse; $i++) {
      $verseData = $versesModel->getVerse($versionAcronym, $bookAcronym, $chapter, $i);
      if ($verseData) {
        $verses[] = $verseData;
      }
    }

    // Buscar contexto (versículos anteriores e posteriores)
    $previousVerses = array();
    if ($verse > 1) {
      for ($i = max(1, $verse - 2); $i < $verse; $i++) {
        $verseData = $versesModel->getVerse($versionAcronym, $bookAcronym, $chapter, $i);
        if ($verseData) {
          $previousVerses[] = $verseData;
        }
      }
    }

    $nextVerses = array();
    if ($endVerse < ($book['versiculos'][$chapter - 1] ?? PHP_INT_MAX)) {
      for ($i = $endVerse + 1; $i <= min($endVerse + 2, $book['versiculos'][$chapter - 1]); $i++) {
        $verseData = $versesModel->getVerse($versionAcronym, $bookAcronym, $chapter, $i);
        if ($verseData) {
          $nextVerses[] = $verseData;
        }
      }
    }

    $this->loadTemplate('Verse', [
      'verses' => $verses,
      'previousVerses' => $previousVerses,
      'nextVerses' => $nextVerses,
      'selectedVersion' => $versionAcronym,
      'versions' => $versions,
      'books' => $books,
      'book' => $book,
      'chapter' => $chapter,
      'startVerse' => $verse,
      'endVerse' => $endVerse
    ]);
  }
}
