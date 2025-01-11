<?php

class CompareController extends BaseController {
    public function show($versions, $bookAcronym, $chapter, $verse, $endVerse = null) {
        $versesModel = new VersesModel();
        $booksModel = new BooksModel();
        
        // // Debug dos parâmetros recebidos
        // echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; font-family: monospace;'>";
        // echo "<strong>Debug - Parâmetros recebidos:</strong><br>";
        // echo "versions: " . $versions . "<br>";
        // echo "bookAcronym: " . $bookAcronym . "<br>";
        // echo "chapter: " . $chapter . "<br>";
        // echo "verse: " . $verse . "<br>";
        // echo "endVerse: " . ($endVerse ?? 'null') . "<br><br>";
        
        // Separa as versões e remove espaços em branco
        $versionArray = array_filter(array_map('trim', explode('+', $versions)));
        $selectedVersion = reset($versionArray);
        
        // Busca os dados do livro
        $book = $booksModel->getBook($bookAcronym);
        // if (!$book) {
        //     echo "Livro não encontrado: " . $bookAcronym . "<br>";
        //     header('Location: ' . BASE_URL);
        //     exit;
        // }
        
        // Garante que os valores são inteiros e válidos
        $chapter = max(1, (int)$chapter);
        $startVerse = max(1, (int)$verse);
        $endVerse = $endVerse ? max($startVerse, (int)$endVerse) : $startVerse;
        
        // Se endVerse for menor que startVerse, inverte
        if ($endVerse < $startVerse) {
            $temp = $startVerse;
            $startVerse = $endVerse;
            $endVerse = $temp;
        }
        
        // Busca o total de versículos
        $totalVerses = $booksModel->getChapterVerses($bookAcronym, $chapter);
        
        // // Debug dos valores processados
        // echo "<strong>Debug - Valores processados:</strong><br>";
        // echo "chapter: " . $chapter . "<br>";
        // echo "startVerse: " . $startVerse . "<br>";
        // echo "endVerse: " . $endVerse . "<br>";
        // echo "totalVerses: " . $totalVerses . "<br>";
        // echo "book: " . print_r($book, true) . "<br>";
        // echo "versionArray: " . print_r($versionArray, true) . "<br>";
        // echo "</div>";
        
        // Prepara os dados das versões
        $versesData = [];
        foreach ($versionArray as $version) {
            $verses = [];
            for ($i = $startVerse; $i <= $endVerse; $i++) {
                $verseData = $versesModel->getVerse($version, $bookAcronym, $chapter, $i);
                if ($verseData) {
                    $verses[] = $verseData;
                }
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
            'totalVerses' => $totalVerses,
            'hasNextVerse' => $endVerse < $totalVerses,
            'hasPreviousVerse' => $startVerse > 1
        );
        
        $this->loadTemplate('Compare', $data);
    }
} 