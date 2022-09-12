<?php
    include_once __DIR__ . '/local-settings.php';
    
    define('DIR_TEMPLATES', realpath(__DIR__ . '/../src/view/templates/') . '/');
    define('DIR_PUBLIC_HTML', realpath(__DIR__ . '/../../public_html/') . '/');
    define('DIR_PASSWORDS', realpath(__DIR__ . '/passwords/') . '/');
    define('DIR_PRIVATE_DOCUMENTS', realpath(__DIR__ . '/../documents/') . '/');
    define('DIR_LOGS', realpath(__DIR__ . '/../logs/') . '/');
