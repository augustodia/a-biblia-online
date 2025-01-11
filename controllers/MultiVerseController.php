<?php

class MultiVerseController extends BaseController {
    public function show($version, $book, $chapter, $verse1, $verse2) {
        $versesModel = new VersesModel();
        
        $verses = array();
        for ($i = $verse1; $i <= $verse2; $i++) {
            $verses[] = $versesModel->getVerse($version, $book, $chapter, $i);
        }
        
        $data = array(
            'versions' => $versesModel->getVersions(),
            'books' => $versesModel->getBooks(),
            'selectedVersion' => $version,
            'book' => $versesModel->getBook($book),
            'chapter' => $chapter,
            'startVerse' => $verse1,
            'endVerse' => $verse2,
            'verses' => $verses
        );

        $this->loadTemplate('MultiVerse', $data);
    }
} 