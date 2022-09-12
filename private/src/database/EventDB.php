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
            $query = parent::connect()->prepare('SELECT `idEventParticipant` AS `id`, `name`, `email`, `cpf`, `cup` FROM `EVENT_PARTICIPANTS`');
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
}
