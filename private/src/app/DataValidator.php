<?php
namespace App;

use DateTime;
use DateTimeZone;

class DataValidator 
{
    private static $post;
    //private $file;

    private static function post($input)
    {
        if(!empty(self::$post)) {
            return (isset(self::$post[$input]))? self::$post[$input]:null;
        }
        return null;
    }
    
    public static function validateCpf($cpf)
    {
        $cpf = filter_var(preg_replace( '/[^0-9]/is', '', $cpf), FILTER_SANITIZE_NUMBER_INT);
        if(strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return $cpf;
    }

    public static function isNumber($number)
    {
        for($i = 0; $i < strlen($number); $i++) {
            if(!(is_numeric($number[$i]) || $number[$i] === '-' || $number[$i] === '.')) {
                return false;
            }
        }
        if(!empty($number)) {
            return true;
        }
    }

    public static function isAlphaNum($string)
    {
        for($i = 0; $i < strlen($string); $i++) {
            if(!(ctype_alnum($string[$i]) || $string[$i] === '_' || $string[$i] === '.')) {
                return false;
            }
        }
        if(!empty($string)) {
            return true;
        }
    }

    public static function validateNumber($number)
    {
        $number = filter_var(preg_replace('/[^0-9]/is', '', $number), FILTER_SANITIZE_NUMBER_INT);
        return (!empty($number))? $number:false;
    }

    public static function validateRegistrationNumber($number)
    {
        $number = filter_var(preg_replace('/[^0-9]/is', '', $number), FILTER_SANITIZE_NUMBER_INT);
        return (strlen($number) === 8)? $number:false;
    }

    public static function validateInt($int)
    {
        $int = self::validateNumber($int);
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    public static function validateEmail($email)
    {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateString($string)
    {
        return (!empty(trim($string)))? trim($string):'';
    }

    public static function validateDatetime($date, $inputFormat = 'Y-m-d\TH:i', $outputFormat = 'Y-m-d H:i:s')
    {
        $date = self::validateString($date);
        if(empty($date)) return false;
        $timezone = new DateTimeZone('America/Sao_Paulo');
        $datetime = DateTime::createFromFormat($inputFormat, $date, $timezone);
    
        return ($datetime !== false)? $datetime->format($outputFormat):false;
    }

    public static function validateUserLoginData($data) 
    {
        $email = self::validateEmail($data);
        if($email !== false) {
            return (Object)['error' => false, 'type' => 'email', 'data' => $email];
        }
        
        $cpf = self::validateCpf($data);
        if($cpf !== false) {
            return (Object)['error' => false, 'type' => 'cpf', 'data' => $cpf];
        }

        $registration = self::validateRegistrationNumber($data);
        if($registration !== false) {
            return (Object)['error' => false, 'type' => 'registration', 'data' => $registration];
        }

        return (Object)['error' => true, 'type' => '', 'data' => $data];
    }

    public static function validateActivity($post) 
    {   
        $data = array('error' => false);
        $data['name'] = self::validateString($post['name']);
        $data['local'] = self::validateString($post['local']);
        $data['start'] = self::validateDatetime($post['start']);
        $data['final'] = self::validateDatetime($post['final']);
        
        if(isset($post['id'])) {
            
            $data['id'] = self::validateInt($post['id']);
            if($data['id'] === false) {
                $data['error'] = true;
            }
        }

        if(empty($data['name']) || empty($data['local']) || $data['error'] === true ||
        $data['start'] === false || $data['final'] === false) {
            $data['error'] = true;
        } else {
            $data['error'] = false;
        }
        return (Object)$data;
    }

    public static function validateParticipant($post) 
    {   
        self::$post = $post;
        $data = array('error' => false);
        $data['name'] = self::validateString(self::post('name'));
        $data['cup'] = self::validateString(self::post('cup'));
        $data['cpf'] = self::validateCpf(self::post('cpf'));

        if(isset($post['id'])) {
            
            $data['id'] = self::validateInt($post['id']);
            if($data['id'] === false) {
                $data['error'] = true;
            }
        }

        if(empty($data['name']) || empty($data['cup']) || 
        $data['cpf'] === false || $data['error'] === true) {
            $data['error'] = true;
        } else {
            $data['error'] = false;
        }
        return (Object)$data;
    }

    public static function validatePresenceVoting($post) 
    {   
        self::$post = $post;
        $data = array('error' => false);
        $data['name'] = self::validateString(self::post('name'));
        $data['cup'] = self::validateInt(self::post('cup'));
        $data['cpf'] = self::validateCpf(self::post('cpf'));
        $data['email'] = self::validateEmail(self::post('email'));
        $data['role'] = self::validateString(self::post('role'));

        if(empty($data['name']) || $data['cup'] === false || empty($data['role']) ||
        $data['cpf'] === false || $data['email'] === false) {
            $data['error'] = true;
        } else {
            $data['error'] = false;
        }
        return (Object)$data;
    }
}