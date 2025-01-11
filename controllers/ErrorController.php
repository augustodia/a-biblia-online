<?php
class ErrorController extends BaseController
{
    public function notFound()
    {
        http_response_code(404);
        
        $versesModel = new VersesModel();
        $booksModel = new BooksModel();
        
        $data = [
            'versions' => $versesModel->getVersions(),
            'books' => $booksModel->all(),
            'selectedVersion' => 'ARA' // Versão padrão
        ];
        
        $this->loadTemplate('404', $data);
    }
} 