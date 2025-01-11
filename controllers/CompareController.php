<?php

class CompareController extends BaseController {
    public function show($version1, $version2, $book, $chapter, $verse) {
        $versesModel = new VersesModel();
        
        $data = array(
            'versions' => $versesModel->getVersions(),
            'books' => $versesModel->getBooks(),
            'selectedVersion' => $version1,
            'compareVersion' => $version2,
            'book' => $versesModel->getBook($book),
            'chapter' => $chapter,
            'verse' => $verse,
            'verse1' => $versesModel->getVerse($version1, $book, $chapter, $verse),
            'verse2' => $versesModel->getVerse($version2, $book, $chapter, $verse)
        );

        $this->loadTemplate('Compare', $data);
    }
} 