<?php

class CompareController extends BaseController {
    public function show($versions, $bookAcronym, $chapter, $verse, $endVerse = null) {
        $versesModel = new VersesModel();
        $booksModel = new BooksModel();
        
        // Converte parâmetros para inteiro
        $chapter = (int)$chapter;
        $verse = (int)$verse;
        $endVerse = $endVerse ? (int)$endVerse : $verse;
        
        // Obtém o total de versículos do capítulo
        $totalVerses = $booksModel->getChapterVerses($bookAcronym, $chapter);
        
        // Processa as versões
        $versionArray = array_filter(array_map('trim', explode('+', $versions)));
        $selectedVersion = reset($versionArray);
        
        // Obtém os versículos para cada versão
        $versesData = [];
        foreach ($versionArray as $version) {
            $verses = [];
            for ($i = $verse; $i <= $endVerse; $i++) {
                $verseData = $versesModel->getVerse($version, $bookAcronym, $chapter, $i);
                if ($verseData) {
                    $verses[] = $verseData;
                }
            }
            $versesData[$version] = $verses;
        }
        
        $data = [
            'versions' => $versesModel->getVersions(),
            'books' => $versesModel->getBooks(),
            'selectedVersion' => $selectedVersion,
            'selectedVersions' => $versionArray,
            'book' => $booksModel->getBook($bookAcronym),
            'chapter' => $chapter,
            'startVerse' => $verse,
            'endVerse' => $endVerse,
            'versesData' => $versesData,
            'totalVerses' => $totalVerses
        ];
        
        $this->loadTemplate('Compare', $data);
    }
} 