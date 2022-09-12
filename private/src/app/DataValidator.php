<?php
namespace App;

use DateTime;
use DateTimeZone;

class DataValidator 
{
    private static $post;
    //private $file;

    private static function post($input)
    {
        if(!empty(self::$post)) {
            return (isset(self::$post[$input]))? self::$post[$input]:null;
        }
        return null;
    }
    
    public static function validateCpf($cpf)
    {
        $cpf = filter_var(preg_replace( '/[^0-9]/is', '', $cpf), FILTER_SANITIZE_NUMBER_INT);
        if(strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return $cpf;
    }

    public static function isNumber($number)
    {
        for($i = 0; $i < strlen($number); $i++) {
            if(!(is_numeric($number[$i]) || $number[$i] === '-' || $number[$i] === '.')) {
                return false;
            }
        }
        if(!empty($number)) {
            return true;
        }
    }

    public static function isAlphaNum($string)
    {
        for($i = 0; $i < strlen($string); $i++) {
            if(!(ctype_alnum($string[$i]) || $string[$i] === '_' || $string[$i] === '.')) {
                return false;
            }
        }
        if(!empty($string)) {
            return true;
        }
    }

    public static function validateNumber($number)
    {
        $number = filter_var(preg_replace('/[^0-9]/is', '', $number), FILTER_SANITIZE_NUMBER_INT);
        return (!empty($number))? $number:false;
    }

    public static function validateRegistrationNumber($number)
    {
        $number = filter_var(preg_replace('/[^0-9]/is', '', $number), FILTER_SANITIZE_NUMBER_INT);
        return (strlen($number) === 8)? $number:false;
    }

    public static function validateInt($int)
    {
        $int = self::validateNumber($int);
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    public static function validateEmail($email)
    {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateString($string)
    {
        return (!empty(trim($string)))? trim($string):'';
    }

    public static function validateDatetime($date, $format = 'Y-m-d H:i:s')
    {
        $date = self::validateString($date);
        if($date == '') return false;
        $timezone = new DateTimeZone('America/Sao_Paulo');
        $datetime = DateTime::createFromFormat($format, $date, $timezone);
            
        return ($date !== false && !array_sum($datetime->getLastErrors()))? $datetime->format($format):false;
    }

    public static function validateUserLoginData($data) 
    {
        $email = self::validateEmail($data);
        if($email !== false) {
            return (Object)['error' => false, 'type' => 'email', 'data' => $email];
        }
        
        $cpf = self::validateCpf($data);
        if($cpf !== false) {
            return (Object)['error' => false, 'type' => 'cpf', 'data' => $cpf];
        }

        $registration = self::validateRegistrationNumber($data);
        if($registration !== false) {
            return (Object)['error' => false, 'type' => 'registration', 'data' => $registration];
        }

        return (Object)['error' => true, 'type' => '', 'data' => $data];
    }

    public static function validateManagerCandidateData($post) {

        self::$post = $post;

        $data = array();
        $errorLog = array();
        $motivation = array();
        $availableTimes = array();
        $oldProjects = array();
        $currentProjects = array();
        //$divulgation = array(); // Não utilizado no momento

        $data['firstName'] = self::validateString(self::post('first-name'));
        if($data['firstName'] === '') $errorLog[] = 'Nome inválido ou ausente';
        $data['lastName'] = self::validateString(self::post('last-name'));
        if($data['lastName'] === '') $errorLog[] = 'Sobrenome inválido ou ausente';
        $data['sex'] = self::validateString(self::post('sex'));
        if($data['sex'] === '') $errorLog[] = 'Sexo inválido ou ausente';
        $data['birthDate'] = self::validateDatetime(self::post('birth-date'), 'Y-m-d');
        $data['cpf'] = self::validateCpf(self::post('cpf'));
        if(!$data['birthDate']) $errorLog[] = 'Data de nascimento inválida ou ausente';
        $data['email'] = self::validateEmail(self::post('email'));
        $data['phone'] = self::validateNumber(self::post('phone'));
        $data['parentGuardianName'] = self::validateString(self::post('parent-guardian-name'));
        $data['parentGuardianPhone'] = self::validateString(self::post('parent-guardian-phone'));
        $data['parentGuardianRelationship'] = self::validateString(self::post('parent-guardian-relationship'));
        $data['parentGuardianCpf'] = self::validateString(self::post('parent-guardian-cpf'));
        $data['firstOption'] = self::validateString(self::post('first-option'));
        $data['secondOption'] = self::validateString(self::post('second-option'));
        $data['thirdOption'] = self::validateString(self::post('third-option'));
        $motivation['becauseSector'] = self::validateString(self::post('motivation-because-sector'));
        $motivation['becausePes'] = self::validateString(self::post('motivation-because-pes'));
        $motivation['howHelp'] = self::validateString(self::post('motivation-how-help'));
        $motivation['becauseEducation'] = self::validateString(self::post('motivation-because-education'));
        $data['motivation'] = json_encode($motivation, JSON_UNESCAPED_UNICODE);
        $data['weeklyAvailability'] = self::validateString(self::post('weekly-availability'));
        $availableTimes['mon01'] = (isset($post['mon01']) && trim($post['mon01'])==='Disponível')? true:false;
        $availableTimes['mon02'] = (isset($post['mon02']) && trim($post['mon02'])==='Disponível')? true:false;
        $availableTimes['mon03'] = (isset($post['mon03']) && trim($post['mon03'])==='Disponível')? true:false;
        $availableTimes['mon04'] = (isset($post['mon04']) && trim($post['mon04'])==='Disponível')? true:false;
        $availableTimes['tue01'] = (isset($post['tue01']) && trim($post['tue01'])==='Disponível')? true:false;
        $availableTimes['tue02'] = (isset($post['tue02']) && trim($post['tue02'])==='Disponível')? true:false;
        $availableTimes['tue03'] = (isset($post['tue03']) && trim($post['tue03'])==='Disponível')? true:false;
        $availableTimes['tue04'] = (isset($post['tue04']) && trim($post['tue04'])==='Disponível')? true:false;
        $availableTimes['wed01'] = (isset($post['wed01']) && trim($post['wed01'])==='Disponível')? true:false;
        $availableTimes['wed02'] = (isset($post['wed02']) && trim($post['wed02'])==='Disponível')? true:false;
        $availableTimes['wed03'] = (isset($post['wed03']) && trim($post['wed03'])==='Disponível')? true:false;
        $availableTimes['wed04'] = (isset($post['wed04']) && trim($post['wed04'])==='Disponível')? true:false;
        $availableTimes['thu01'] = (isset($post['thu01']) && trim($post['thu01'])==='Disponível')? true:false;
        $availableTimes['thu02'] = (isset($post['thu02']) && trim($post['thu02'])==='Disponível')? true:false;
        $availableTimes['thu03'] = (isset($post['thu03']) && trim($post['thu03'])==='Disponível')? true:false;
        $availableTimes['thu04'] = (isset($post['thu04']) && trim($post['thu04'])==='Disponível')? true:false;
        $availableTimes['fri01'] = (isset($post['fri01']) && trim($post['fri01'])==='Disponível')? true:false;
        $availableTimes['fri02'] = (isset($post['fri02']) && trim($post['fri02'])==='Disponível')? true:false;
        $availableTimes['fri03'] = (isset($post['fri03']) && trim($post['fri03'])==='Disponível')? true:false;
        $availableTimes['fri04'] = (isset($post['fri04']) && trim($post['fri04'])==='Disponível')? true:false;
        $data['availableTimes'] = json_encode($availableTimes, JSON_UNESCAPED_UNICODE);
        $data['pesRelationship'] = self::validateString(self::post('pes-relationship'));
        $data['occupation'] = self::validateString(self::post('occupation'));
        $data['universityName'] = self::validateString(self::post('university-name'));
        $data['universityRegistration'] = self::validateString(self::post('university-registration'));
        $data['universityPhase'] = self::validateString(self::post('university-phase'));
        $data['universityCourse'] = self::validateString(self::post('university-course'));
        
        // projetos antigos e atuais
        for($i = 0; $i < 5; $i++) {
            if(isset($post['old-project-name-'.strval($i)]) || isset($post['old-project-professor-'.strval($i)])
            || isset($post['old-project-time-'.strval($i)])) {
                $oldProjects['project'.strval($i)] = [
                    'name' => self::validateString(self::post('old-project-name-'.strval($i))),
                    'professor' => self::validateString(self::post('old-project-name-'.strval($i))),
                    'time' => self::validateString(self::post('old-project-name-'.strval($i)))
                ];
            }

            if(isset($post['current-project-name-'.strval($i)]) || isset($post['current-project-professor-'.strval($i)])
            || isset($post['current-project-time-'.strval($i)])) {
                $currentProjects['project'.strval($i)] = [
                    'name' => self::validateString(self::post('current-project-name-'.strval($i))),
                    'professor' => self::validateString(self::post('current-project-name-'.strval($i))),
                    'time' => self::validateString(self::post('current-project-name-'.strval($i)))
                ];
            }
        }
        $data['oldProjects'] = (!empty($oldProjects))? json_encode($oldProjects, JSON_UNESCAPED_UNICODE):null;
        $data['currentProjects'] = (!empty($currentProjects))? json_encode($currentProjects, JSON_UNESCAPED_UNICODE):null;
        $data['volunteerExperience'] = self::validateString(self::post('volunteer-experience'));

        if(empty($errorLog)) {
            $data['errorStatus'] = false;
        } else {
            $data['errorStatus'] = true;
            $data['errorLog'] = $errorLog;
        }

        self::$post = null;

        return (Object)$data;
    }

    public static function validateStudentCandidateData($post) {

        self::$post = $post;

        $data = array();
        $errorLog = array();
        $scholarity = array();
        $occupation = array();
        $studyRoutine = array();
        $universitaryEntranceExam = array();
        $mobility = array();
        //$divulgation = array(); // Não utilizado no momento

        $data['firstName'] = self::validateString(self::post('first-name'));
        if($data['firstName'] === '') $errorLog[] = 'Nome inválido ou ausente';
        $data['lastName'] = self::validateString(self::post('last-name'));
        if($data['lastName'] === '') $errorLog[] = 'Sobrenome inválido ou ausente';
        $data['sex'] = self::validateString(self::post('sex'));
        if($data['sex'] === '') $errorLog[] = 'Sexo inválido ou ausente';
        $data['birthDate'] = self::validateDatetime(self::post('birth-date'), 'Y-m-d');
        $data['cpf'] = self::validateCpf(self::post('cpf'));
        if(!$data['birthDate']) $errorLog[] = 'Data de nascimento inválida ou ausente';
        $data['email'] = self::validateEmail(self::post('email'));
        $data['phone'] = self::validateNumber(self::post('phone'));
        $data['parentGuardianName'] = self::validateString(self::post('parent-guardian-name'));
        $data['parentGuardianPhone'] = self::validateString(self::post('parent-guardian-phone'));
        $data['parentGuardianRelationship'] = self::validateString(self::post('parent-guardian-relationship'));
        $data['parentGuardianCpf'] = self::validateString(self::post('parent-guardian-cpf'));
        $data['type'] = (isset($post['student-type']) && trim($post['student-type'])==='estudante')? 'estudante':'formado';
       
        $scholarity['schoolName'] = self::validateString(self::post('school-name'));
        $scholarity['schoolCity'] = self::validateString(self::post('school-city'));
        $scholarity['schoolUf'] = self::validateString(self::post('school-uf'));
        $scholarity['schoolRegistration'] = self::validateString(self::post('school-registration'));
        $scholarity['schoolGrade'] = self::validateString(self::post('school-grade'));
        $scholarity['schoolClass'] = self::validateString(self::post('school-class'));
        $scholarity['schoolShift'] = self::validateString(self::post('school-shift'));
        $scholarity['schoolConclusionYear'] = self::validateString(self::post('school-conclusion-year'));
        $scholarity['schoolType'] = self::validateString(self::post('school-type'));
        
        $data['scholarity'] = json_encode($scholarity, JSON_UNESCAPED_UNICODE);
        
        $occupation['work'] = self::validateString(self::post('work'));
        $occupation['workTime'] = self::validateString(self::post('work-time'));
        $occupation['workingHours'] = self::validateString(self::post('working-hours'));
        $studyRoutine['hasRoutine'] = self::validateString(self::post('study-routine'));
        $studyRoutine['days'] = self::validateString(self::post('study-days'));
        $studyRoutine['time'] = self::validateString(self::post('study-time'));
        $mobility['origin'] = self::validateString(self::post('route-origin'));
        $mobility['transportType'] = self::validateString(self::post('transport-type'));
        $mobility['time'] = self::validateString(self::post('route-time'));
        $university['entranceExam'] = self::validateString(self::post('university-entrance-exam'));
        $university['course'] = self::validateString(self::post('university-course'));
        $university['type'] = self::validateString(self::post('university-type'));
       
        $data['additionalInformation'] = json_encode([
            'occupation' => $occupation,
            'studyRoutine' => $studyRoutine,
            'mobility' => $mobility,
            'university' => $university
        ], JSON_UNESCAPED_UNICODE);

        if(empty($errorLog)) {
            $data['errorStatus'] = false;
        } else {
            $data['errorStatus'] = true;
            $data['errorLog'] = $errorLog;
        }

        self::$post = null;

        return (Object)$data;
    }

    public static function validateCandidateData($post, $type)
    {
        self::$post = $post;

        $data = array();
        $errorLog = array();
        $parentGuardian = array();
        //$divulgation = array(); // Não utilizado no momento

        $data['firstName'] = self::validateString(self::post('first-name'));
        if($data['firstName'] === '') $errorLog[] = 'Nome inválido ou ausente';
        $data['lastName'] = self::validateString(self::post('last-name'));
        if($data['lastName'] === '') $errorLog[] = 'Sobrenome inválido ou ausente';
        $data['sex'] = self::validateString(self::post('sex'));
        if($data['sex'] === '') $errorLog[] = 'Sexo inválido ou ausente';
        $data['birthDate'] = self::validateDatetime(self::post('birth-date'), 'Y-m-d');
        if(!$data['birthDate']) $errorLog[] = 'Data de nascimento inválida ou ausente';
        $data['cpf'] = self::validateCpf(self::post('cpf'));
        if(!$data['cpf']) $errorLog[] = 'Número de CPF inválido ou ausente';
        $data['email'] = self::validateEmail(self::post('email'));
        if(!$data['email']) $errorLog[] = 'Endereço de e-mail inválido ou ausente';
        $data['phone'] = self::validateNumber(self::post('phone'));
        $parentGuardian['name'] = self::validateString(self::post('parent-guardian-name'));
        $parentGuardian['phone'] = self::validateString(self::post('parent-guardian-phone'));
        $parentGuardian['relationship'] = self::validateString(self::post('parent-guardian-relationship'));
        $parentGuardian['cpf'] = self::validateString(self::post('parent-guardian-cpf'));
        $data['parentGuardianInformation'] = json_encode($parentGuardian, JSON_UNESCAPED_UNICODE);
        $data['address'] = '{}';
        $data['divulgation'] = '{}';

        if($type === 'CA') {

            $scholarity = array();
            $occupation = array();
            $studyRoutine = array();
            $university = array();
            $mobility = array();

            $data['type'] = (isset($post['student-type']) && trim($post['student-type'])==='estudante')? 'E':'F';
       
            $scholarity['schoolName'] = self::validateString(self::post('school-name'));
            $scholarity['schoolCity'] = self::validateString(self::post('school-city'));
            $scholarity['schoolUf'] = self::validateString(self::post('school-uf'));
            $scholarity['schoolRegistration'] = self::validateString(self::post('school-registration'));
            $scholarity['schoolGrade'] = self::validateString(self::post('school-grade'));
            $scholarity['schoolClass'] = self::validateString(self::post('school-class'));
            $scholarity['schoolShift'] = self::validateString(self::post('school-shift'));
            $scholarity['schoolConclusionYear'] = self::validateString(self::post('school-conclusion-year'));
            $scholarity['schoolType'] = self::validateString(self::post('school-type'));
            
            $data['scholarity'] = json_encode($scholarity, JSON_UNESCAPED_UNICODE);
            
            $occupation['work'] = self::validateString(self::post('work'));
            $occupation['workTime'] = self::validateString(self::post('work-time'));
            $occupation['workingHours'] = self::validateString(self::post('working-hours'));
            $studyRoutine['hasRoutine'] = self::validateString(self::post('study-routine'));
            $studyRoutine['days'] = self::validateString(self::post('study-days'));
            $studyRoutine['time'] = self::validateString(self::post('study-time'));
            $mobility['origin'] = self::validateString(self::post('route-origin'));
            $mobility['transportType'] = self::validateString(self::post('transport-type'));
            $mobility['time'] = self::validateString(self::post('route-time'));
            $university['entranceExam'] = self::validateString(self::post('university-entrance-exam'));
            $university['course'] = self::validateString(self::post('university-course'));
            $university['type'] = self::validateString(self::post('university-type'));
        
            $data['additionalInformation'] = json_encode([
                'occupation' => $occupation,
                'studyRoutine' => $studyRoutine,
                'mobility' => $mobility,
                'university' => $university
            ], JSON_UNESCAPED_UNICODE);

        } else {

            $motivation = array();
            $availability = array();
            $motivation = array();
            $motivation = array();
            $availableTimes = array();
            $oldProjects = array();
            $currentProjects = array();

            $data['type'] = (isset($post['volunteer-type']) && trim($post['volunteer-type'])==='gestor')? 'G':'P';

            $data['firstOption'] = self::validateString(self::post('first-option'));
            $data['secondOption'] = self::validateString(self::post('second-option'));
            $data['thirdOption'] = self::validateString(self::post('third-option'));
            $motivation['becauseSector'] = self::validateString(self::post('motivation-because-sector'));
            $motivation['becausePes'] = self::validateString(self::post('motivation-because-pes'));
            $motivation['howHelp'] = self::validateString(self::post('motivation-how-help'));
            $motivation['becauseEducation'] = self::validateString(self::post('motivation-because-education'));
            $data['motivation'] = json_encode($motivation, JSON_UNESCAPED_UNICODE);

            $availability['weeklyAvailability'] = self::validateString(self::post('weekly-availability'));
            $availableTimes['mon01'] = (isset($post['mon01']) && trim($post['mon01'])==='Disponível')? true:false;
            $availableTimes['mon02'] = (isset($post['mon02']) && trim($post['mon02'])==='Disponível')? true:false;
            $availableTimes['mon03'] = (isset($post['mon03']) && trim($post['mon03'])==='Disponível')? true:false;
            $availableTimes['mon04'] = (isset($post['mon04']) && trim($post['mon04'])==='Disponível')? true:false;
            $availableTimes['tue01'] = (isset($post['tue01']) && trim($post['tue01'])==='Disponível')? true:false;
            $availableTimes['tue02'] = (isset($post['tue02']) && trim($post['tue02'])==='Disponível')? true:false;
            $availableTimes['tue03'] = (isset($post['tue03']) && trim($post['tue03'])==='Disponível')? true:false;
            $availableTimes['tue04'] = (isset($post['tue04']) && trim($post['tue04'])==='Disponível')? true:false;
            $availableTimes['wed01'] = (isset($post['wed01']) && trim($post['wed01'])==='Disponível')? true:false;
            $availableTimes['wed02'] = (isset($post['wed02']) && trim($post['wed02'])==='Disponível')? true:false;
            $availableTimes['wed03'] = (isset($post['wed03']) && trim($post['wed03'])==='Disponível')? true:false;
            $availableTimes['wed04'] = (isset($post['wed04']) && trim($post['wed04'])==='Disponível')? true:false;
            $availableTimes['thu01'] = (isset($post['thu01']) && trim($post['thu01'])==='Disponível')? true:false;
            $availableTimes['thu02'] = (isset($post['thu02']) && trim($post['thu02'])==='Disponível')? true:false;
            $availableTimes['thu03'] = (isset($post['thu03']) && trim($post['thu03'])==='Disponível')? true:false;
            $availableTimes['thu04'] = (isset($post['thu04']) && trim($post['thu04'])==='Disponível')? true:false;
            $availableTimes['fri01'] = (isset($post['fri01']) && trim($post['fri01'])==='Disponível')? true:false;
            $availableTimes['fri02'] = (isset($post['fri02']) && trim($post['fri02'])==='Disponível')? true:false;
            $availableTimes['fri03'] = (isset($post['fri03']) && trim($post['fri03'])==='Disponível')? true:false;
            $availableTimes['fri04'] = (isset($post['fri04']) && trim($post['fri04'])==='Disponível')? true:false;
            $availability['availableTimes'] = json_encode($availableTimes, JSON_UNESCAPED_UNICODE);

            $data['availability'] = json_encode($availability, JSON_UNESCAPED_UNICODE);
            
            $additionalInformation['pesRelationship'] = self::validateString(self::post('pes-relationship'));
            $additionalInformation['occupation'] = self::validateString(self::post('occupation'));
            $university['name'] = self::validateString(self::post('university-name'));
            $university['registration'] = self::validateString(self::post('university-registration'));
            $university['phase'] = self::validateString(self::post('university-phase'));
            $university['course'] = self::validateString(self::post('university-course'));
            $additionalInformation['university'] = json_encode($university, JSON_UNESCAPED_UNICODE);
            // projetos antigos e atuais
            for($i = 0; $i < 5; $i++) {
                if(isset($post['old-project-name-'.strval($i)]) || isset($post['old-project-professor-'.strval($i)])
                || isset($post['old-project-time-'.strval($i)])) {
                    $oldProjects['project'.strval($i)] = [
                        'name' => self::validateString(self::post('old-project-name-'.strval($i))),
                        'professor' => self::validateString(self::post('old-project-professor-'.strval($i))),
                        'time' => self::validateString(self::post('old-project-time-'.strval($i)))
                    ];
                }

                if(isset($post['current-project-name-'.strval($i)]) || isset($post['current-project-professor-'.strval($i)])
                || isset($post['current-project-time-'.strval($i)])) {
                    $currentProjects['project'.strval($i)] = [
                        'name' => self::validateString(self::post('current-project-name-'.strval($i))),
                        'professor' => self::validateString(self::post('current-project-professor-'.strval($i))),
                        'time' => self::validateString(self::post('current-project-time-'.strval($i)))
                    ];
                }
            }
            $additionalInformation['oldProjects'] = (!empty($oldProjects))? json_encode($oldProjects, JSON_UNESCAPED_UNICODE):null;
            $additionalInformation['currentProjects'] = (!empty($currentProjects))? json_encode($currentProjects, JSON_UNESCAPED_UNICODE):null;
            $additionalInformation['volunteerExperience'] = self::validateString(self::post('volunteer-experience'));

            $data['additionalInformation'] = json_encode($additionalInformation, JSON_UNESCAPED_UNICODE);
        }
        

        if(empty($errorLog)) {
            $data['errorStatus'] = false;
        } else {
            $data['errorStatus'] = true;
            $data['errorLog'] = $errorLog;
        }

        self::$post = null;

        return (Object)$data;
    }
}