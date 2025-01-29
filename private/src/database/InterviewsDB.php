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

    public static function getTimeInfo($id)
    {
        try {
            $query = parent::connect()->prepare("SELECT `idInterviewTime` AS `id`, `datetime`, `meet` FROM `INTERVIEW_TIMES` WHERE `idInterviewTime` = :id LIMIT 1");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function InterviewDB::getTimeInfo.";
            $message .= " ID Time: " . $id;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function getInterviewTimeId($id)
    {
        try {
            $query = parent::connect()->prepare("SELECT `idInterviewTime` AS `id` FROM `INTERVIEW_SCHEDULES` WHERE `idCandidate` = :id LIMIT 1");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function InterviewDB::getTimeInfo.";
            $message .= " ID Time: " . $id;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function insertInterviewSchedule($idCandidate, $idTime)
    {
        try {
            $connection = parent::connect();

            $query = $connection->prepare('INSERT INTO `INTERVIEW_SCHEDULES` (`idInterviewTime`, `idCandidate`, `createdAt`, `updatedAt`) VALUES(:idTime, :idCandidate, NOW(), NOW())');
            $query->bindValue(':idTime', $idTime, PDO::PARAM_INT);
            $query->bindValue(':idCandidate', $idCandidate, PDO::PARAM_INT);
            $query->execute();
            
            parent::disconnect();
            
            return (Object)array('error' => false);

        } catch (PDOException $exception) {
            $message = "Error in function InterviewDB::insertInterviewSchedule.";
            $message .= " idCandidate: " . $idCandidate . " idTime: " . $idTime;
            Log::error($message, 'database.log', $exception->getMessage());
            
            parent::disconnect();
            
            return (Object)array('error' => true, 'errorCode' => 'FAILURE_INSERT');
        }
    }

}
