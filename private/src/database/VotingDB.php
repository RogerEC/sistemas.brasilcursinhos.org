<?php 
namespace Database;

use Database\Database;
use App\Log;
Use PDO;
Use PDOException;

class VotingDB extends Database
{

    public static function getVoting($code)
    {
        try {
            $query = parent::connect()->prepare('SELECT v.`idVoting` AS `id`, v.`name`, v.`description`, v.`datetime`, s.`code` AS `status`, v.`code` AS `code` FROM `VOTINGS` v INNER JOIN `STATUS` s ON (v.`idStatus` = s.`idStatus`) WHERE v.`code` = :code LIMIT 1');
            $query->bindValue(':code', $code, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function VotingDB::getVoting.";
            $message .= " Voting code: " . $code;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getCups()
    {
        try {
            $query = parent::connect()->prepare('SELECT c.`idCup` AS `id`, c.`shortName` AS `name` FROM `CUPS` c INNER JOIN `STATUS` s ON (c.`idStatus` = s.`idStatus`) WHERE s.`code` = "A" ORDER BY c.`shortName` ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function VotingDB::getCups.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function insertPresenceVoting($votingId, $data)
    {
        try {
            $connection = parent::connect();

            $query = $connection->prepare('SELECT `idPresenceInVoting`, `fullName` AS `name`, `email`, `cpf`, `role` FROM `PRESENCE_IN_VOTINGS` WHERE `idCup` = :idCup AND `idVoting` = :idVoting LIMIT 1');
            $query->bindValue(':idCup', $data->cup, PDO::PARAM_INT);
            $query->bindValue(':idVoting', $votingId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if($result) {
                $result['error'] = true;
                $result['errorCode'] = 'DUPLICATE';
                return (Object)$result;
            }

            $query = $connection->prepare('SELECT `idPresenceInVoting`, `fullName` AS `name`, `email`, `cpf`, `role` FROM `PRESENCE_IN_VOTINGS` WHERE (`cpf` = :cpf OR `email` = :email) AND `idVoting` = :idVoting LIMIT 1');
            $query->bindValue(':cpf', $data->cpf, PDO::PARAM_STR);
            $query->bindValue(':email', $data->email, PDO::PARAM_STR);
            $query->bindValue(':idVoting', $votingId, PDO::PARAM_INT);
            $query->execute();
            $result2 = $query->fetch(PDO::FETCH_ASSOC);

            if($result2) {
                $result2['error'] = true;
                $result2['errorCode'] = 'DUPLICATE_CPF';
                return (Object)$result2;
            }

            $query = $connection->prepare('INSERT INTO `PRESENCE_IN_VOTINGS` (`fullName`, `cpf`, `email`, `role`, `idCup`, `idVoting`, `createdAt`, `updatedAt`) VALUES(:name_, :cpf, :email, :role_, :idCup, :idVoting, NOW(), NOW())');
            $query->bindValue(':name_', $data->name, PDO::PARAM_STR);
            $query->bindValue(':cpf', $data->cpf, PDO::PARAM_STR);
            $query->bindValue(':email', $data->email, PDO::PARAM_STR);
            $query->bindValue(':role_', $data->role, PDO::PARAM_STR);
            $query->bindValue(':idCup', $data->cup, PDO::PARAM_INT);
            $query->bindValue(':idVoting', $votingId, PDO::PARAM_INT);
            $query->execute();
            
            parent::disconnect();
            
            return (Object)array('error' => false);

        } catch (PDOException $exception) {
            $message = "Error in function VotingDB::insertPresenceVoting.";
            $message .= " VotingID: " . $votingId . " Data: " . var_dump($data);
            Log::error($message, 'database.log', $exception->getMessage());
            
            parent::disconnect();
            
            return (Object)array('error' => true, 'errorCode' => 'FAILURE_INSERT');
        }
    }

    public static function getVotings()
    {
        try {
            $query = parent::connect()->prepare('SELECT v.`code` AS `id`, v.`name`, v.`datetime` FROM `VOTINGS` v ORDER BY v.`datetime` DESC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function VotingDB::getVotings.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getPrecenceInVoting($code)
    {
        try {
            $query = parent::connect()->prepare('SELECT pv.`fullName` AS `name`, c.`shortName` AS `cup`, pv.`email` AS `email`, c.`username` AS `user`, pv.`role` AS `role` FROM `PRESENCE_IN_VOTINGS` pv INNER JOIN `CUPS` c ON (pv.`idCup` = c.`idCup`) INNER JOIN `VOTINGS` v ON (v.`idVoting` = pv.`idVoting`) WHERE v.`code` = :code');
            $query->bindValue(':code', $code, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function VotingDB::getVotings.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    
}
