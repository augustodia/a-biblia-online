<?php

class CompareController extends BaseController {
    public function show($version1, $version2, $book, $chapter, $verse, $endVerse = null) {
        $versesModel = new VersesModel();
        $booksModel = new BooksModel();
        
        // Se não foi especificado um endVerse, mostrar apenas o versículo atual
        if (!$endVerse) {
            $endVerse = $verse;
        }

        // Buscar os versículos no intervalo especificado para ambas as versões
        $verses1 = array();
        $verses2 = array();
        for ($i = $verse; $i <= $endVerse; $i++) {
            $verses1[] = $versesModel->getVerse($version1, $book, $chapter, $i);
            $verses2[] = $versesModel->getVerse($version2, $book, $chapter, $i);
        }
        
        $bookData = $booksModel->getBook($book);
        $totalVerses = $booksModel->getChapterVerses($bookData['id'], $chapter);

        $data = array(
            'versions' => $versesModel->getVersions(),
            'books' => $versesModel->getBooks(),
            'selectedVersion' => $version1,
            'compareVersion' => $version2,
            'book' => $bookData,
            'chapter' => $chapter,
            'startVerse' => $verse,
            'endVerse' => $endVerse,
            'verses1' => $verses1,
            'verses2' => $verses2,
            'totalVerses' => $totalVerses
        );

        $this->loadTemplate('Compare', $data);
    }
} 