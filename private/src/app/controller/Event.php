<?php 
// Controlador para área restrita geral
namespace App\Controller;

use App\Authenticator;
use App\DataValidator;
use App\Log;
use App\Page;
use Database\EventDB;
use Router\Request;

class Event {
    
    public function insertActivity()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'activities')) {
            Authenticator::removeFormCode('activities');

            $data = DataValidator::validateActivity($request->all());
            
            if(!$data->error) {
                if(!EventDB::insertActivity($data)) {
                    // erro no insert
                }
            } else {
                // erro de validação dos dados;
            }

            $url = Authenticator::getUserURL();
            header("Location: $url/activities");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function updateActivity()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'activities')) {
            
            Authenticator::removeFormCode('activities');
            
            $data = DataValidator::validateActivity($request->all());

            if(!$data->error) {
                if(!EventDB::updateActivity($data)) {
                    // erro no insert
                }
            } else {
                // erro de validação dos dados;
            }
            
            $url = Authenticator::getUserURL();
            header("Location: $url/activities");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function deleteActivity()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'activities')) {
            
            Authenticator::removeFormCode('activities');

            $id = DataValidator::validateInt($request->__get('deletion-id'));
            EventDB::deleteActivity($id);
            
            $url = Authenticator::getUserURL();
            header("Location: $url/activities");
            exit;

        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function insertParticipant()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'participants')) {
            
            Authenticator::removeFormCode('participants');
            
            $data = DataValidator::validateParticipant($request->all());
           
            if(!$data->error) {
                if(!EventDB::insertParticipant($data)) {
                    // erro no insert
                }
            } else {
                // erro de validação dos dados;
            }
            
            $url = Authenticator::getUserURL();
            header("Location: $url/participants");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function updateParticipant()
    {
        $request = new Request;
        $code = $request->__get('form-code');

        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'participants')) {
            
            Authenticator::removeFormCode('participants');

            $data = DataValidator::validateParticipant($request->all());

            if(!$data->error) {
                if(!EventDB::updateParticipant($data)) {
                    // erro no insert
                }
            } else {
                // erro de validação dos dados;
            }

            $url = Authenticator::getUserURL();
            header("Location: $url/participants");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function deleteParticipant()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'participants')) {
            
            Authenticator::removeFormCode('participants');

            $id = DataValidator::validateInt($request->__get('deletion-id'));
            EventDB::deleteParticipant($id);

            $url = Authenticator::getUserURL();
            header("Location: $url/participants");
            exit;

        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function checkParticipantCpf()
    {
        $request = new Request;

        if($request->__isset('cpf')) {

            if($request->__isset('original-cpf')) {
                if($request->__get('cpf') === $request->__get('original-cpf')) {
                    echo 'true';
                    exit;
                }
            }
            
            $cpf = DataValidator::validateCpf($request->__get('cpf'));
            
            if($cpf === false) {
                echo 'false';
                exit;
            }

            $check = EventDB::checkParticipantCpf($cpf);
            
            if(empty($check)) {
                echo 'true';
            } else {
                echo 'false';
            }
        } else {
            echo 'false';
        }
        exit;
    }

    public static function checkPresence()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'presence')) {
            
            Authenticator::removeFormCode('presence');

            $activityId = DataValidator::validateInt($request->__get('activity-id'));
            $participantCode = DataValidator::validateInt($request->__get('participant-id'));
            $parameters = array('error' => false);
            
            if($activityId === false || $participantCode === false) {
                $parameters['error'] = true;
                $parameters['errorMessage'] = 'Identificador de atividade ou código de participante inválido.';
            }

            if($activityId !== false) {
                $_SESSION['lastActivityId'] = $activityId;
            }

            
            $parameters['activity'] = EventDB::getActivityData($activityId);
            
            if(empty($parameters['activity'])) {
                $parameters['error'] = true;
                $parameters['errorMessage'] = 'Atividade não encontrada.';
            }
            
            $parameters['participant'] = EventDB::getParticipantData($participantCode);
            
            if(empty($parameters['participant'])) {
                $parameters['error'] = true;
                $parameters['errorMessage'] = 'Participante não encontrado.';
            }

            $parameters['formCode'] = Authenticator::createFormCode('confirm-presence');

            Page::render('@admin/confirm-presence.html', $parameters);

        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public static function cancelPresence()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'confirm-presence')) {
            
            Authenticator::removeFormCode('confirm-presence');

            $url = Authenticator::getUserURL();
            header("Location: $url/presence");
            exit;

        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public static function confirmPresence()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'confirm-presence')) {
            
            Authenticator::removeFormCode('confirm-presence');

            $activityId = DataValidator::validateInt($request->__get('activity-id'));
            $participantId = DataValidator::validateInt($request->__get('participant-id'));

            if($activityId === false || $participantId === false) {
                $_SESSION['insertPresenceError'] = true;
            }

            $_SESSION['insertPresence'] = true;

            $_SESSION['insertPresenceError'] = !EventDB::insertPresence($activityId, $participantId, Authenticator::getUserID());

            $url = Authenticator::getUserURL();
            header("Location: $url/presence");
            exit;

        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public static function checkParticipant()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        
        if(Authenticator::checkLogin() && Authenticator::checkFormCode($code, 'participant')) {
            
            Authenticator::removeFormCode('participant');

            $cpf = DataValidator::validateCpf($request->__get('cpf'));

            $parameters = array('error' => false);
            
            if($cpf === false) {
                $parameters['error'] = true;
                $parameters['errorMessage'] = 'CPF inválido.';
            } else {
                $participant = EventDB::getParticipant($cpf);

                if(empty($participant)) {
                    $parameters['error'] = true;
                    $parameters['errorMessage'] = 'Participante não encontrado.';
                } else {
                    $parameters['participant'] = $participant;
                }
            }

            $parameters['statusSearch'] = true;
            $parameters['formCode'] = Authenticator::createFormCode('participant');
            $parameters['links'] = array(
                (object) array('name' => 'Gerenciar Atividades', 'url' => '/administrador/activities'),
                (object) array('name' => 'Gerenciar Participantes', 'url' => '/administrador/participants'),
                (object) array('name' => 'Código do participante', 'url' => '/administrador/participant'),
                (object) array('name' => 'Gerenciar Presença', 'url' => '/administrador/presence'));

            Page::render('@admin/participant.html', $parameters);

        } else {
            Page::showErrorHttpPage('401');
        }
    }
}