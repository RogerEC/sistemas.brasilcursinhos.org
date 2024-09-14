<?php 
// Controlador para área pública
namespace App\Controller;

use App\Page;

class Encup {
    
    // exibe a página inicial do site
    public function showHomePage()
    {   
        Page::render('@encup/2024.html');
    }

    public function showPhotosPage()
    {
        Page::render('@encup/pages/photos.html');
    }

    public function showExceptionalInscriptionPage()
    {
        Page::render('@encup/pages/exceptional-inscription.html');
    }
}