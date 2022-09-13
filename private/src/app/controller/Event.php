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
        if(Authenticator::checkFormCode($code, 'activities')) {
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
        if(Authenticator::checkFormCode($code, 'activities')) {
            
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
        
        if(Authenticator::checkFormCode($code, 'activities')) {
            
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
        if(Authenticator::checkFormCode($code, 'participants')) {
            
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

        if(Authenticator::checkFormCode($code, 'participants')) {
            
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
        
        if(Authenticator::checkFormCode($code, 'participants')) {
            
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
}