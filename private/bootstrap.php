<?php
    
    ini_set('default_charset', 'utf-8');
    
    ini_set('session.cookie_lifetime', 0);
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_trans_sid', 0);
    ini_set('session.sid_length', 48);
    ini_set('session.sid_bits_per_character', 6);
    ini_set('session.cache_limiter', 'nocache');
    
    header_remove('x-powered-by');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Content-Security-Policy: upgrade-insecure-requests;default-src https:;img-src https: data:');
     
    require __DIR__ . '/vendor/autoload.php';

    if(DEBUG_MODE) {
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        ini_set('error_log', DIR_LOGS . 'php.log');
        ini_set('log_errors', 1);
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', false);
        ini_set('error_log', DIR_LOGS . 'php.log');
        ini_set('log_errors', 1);
    }

    session_name("BCSESSIONID");
    session_start();
    session_regenerate_id();
     
    try {
     
        require __DIR__ . '/routes/routes.php';
     
    } catch(\Exception $exeption){
         
        echo $exeption->getMessage();
    }
?>