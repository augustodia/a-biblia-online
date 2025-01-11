<?php
class VerseController extends BaseController
{
  public function show($version, $bookAcronym, $chapter, $verse, $endVerse = null)
  {
    $versesModel = new VersesModel();
    $booksModel = new BooksModel();

    // Converte parâmetros para inteiro
    $chapter = (int)$chapter;
    $verse = (int)$verse;
    $endVerse = $endVerse ? (int)$endVerse : $verse;

    // Obtém o total de versículos do capítulo
    $totalVerses = $booksModel->getChapterVerses($bookAcronym, $chapter);

    // Obtém os versículos
    $verses = [];
    for ($i = $verse; $i <= $endVerse; $i++) {
      $verseData = $versesModel->getVerse($version, $bookAcronym, $chapter, $i);
      if ($verseData) {
        $verses[] = $verseData;
      }
    }

    $data = [
      'versions' => $versesModel->getVersions(),
      'books' => $versesModel->getBooks(),
      'selectedVersion' => $version,
      'book' => $booksModel->getBook($bookAcronym),
      'chapter' => $chapter,
      'startVerse' => $verse,
      'endVerse' => $endVerse,
      'verses' => $verses,
      'totalVerses' => $totalVerses
    ];

    $this->loadTemplate('Verse', $data);
  }
}
