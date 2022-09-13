<?php 
namespace Database;

use Database\Database;
use App\Log;
Use PDO;
Use PDOException;

class EventDB extends Database
{
    public static function getEventStatus()
    {   
        try {
            $query = parent::connect()->prepare('SELECT `idStatus` AS `id`, `code`, `name` FROM `status` WHERE `role` = "EVENT"');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch(PDOException $exception) {
            $message = "Error in function EventDB::getEventStatus.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
        
    }
    
    public static function getEventsData()
    {   
        try {
            $query = parent::connect()->prepare('SELECT `idEvent` AS `id`, `name`, `startDate`, `finalDate`, `idStatus` AS `status` FROM `EVENTS`');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function EventDB::getEventsData.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
    }

    public static function getActivitiesData()
    {
        try {
            $query = parent::connect()->prepare('SELECT `idEventActivity` AS `id`, `name`, `local`, DATE_FORMAT(`startDatetime`, "%Y-%m-%dT%H:%i") AS `start`, DATE_FORMAT(`finalDatetime`, "%Y-%m-%dT%H:%i") AS `final` FROM `EVENT_ACTIVITIES` ORDER BY `startDatetime` ASC, `name` ASC');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function EventDB::getActivitiesData.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
    }

    public static function getParticipantsData()
    {
        try {
            $query = parent::connect()->prepare('SELECT `idEventParticipant` AS `id`, `name`, `cpf`, `cup` FROM `EVENT_PARTICIPANTS`');
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function EventDB::getParticipantsData.";
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
    }

    public static function deleteActivity($id)
    {
        try {
            $query = parent::connect()->prepare('DELETE FROM `EVENT_ACTIVITIES` WHERE `idEventActivity` = :idEventActivity LIMIT 1');
            $query->bindValue(':idEventActivity', $id, PDO::PARAM_INT);
            $query->execute();
            parent::disconnect();
            return true;
        } catch (PDOException $exception) {
            $message = "Error in function EventDB::deleteActivity.";
            $message .= " Event Activity ID: " . $id;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function deleteParticipant($id)
    {
        try {
            $query = parent::connect()->prepare('DELETE FROM `EVENT_PARTICIPANTS` WHERE `idEventParticipant` = :idEventParticipant LIMIT 1');
            $query->bindValue(':idEventParticipant', $id, PDO::PARAM_INT);
            $query->execute();
            parent::disconnect();
            return true;
        } catch (PDOException $exception) {
            $message = "Error in function EventDB::deleteParticipant.";
            $message .= " Event Participant ID: " . $id;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function insertActivity($data) 
    {
        try {
            $query = parent::connect()->prepare('INSERT INTO `EVENT_ACTIVITIES`(`name`, `local`, `startDatetime`, `finalDatetime`, `createdAt`, `updatedAt`) VALUES (:name_, :local_, :startDatetime, :finalDatetime, NOW(), NOW())');
            $query->bindValue(':name_', $data->name, PDO::PARAM_STR);
            $query->bindValue(':local_', $data->local, PDO::PARAM_STR);
            $query->bindValue(':startDatetime', $data->start, PDO::PARAM_STR);
            $query->bindValue(':finalDatetime', $data->final, PDO::PARAM_STR);
            $query->execute();
            parent::disconnect();
            return true;

        } catch (PDOException $exception) {
            
            $message = "Error in function EventDB::insertActivity.";
            $message .= " Name: " . $data->name . " | Local: " . $data->local;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function insertParticipant($data) 
    {
        try {
            $query = parent::connect()->prepare('INSERT INTO `EVENT_PARTICIPANTS`(`name`, `cpf`, `cup`, `createdAt`, `updatedAt`) VALUES (:name_, :cpf, :cup, NOW(), NOW())');
            $query->bindValue(':name_', $data->name, PDO::PARAM_STR);
            $query->bindValue(':cpf', $data->cpf, PDO::PARAM_STR);
            $query->bindValue(':cup', $data->cup, PDO::PARAM_STR);
            $query->execute();
            parent::disconnect();
            return true;

        } catch (PDOException $exception) {
            
            $message = "Error in function EventDB::insertParticipant.";
            $message .= " Name: " . $data->name . " | CPF: " . $data->cpf . " | CUP: " . $data->cup;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function updateActivity($data) 
    {
        try {
            $query = parent::connect()->prepare('UPDATE `EVENT_ACTIVITIES` SET `name` = :name_, `local` = :local_, `startDatetime` = :startDatetime, `finalDatetime` = :finalDatetime, `updatedAt` = NOW() WHERE `idEventActivity` = :idEventActivity LIMIT 1');
            $query->bindValue(':name_', $data->name, PDO::PARAM_STR);
            $query->bindValue(':local_', $data->local, PDO::PARAM_STR);
            $query->bindValue(':startDatetime', $data->start, PDO::PARAM_STR);
            $query->bindValue(':finalDatetime', $data->final, PDO::PARAM_STR);
            $query->bindValue(':idEventActivity', $data->id, PDO::PARAM_INT);
            $query->execute();
            parent::disconnect();
            return true;

        } catch (PDOException $exception) {
            
            $message = "Error in function EventDB::updateActivity.";
            $message .= " Name: " . $data->name . " | Local: " . $data->local;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function updateParticipant($data) 
    {
        try {
            $query = parent::connect()->prepare('UPDATE `EVENT_PARTICIPANTS` SET `name` = :name_, `cpf` = :cpf, `cup` = :cup, `updatedAt` = NOW() WHERE `idEventParticipant` = :idEventParticipant LIMIT 1');
            $query->bindValue(':name_', $data->name, PDO::PARAM_STR);
            $query->bindValue(':cpf', $data->cpf, PDO::PARAM_STR);
            $query->bindValue(':cup', $data->cup, PDO::PARAM_STR);
            $query->bindValue(':idEventParticipant', $data->id, PDO::PARAM_INT);
            $query->execute();
            parent::disconnect();
            return true;

        } catch (PDOException $exception) {
            
            $message = "Error in function EventDB::updateParticipant.";
            $message .= " Name: " . $data->name . " | CPF: " . $data->cpf . " | CUP: " . $data->cup;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return false;
        }
    }

    public static function checkParticipantCpf($cpf)
    {
        try {
            $query = parent::connect()->prepare('SELECT `name` FROM `EVENT_PARTICIPANTS` WHERE `cpf` = :cpf LIMIT 1');
            $query->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            parent::disconnect();
            return $result;
        } catch (PDOException $exception) {
            $message = "Error in function EventDB::checkParticipantCpf. CPF: " . $cpf;
            Log::error($message, 'database.log', $exception->getMessage());
            parent::disconnect();
            return null;
        }
    }
}
