<?php 
// Controlador para o acesso ao site. Login, Logout, Recuperação de senha
namespace App\Controller;

use App\Page;
use Router\Request;
use App\Authenticator;
use Database\AccessDB;

class Access {
    
    // exibe a página de login
    public function showLoginPage($error = [])
    {
        if(!Authenticator::checkLogin()){
            
            Page::render('@public/login.html', $error);
            exit();

        }else{
            
            $url = Authenticator::getUserURL();
            
            if($url === "401"){
                Page::showErrorHttpPage($url);
                exit();
            } else {
                header("Location: $url");
                exit();
            }
        }
    }

    // checa se os dados de login estão corretos e redireciona para a 
    // página de usuário específica a depender do perfil do usuário logado
    public function checkLogin()
    {   
        $request = new Request();
        
        $user = $request->__get("user");
        $password = $request->__get("password");
        
        $callback = Authenticator::makeLogin($user, $password);
        
        if($callback['error'] === false) {
            
            $url = Authenticator::getUserURL();
            
            if($url === "401"){
                Page::showErrorHttpPage($url);
                exit();
            } else {
                header("Location: $url");
                exit();
            }
            
        } else {
            if($callback['code'] === Authenticator::ERROR_BLOCKED_USER) {
                Page::render('@errors/access/blocked-user.html');
            } else if($callback['code'] === Authenticator::ERROR_INACTIVE_USER) {
                Page::render('@errors/access/inactive-user.html');
            } else if($callback['code'] === Authenticator::ERROR_DISABLED_USER) {
                Page::render('@errors/access/disabled-user.html');
            } else {
                $this->showLoginPage($callback);
            }
        }
    }

    public function makeLogout()
    {   
        $request = new Request;
        $code = $request->__get('logout-code');
        if(Authenticator::makeLogout($code)){
            header("Location: /login");
        } else {
            $url = Authenticator::getUserURL();
            header("Location: $url");
        }
        exit();
    }

    //exibe a página para recuperação de senha
    public function showRecoverPasswordPage($error = [])
    {
        Page::render('@public/recover-password.html', $error);
        exit();
    }

    public function sendEmailVerificationCode()
    {
        $request = new Request();

        $cpf = $request->__get('cpf');

        $callback = Authenticator::sendEmailVerificationCode($cpf);

        if($callback['error'] === false) {
            echo "email envaido com sucesso";
        } else {
            if($callback['code'] === Authenticator::ERROR_CODE_ALREADY_SENT) {
                Page::render('@errors/access/code-already-sent.html', $callback);
            } else if($callback['code'] === Authenticator::ERROR_SENDING_CODE) {
                Page::render('@errors/access/sending-code.html');
            }  else {
                $this->showRecoverPasswordPage($callback);
            }
        }
    }

    // realiza a validação do código de email
    public function showValidationPage($user, $code)
    {
        $verification = AccessDB::getVerificationCode($user, $code);

        if($verification && !empty($verification)) {
            
        }
    }
}