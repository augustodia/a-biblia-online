<?php
class VerseController extends BaseController
{
  public function show(String $versionAcronym, String $bookAcronym, int $chapter, int $verse)
  {
    $versesModel = new VersesModel();
    $verseData = $versesModel->getVerseWithContext($versionAcronym, $bookAcronym, $chapter, $verse);
    
    // Carregar as versões e livros (necessário para o template)
    $versionsModel = new VersionModel();
    $versions = $versionsModel->all();
    $booksModel = new BooksModel();
    $books = $booksModel->all();

    $this->loadTemplate('Verse', [
      'verse' => $verseData['currentVerse'],
      'previousVerses' => $verseData['previousVerses'],
      'nextVerses' => $verseData['nextVerses'],
      'selectedVersion' => $versionAcronym,
      'versions' => $versions,
      'books' => $books
    ]);
  }
} 