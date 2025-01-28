<?php
    
    ini_set('default_charset', 'utf-8');
    locale_set_default('pt_BR.utf8');
    ini_set('date.timezone', 'America/Sao_Paulo');
    
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

    session_name("BC" . strtoupper(WEBSITE_NAME) . "SESSIONID");
    session_start();
    //session_regenerate_id();

    error_reporting(E_ALL);
    ini_set('error_log', realpath(__DIR__ . '/logs/') . '/' .WEBSITE_NAME . 'php.log');
    ini_set('log_errors', 1);
    ini_set('display_errors', false);
     
    require __DIR__ . '/vendor/autoload.php';

    if(DEBUG_MODE) {
        header_remove('Strict-Transport-Security');
        header('Strict-Transport-Security: max-age=0; includeSubDomains');
        ini_set('display_errors', true);
    }
     
    try {
        
        if(WEBSITE_NAME === 'sistemas') {
            
            require __DIR__ . '/routes/routes-sistemas.php';

        } else if (WEBSITE_NAME === 'encup') {
            
            require __DIR__ . '/routes/routes-encup.php';

        } else if (WEBSITE_NAME === 'calendario') {
            
            require __DIR__ . '/routes/routes-calendario.php';
        }

    } catch(\Exception $exeption){
         
        echo $exeption->getMessage();
    }
?>