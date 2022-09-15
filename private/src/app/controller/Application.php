<?php 
// Controlador para área pública
namespace App\Controller;

use App\Authenticator;
use App\Log;
use Router\Request;

class Application {
    
    // exibe a página inicial do site
    public function checkLogin()
    {   
        $request = new Request;
        
        $user = $request->__get("user");
        $password = $request->__get("password");
        
        $callback = Authenticator::makeLogin($user, $password, Authenticator::LOGIN_TYPE_APP);
        
        echo json_encode($callback);
        exit;
    }
}