<?php 
namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email {

    private static $mail;

    public function __construct($parameters = [])
    {
        self::initMail($parameters);
    }

    private static function initMail($parameters = [])
    {
        if(is_null(self::$mail)) {
            try {
                require_once DIR_PASSWORDS . '/email.php';

                self::$mail = new PHPMailer(true);

                self::$mail->isSMTP();
                self::$mail->isHTML(true);
                self::$mail->SMTPDebug  = (isset($parameters['DEBUG']))? $parameters['DEBUG']:SMTP::DEBUG_OFF;
                self::$mail->Host       = (isset($parameters['SMTP_HOST']))? $parameters['SMTP_HOST']:EMAIL_SMTP_HOST;
                self::$mail->SMTPAuth   = (isset($parameters['SMTP_AUTH']))? $parameters['SMTP_AUTH']:true;
                self::$mail->Username   = (isset($parameters['EMAIL_ADDRESS']))? $parameters['EMAIL_ADDRESS']:EMAIL_ADDRESS;
                self::$mail->Password   = (isset($parameters['EMAIL_PASSWORD']))? $parameters['EMAIL_PASSWORD']:EMAIL_PASSWORD;
                self::$mail->SMTPSecure = (isset($parameters['SMTP_SECURE']))? $parameters['SMTP_SECURE']:PHPMailer::ENCRYPTION_STARTTLS;
                self::$mail->Port       = (isset($parameters['SMTP_PORT']))? $parameters['SMTP_PORT']:EMAIL_SMTP_PORT;
                self::$mail->CharSet    = (isset($parameters['CHARSET']))? $parameters['CHARSET']:PHPMailer::CHARSET_UTF8;
        
            
                self::$mail->setFrom((isset($parameters['EMAIL_ADDRESS']))? $parameters['EMAIL_ADDRESS']:EMAIL_ADDRESS,
                               (isset($parameters['EMAIL_NAME']))? $parameters['EMAIL_NAME']:EMAIL_NAME);
                
                return true;
            } catch(Exception $exception) {
                $message = 'Error starting email.';
                Log::error($message, 'email.log', $exception->getMessage());
                self::$mail = null;
                return false;
            }
        } else {
            return true;
        }
    }

    public static function send($email, $parameters = [])
    {
        if(!self::initMail($parameters)) {
            return false;
        }
        
        try {

            self::$mail->addAddress($email->address, (isset($email->name))? $email->name:'');
            
            if (isset($email->replyToAddress)) {
                self::$mail->addReplyTo($email->replyToAddress, (isset($email->replyToName))? $email->replyToName:'');
            }
            if (isset($email->ccAddress)) {
                self::$mail->addCC($email->ccAddress, (isset($email->ccName))? $email->ccName:'');
            }
            if (isset($email->bccAddress)) {
                self::$mail->addBCC($email->bccAddress, (isset($email->bccName))? $email->bccName:'');
            }
            
            self::$mail->Subject = $email->subject;
            self::$mail->Body    = $email->html;
            self::$mail->AltBody = $email->text;
        
            self::$mail->send();

            self::$mail = null;

            return true;

        } catch(Exception $exception) {
            
            $message = 'Error sending email. ';
            $message .= 'Subject: ' . $email->subject . ' Address: ' . $email->address;
            Log::error($message, 'email.log', $exception->getMessage());
            
            self::$mail = null;
            
            return false;
        }
    }

    public static function sendRecoverPasswordCode($user, $code)
    {
        $html = Page::getRender('@emails/html/recover-password.html', ['user' => $user, 'code' => $code]);
        $text = Page::getRender('@emails/text/recover-password.html', ['user' => $user, 'code' => $code]);
        $email = array('subject' => 'Recuperação de Senha - Brasil Cursinhos',
                       'html' => $html, 'text' => $text, 'replyToAddress' => 'suporte@pes.ufsc.br',
                       'address' => $user->email, 'name' => $user->fullName);
        return self::send((Object)$email, ['DUBUG' => 4]);
    }

    
}