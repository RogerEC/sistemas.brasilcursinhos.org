<?php 
// Controlador para área restrita geral
namespace App\Controller;

use App\Authenticator;
use App\Page;
use Router\Request;

class Event {
    
    public function insertActivity()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkFormCode($code, 'activities')) {
            Authenticator::removeFormCode('activities');
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
            $url = Authenticator::getUserURL();
            header("Location: $url/participants");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }
}