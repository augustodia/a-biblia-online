<?php
class VerseController extends BaseController
{
  public function show($version, $book, $chapter, $verse, $endVerse = null)
  {
    $versesModel = new VersesModel();
    $booksModel = new BooksModel();

    // Se não foi especificado um endVerse, mostrar apenas o versículo atual
    if (!$endVerse) {
      $endVerse = $verse;
    }

    // Buscar os versículos no intervalo especificado
    $verses = [];
    for ($i = $verse; $i <= $endVerse; $i++) {
      $verses[] = $versesModel->getVerse($version, $book, $chapter, $i);
    }

    $bookData = $booksModel->getBook($book);
    $totalVerses = $booksModel->getChapterVerses($bookData['id'], $chapter);

    $data = array(
      'versions' => $versesModel->getVersions(),
      'books' => $versesModel->getBooks(),
      'selectedVersion' => $version,
      'book' => $bookData,
      'chapter' => $chapter,
      'startVerse' => $verse,
      'endVerse' => $endVerse,
      'verses' => $verses,
      'totalVerses' => $totalVerses
    );

    $this->loadTemplate('Verse', $data);
  }
}
