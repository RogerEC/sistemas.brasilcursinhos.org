<?php 
namespace Database;

use Database\Database;
use App\Log;
Use PDO;
Use PDOException;

class AccessDB extends Database
{
    
    public static function getUserData($userData, $type)
    {
        if($type === 'cpf') {
            
            $query = parent::connect()->prepare('SELECT u.`idUser`, u.`passwordHash`, u.`lastAccess`, u.`accessAttempts`, t.`code` AS `type`, s.`code` AS `status` FROM `USERS` u INNER JOIN `TYPES` t ON (u.`idType` = t.`idType`) INNER JOIN `STATUS` s ON (u.`idStatus` = s.`idStatus`) WHERE u.`cpf` = :cpf LIMIT 1');
            $query->bindValue(':cpf', $userData, PDO::PARAM_STR);

        } else if ($type === 'email') {
            
            $query = parent::connect()->prepare('SELECT u.`idUser`, u.`passwordHash`,  t.`code` AS `type`, s.`code` AS `status` FROM `USERS` u INNER JOIN `TYPES` t ON (u.`idType` = t.`idType`) INNER JOIN `STATUS` s ON (u.`idStatus` = s.`idStatus`) WHERE u.`email` = :email LIMIT 1');
            $query->bindValue(':email', $userData, PDO::PARAM_STR);

        } else if ($type === 'registration') {
            
            $query = parent::connect()->prepare('SELECT u.`idUser`, u.`passwordHash`, u.`lastAccess`, u.`accessAttempts`, t.`code` AS `type`, s.`code` AS `status` FROM `USERS` u INNER JOIN `TYPES` t ON (u.`idType` = t.`idType`) INNER JOIN `STATUS` s ON (u.`idStatus` = s.`idStatus`) WHERE u.`registration` = :registration LIMIT 1');
            $query->bindValue(':registration', $userData, PDO::PARAM_STR);

        } else if ($type === 'username') {
            
            $query = parent::connect()->prepare('SELECT u.`idUser`, u.`passwordHash`, u.`lastAccess`, u.`accessAttempts`, t.`code` AS `type`, s.`code` AS `status` FROM `USERS` u INNER JOIN `TYPES` t ON (u.`idType` = t.`idType`) INNER JOIN `STATUS` s ON (u.`idStatus` = s.`idStatus`) WHERE u.`username` = :username LIMIT 1');
            $query->bindValue(':username', $userData, PDO::PARAM_STR);

        } else {

            return null;
        }
        
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        parent::disconnect();
        
        return $result;
    }

    public static function updatePasswordHash($userId, $newHash) {
        $query = parent::connect()->prepare('UPDATE `USERS` SET `passwordHash` = :newHash, `updatedAt` = NOW() WHERE `idUser` = :userId LIMIT 1');
        $query->bindValue(':newHash', $newHash, PDO::PARAM_STR);
        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->rowCount();
        parent::disconnect();
        return $result;
    }

    public static function updateAccessLog($userData, $status, $blockedStatusCode = 'B')
    {
        $ipAddress = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

        try {
            if($status) {
                
                $query = parent::connect()->prepare('UPDATE `USERS` SET `lastIpAddress` = INET6_ATON(:ipAddress), `accessAttempts` = 0, `lastAccess` = NOW() WHERE `idUser` = :idUser LIMIT 1');
                $query->bindValue(':ipAddress', $ipAddress, PDO::PARAM_STR);
                $query->bindValue(':idUser', $userData->idUser, PDO::PARAM_INT);

            } else {
                if($userData->accessAttempts >= 5) {
                    
                    $query = parent::connect()->prepare('UPDATE `USERS` SET `idStatus` = (SELECT `idStatus` FROM `STATUS` WHERE `code` = :statusCode and `role` = "USER"), `lastIpAddress` = INET6_ATON(:ipAddress), `accessAttempts` = 0, `lastAccess` = NOW() WHERE `idUser` = :idUser LIMIT 1');
                    $query->bindValue(':ipAddress', $ipAddress, PDO::PARAM_STR);
                    $query->bindValue(':statusCode', $blockedStatusCode, PDO::PARAM_STR);
                    $query->bindValue(':idUser', $userData->idUser, PDO::PARAM_INT);

                } else {
                    
                    $query = parent::connect()->prepare('UPDATE `USERS` SET `lastIpAddress` = INET6_ATON(:ipAddress), `accessAttempts` = :accessAttempts, `lastAccess` = NOW() WHERE `idUser` = :idUser LIMIT 1');
                    $query->bindValue(':ipAddress', $ipAddress, PDO::PARAM_STR);
                    $query->bindValue(':accessAttempts', $userData->accessAttempts+1, PDO::PARAM_INT);
                    $query->bindValue(':idUser', $userData->idUser, PDO::PARAM_INT);
                }
            }
            $query->execute();
            parent::disconnect();
            return true;
        } catch(PDOException $exception) {
            $message = "Erro ao atualizar log de acesso ao site.";
            $message .= ' ID Usuário: ' . $userData->userId;
            $message .= ' | Status: ' . (($status)? 'SUCESSO':'FALHA');
            $message .= ' | Tentativa de acesso: ' . $userData->accessAttempts;
            $message .= ' | Endereço de IP: ' . $ipAddress; 
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getUserEmail($cpf) {
        
        try {
            
            $query = parent::connect()->prepare('SELECT u.`idUser` AS `id`, pi.`firstName` AS `name`, CONCAT(pi.`firstName`, " ", pi.`lastName`) AS `fullName`, u.`email`, u.`registration`, s.`code` AS `status`  FROM `USERS` u INNER JOIN `STATUS` s ON (u.`idStatus` = s.`idStatus`) INNER JOIN `PERSONAL_INFORMATIONS` pi ON (u.`idUser` = pi.`idUser`) WHERE u.`cpf` = :cpf LIMIT 1');
            $query->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $query->execute();
            parent::disconnect();
            return $query->fetch(PDO::FETCH_OBJ);

        } catch (PDOException $exception) {
            
            $message = "Error in function AccessDB::getUserEmail.";
            $message .= " USER: " . $cpf;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function insertVerificationCode($user, $code, $sentStatusCode = 'E') 
    {
        try {
            $query = parent::connect()->prepare('INSERT INTO `VERIFICATION_CODES`(`code`, `sentAt`, `idStatus`, `idUser`) VALUES (:code, NOW(), (SELECT `idStatus` FROM `STATUS` WHERE `code` = :sentStatusCode AND `role` = "VERIFICATION_CODE"), :idUser)');
            $query->bindValue(':code', $code, PDO::PARAM_STR);
            $query->bindValue(':sentStatusCode', $sentStatusCode, PDO::PARAM_STR);
            $query->bindValue(':idUser', $user->id, PDO::PARAM_STR);
            $query->execute();
            $id = parent::connect()->lastInsertId();
            parent::disconnect();
            return $id;

        } catch (PDOException $exception) {
            
            $message = "Error in function AccessDB::insertVerificationCode.";
            $message .= " User ID: " . $user->id . " | Code: " . $code;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getLastSentVerificationCode($user, $sentStatusCode = 'E')
    {
        try {
            
            $query = parent::connect()->prepare('SELECT `sentAt` FROM `VERIFICATION_CODES` WHERE `idUser` = :idUser AND `idStatus` = (SELECT `idStatus` FROM `STATUS` WHERE `code` = :sentStatusCode AND `role` = "VERIFICATION_CODE") ORDER BY `sentAt` DESC LIMIT 1');
            $query->bindValue(':idUser', $user->id, PDO::PARAM_INT);
            $query->bindValue(':sentStatusCode', $sentStatusCode, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
        
            return $result;

        } catch (PDOException $exception) {
            
            $message = "Error in function AccessDB::getLastSentVerificationCode.";
            $message .= " User ID: " . $user->id;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
    }

    public static function updateVerificationCodeStatus($id, $statusCode) 
    {
        try {
            
            $query = parent::connect()->prepare('UPDATE `VERIFICATION_CODES` SET `idStatus` = (SELECT `idStatus` FROM `STATUS` WHERE `code` = :statusCode AND `role` = "VERIFICATION_CODE"), `confirmedAt` = NOW() WHERE `idVerificationCode` = :idVerificationCode LIMIT 1');
            $query->bindValue(':idVerificationCode', $id, PDO::PARAM_INT);
            $query->bindValue(':statusCode', $statusCode, PDO::PARAM_STR);
            $query->execute();
            parent::disconnect();
        
            return true;

        } catch (PDOException $exception) {
            
            $message = "Error in function AccessDB::updateVerificationCodeStatus";
            $message .= " Verification Code ID: " . $id . ' Status Code ID: '. $statusCode;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getVerificationCode($user, $code)
    {
        try {
            
            $query = parent::connect()->prepare('SELECT vc.`idVerificationCode` AS `id`, vc.`sentAt`, s.`code` AS `status` FROM `VERIFICATION_CODES` vc INNER JOIN `USERS` u ON (vc.`idUser` = u.`idUser`) INNER JOIN `STATUS` s ON (vc.`idStatus` = s.`idStatus`) WHERE u.`registration` = :registration AND vc.`code` = :verificationCode LIMIT 1');
            $query->bindValue(':registration', $user, PDO::PARAM_STR);
            $query->bindValue(':verificationCode', $code, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
        
            return $result;

        } catch (PDOException $exception) {
            
            $message = "Error in function AccessDB::getVerificationCode.";
            $message .= " User ID: " . $user . ' Verification Code: ' . $code;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
    }
}
