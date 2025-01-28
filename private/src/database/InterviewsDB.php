<?php 
namespace Database;

use Database\Database;
use App\Log;
Use PDO;
Use PDOException;

class InterviewsDB extends Database
{

    public static function getCandidateId($cpf)
    {
        try {
            $query = parent::connect()->prepare('SELECT `idCandidate` AS `id` FROM `CANDIDATES` WHERE `cpf` = :cpf LIMIT 1');
            $query->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function InterviewDB::getCandidateId.";
            $message .= " Candidate code: " . $cpf;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getCandidateInfo($id)
    {
        try {
            $query = parent::connect()->prepare('SELECT `idCandidate` AS `id`, `name`, `fullName`, `email`, `cpf`, `phone` FROM `CANDIDATES` WHERE `idCandidate` = :id LIMIT 1');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function InterviewDB::getCandidateInfo.";
            $message .= " Candidate id: " . $id;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getTimesSchedule($day)
    {
        try {
            $query = parent::connect()->prepare("SELECT it.`idInterviewTime` AS `id`, it.`datetime` AS `datetime` FROM `INTERVIEW_TIMES` it LEFT JOIN `INTERVIEW_SCHEDULES` isc ON (it.`idInterviewTime` = isc.`idInterviewTime`) WHERE isc.`idInterviewTime` IS NULL AND (`datetime` >= :day_ AND `datetime` < :day_ + INTERVAL 1 DAY) ORDER BY `datetime` ASC");
            $query->bindValue(':day_', $day, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function InterviewDB::getTimesSchedule.";
            $message .= " Date: " . $day;
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
            $query = parent::connect()->prepare('SELECT pv.`fullName` AS `name`, c.`shortName` AS `cup`, pv.`email` AS `email`, c.`username` AS `user`, pv.`role` AS `role`, pv.`cpf` AS `cpf` FROM `PRESENCE_IN_VOTINGS` pv INNER JOIN `CUPS` c ON (pv.`idCup` = c.`idCup`) INNER JOIN `VOTINGS` v ON (v.`idVoting` = pv.`idVoting`) WHERE v.`code` = :code ORDER BY c.`shortName` ASC');
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
