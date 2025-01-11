<?php

class CompareController extends BaseController {
    public function show($versions, $bookAcronym, $chapter, $verse, $endVerse = null) {
        $versesModel = new VersesModel();
        $booksModel = new BooksModel();
        
        // Separa as versões e remove espaços em branco
        $versionArray = array_map('trim', explode('+', $versions));
        $selectedVersion = $versionArray[0];
        
        // Busca os dados do livro e total de versículos
        $book = $booksModel->getBook($bookAcronym);
        $totalVerses = $booksModel->getChapterVerses($bookAcronym, (int)$chapter);
        
        // Debug do total de versículos
        error_log("Debug Compare - Parâmetros getChapterVerses:");
        error_log("bookAcronym: " . $bookAcronym);
        error_log("chapter: " . $chapter);
        error_log("totalVerses retornado: " . $totalVerses);
        
        // Garante que os valores são inteiros
        $chapter = (int)$chapter;
        $startVerse = (int)$verse;
        $endVerse = $endVerse ? (int)$endVerse : $startVerse;
        
        // Prepara os dados das versões
        $versesData = [];
        foreach ($versionArray as $version) {
            if (empty($version)) continue;
            
            $verses = [];
            for ($i = $startVerse; $i <= $endVerse; $i++) {
                $verses[] = $versesModel->getVerse($version, $bookAcronym, $chapter, $i);
            }
            $versesData[$version] = $verses;
        }
        
        // Prepara os dados para a view
        $data = array(
            'versions' => $versesModel->getVersions(),
            'books' => $booksModel->all(),
            'selectedVersion' => $selectedVersion,
            'selectedVersions' => $versionArray,
            'book' => $book,
            'chapter' => $chapter,
            'startVerse' => $startVerse,
            'endVerse' => $endVerse,
            'versesData' => $versesData,
            'totalVerses' => (int)$totalVerses,
            'hasNextVerse' => $endVerse < $totalVerses,
            'hasPreviousVerse' => $startVerse > 1
        );
        
        // Debug
        error_log("Debug Compare - Dados passados para a view:");
        error_log("startVerse: " . $data['startVerse']);
        error_log("endVerse: " . $data['endVerse']);
        error_log("totalVerses: " . $data['totalVerses']);
        error_log("hasNextVerse: " . ($data['hasNextVerse'] ? 'true' : 'false'));
        error_log("hasPreviousVerse: " . ($data['hasPreviousVerse'] ? 'true' : 'false'));
        
        $this->loadTemplate('Compare', $data);
    }
} 