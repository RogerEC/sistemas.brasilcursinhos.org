<?php 
// classe responsável por gerenciar e controlar a autenticação do usuário no sistema
namespace App;

use App\Controller\Access;
use Database\AccessDB;
use Database\SelectDB;
use Database\UpdateDB;
use DateTime;
use DateTimeZone;

class Authenticator
{
    const ERROR_INVALID_EMAIL = 0;
    const ERROR_INVALID_USERNAME = 1;
    const ERROR_INVALID_NUMBER = 2;
    const ERROR_INVALID_PASSWORD = 3;
    const ERROR_INVALID_USER_DATA = 4;
    const ERROR_USER_NOT_FOUND = 5;
    const ERROR_WRONG_PASSWORD = 6;
    const ERROR_INACTIVE_USER = 7;
    const ERROR_BLOCKED_USER = 8;
    const ERROR_DISABLED_USER = 9;
    const ERROR_INVALID_CPF = 10;
    const ERROR_SENDING_CODE = 11;
    const ERROR_CODE_ALREADY_SENT = 12;
    const LOGIN_TYPE_SITE = 13;
    const LOGIN_TYPE_APP = 14;
    
    // Verifica se o usuário está logado
    public static function checkLogin()
    {
        return (isset($_SESSION['userId']) && !empty($_SESSION['userId']))? true:false;
    }

    // Pega o id do usuário caso ele esteja logado ou retorna null caso contrário
    public static function getUserID()
    {
        if (self::checkLogin()) {
            return $_SESSION['userId'];
        } else {
            return null;
        }
    }

    // retorna o departamento do usuário dependendo do seu tipo
    public static function getDepartment()
    {
        if(!self::checkLogin()) {
            self::makeLogout(true);
            return "401";
        }
        switch(self::getUserType()){
            case 'U':
                $departament = 'usuario';
                break;
            case 'ADMIN':
                $departament = 'administrador';
                break;
            default:
                $departament = "401";
                self::makeLogout(true);
        }
        return $departament;
    }

    // retorna a URL para a página restrita inicial específica dependendo do tipo de usuário
    public static function getUserURL() {
        if (self::checkLogin()) {
            switch(self::getUserType()){
                case 'U':
                    $url = '/usuario';
                    break;
                case 'ADMIN':
                    $url = '/administrador';
                    break;
                default:
                    $url = "401";
                    self::makeLogout(true);
            }
            return $url;
        } else {
            self::makeLogout(true);
            return "401";
        }
    }

    // Pega o typo do usuário caso esteja logado ou retorna null caso contrário
    public static function getUserType()
    {
        if (self::checkLogin()) {
            return $_SESSION['userType'];
        } else {
            return null;
        }
    }

    public static function makeLogin($user, $password, $loginType = self::LOGIN_TYPE_SITE)
    {
        $password = (empty($password))? false:$password;

        if(strpos($user, '@')) {
            $data = DataValidator::validateEmail($user);
            if(!$data) {
                return array(
                    'error' => true,
                    'code' => self::ERROR_INVALID_EMAIL,
                    'userError' => 'Endereço de e-mail inválido!',
                    'user' => $user
                );
            } else {
                $type = 'email';
            }
        } else if(DataValidator::isNumber($user)) {
            $data = DataValidator::validateCpf($user);
            if(!$data) {
                $data = DataValidator::validateRegistrationNumber($user);
                if(!$data) {
                    return array(
                        'error' => true,
                        'code' => self::ERROR_INVALID_NUMBER,
                        'userError' => 'Número de CPF ou Matrícula BC inválido!',
                        'user' => $user
                    );
                } else {
                    $type = 'registration';
                }
            } else {
                $type = 'cpf';
            }
        } else {
            // username
            if(DataValidator::isAlphaNum($user)) {
                $data = DataValidator::validateString($user);
                if(empty($data)) {
                    return array(
                        'error' => true,
                        'code' => self::ERROR_INVALID_USERNAME,
                        'userError' => 'Nome de usuário inválido!',
                        'user' => $user
                    );
                } else {
                    $type = 'username';
                }
            } else {
                return array(
                    'error' => true,
                    'code' => self::ERROR_INVALID_USER_DATA,
                    'userError' => 'Dado de usuário inválido!',
                    'user' => $user
                );
            }
        }

        if($password === false) {
            return array(
                'error' => true,
                'code' => self::ERROR_INVALID_PASSWORD,
                'passwordError' => 'O Campo de senha não pode estar vazio!',
                'user' => $user
            );
        }

        $userData = AccessDB::getUserData($data, $type);
        
        if(!empty($userData)) {

            if($userData->status === 'A') {
                
                if(password_verify($password, $userData->passwordHash)) {

                    if (password_needs_rehash($userData->passwordHash, PASSWORD_ARGON2ID, [
                        'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                        'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                        'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
                    ])) {
                    
                        $newHash = password_hash($password, PASSWORD_ARGON2ID, [
                            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                            'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
                        ]);
    
                        AccessDB::updatePasswordHash($userData->idUser, $newHash);
                    }
                    
                    if($loginType === self::LOGIN_TYPE_SITE) {
                        $_SESSION['userId'] = $userData->idUser;
                        $_SESSION['userType'] = $userData->type;

                        AccessDB::updateAccessLog($userData, true);
                        
                        return array('error' => false);
                    } else {
                        return array('error' => false, 'token' => 'abc123', 'userId' => '123');
                    }
                } else {
                    self::makeLogout(true);
                    
                    AccessDB::updateAccessLog($userData, false);

                    if($userData->accessAttempts >= 5) {
                        return array(
                            'error' => true,
                            'code' => self::ERROR_BLOCKED_USER,
                            'systemError' => 'Usuário bloqueado. Contate um administrador'
                        );
                    } else {
                        $attempts = ($userData->accessAttempts > 1)? ' ' . (5-$userData->accessAttempts) . ' tentativas restantes':'';
                        return array(
                            'error' => true,
                            'code' => self::ERROR_WRONG_PASSWORD,
                            'passwordError' => 'Senha incorreta!' . $attempts,
                            'user' => $user
                        );
                    }
                }
            } else if($userData->status === 'B') {
                return array(
                    'error' => true,
                    'code' => self::ERROR_BLOCKED_USER,
                    'systemError' => 'Usuário bloqueado. Contate o suporte.'
                );
            } else if ($userData->status === 'I') {
                return array(
                    'error' => true,
                    'code' => self::ERROR_INACTIVE_USER,
                    'systemError' => 'Usuário inativo. Acesso o site sistemas.brasilcursinhos.org para realizar o primeiro acesso e ativar a sua conta'
                );
            } else {
                return array(
                    'error' => true,
                    'code' => self::ERROR_DISABLED_USER,
                    'systemError' => 'Usuário desligado. Contate o suporte.'
                );
            }

        } else {
            return array(
                'error' => true,
                'code' => self::ERROR_USER_NOT_FOUND,
                'userError' => 'Usuário não encontrado!',
                'user' => $user
            );
        }
    }

    public static function generateLogoutCode()
    {
        $code = self::getRandomCode(64);
        $_SESSION['logoutCode'] = $code;
        return $code;
    }

    // Destrói a sessão, deslogando o usuário do sistema.
    public static function makeLogout($code)
    {
        if($code === true || $code === $_SESSION['logoutCode']) {
            
            $_SESSION = array();

            if(ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time()-42000, $params['path'],
                $params['domain'], $params['secure'], $params["httponly"]);
            }

            session_destroy();

            return true;
        }

        return false;
    }

    public static function getRandomCode($size){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $randomCode = '';
        for($i = 0; $i < $size; $i = $i+1){
           $randomCode .= $characters[random_int(0, strlen($characters)-1)];
        }
        return $randomCode;
    }

    public static function sendEmailVerificationCode($cpf) {
        
        $cpfValidated = DataValidator::validateCpf($cpf);
        
        if(!$cpfValidated) {
            return array(
                'error' => true,
                'code' => self::ERROR_INVALID_CPF,
                'cpf' => $cpf,
                'cpfError' => 'O número de CPF informado é inválido!'
            );
        }
        
        $user = AccessDB::getUserEmail($cpfValidated);
        
        if(!$user || empty($user)) {
            return array(
                'error' => true,
                'code' => self::ERROR_USER_NOT_FOUND,
                'cpf' => $cpf,
                'cpfError' => 'Nenhum usuário encontrado para o CPF informado.'
            );
        }

        $date = AccessDB::getLastSentVerificationCode($user);

        if($date && !empty($date)) {
            
            $timezone = new DateTimeZone('America/Sao_Paulo');
            $sent = DateTime::createFromFormat('Y-m-d H:i:s', $date->sentAt, $timezone);
            $now = new DateTime('now', $timezone);
            $interval = intval($now->format('U')) - intval($sent->format('U'));
            
            if($interval <= 600) {
                
                $timeLeft = ceil((600 - $interval)/60);
                
                return array(
                    'error' => true,
                    'code' => self::ERROR_CODE_ALREADY_SENT,
                    'timeLeft' => $timeLeft
                );
            }
        }
        
        $code = self::getRandomCode(128);

        $verificationCodeId = AccessDB::insertVerificationCode($user, $code);

        if($verificationCodeId === false) {
            return array(
                'error' => true,
                'code' => self::ERROR_SENDING_CODE,
            );
        }

        if(!Email::sendRecoverPasswordCode($user, $code)) {
            AccessDB::updateVerificationCodeStatus($verificationCodeId, 'NE');
            return array(
                'error' => true,
                'code' => self::ERROR_SENDING_CODE,
            );
        }

        return array(
            'error' => false,
        );
    }

    public static function createFormCode($page)
    {
        $code = self::getRandomCode(64);
        $_SESSION['form-codes'][$page] = $code;
        return $code;
    }

    public static function checkFormCode($code, $page)
    {
        if($_SESSION['form-codes'][$page] === $code) {
            return true;
        } else {
            return false;
        }
    }

    public static function removeFormCode($page)
    {
        if(isset($_SESSION['form-codes'][$page])) {
            unset($_SESSION['form-codes'][$page]);
        }
    }
}




