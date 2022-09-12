<?php 
// Controlador para área restrita geral
namespace App\Controller;

use App\Authenticator;
use App\Page;
use Database\EventDB;
use Router\Request;

class Administrator {

    private static function getLinks()
    {
        return array(
                    (object) array('name' => 'Gerenciar Perfil', 'url' => '/administrador/profile'),
                    (object) array('name' => 'Gerenciar Usuários', 'url' => '/administrador/users'),
                    (object) array('name' => 'Gerenciar Eventos', 'url' => '/administrador/events'),
                    (object) array('name' => 'Gerenciar Atividades', 'url' => '/administrador/activities'),
                    (object) array('name' => 'Gerenciar Participantes', 'url' => '/administrador/participants'));
    }

    private static function checkLink($subpage) 
    {
        $links = self::getLinks();
        $links = array_map(function($it) {
            return $it->url;
        }, $links);
        return (array_search('/administrador/'.$subpage, $links) === false)? false:true;
    }
    
    public function showAdministratorPage()
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'ADMIN') {
                $code = Authenticator::generateLogoutCode();
                $links = self::getLinks();
                Page::render('@admin/home.html', ['logoutCode' => $code, 'links' => $links]);
            } else {
                $url = Authenticator::getUserURL();
                header("Location: $url");
                exit;
            }
        } else {
            Page::showErrorHttpPage("401");
            exit;
        }
        
    }

    private static function getBdData($subpage)
    {
        if($subpage === 'activities') {
            return EventDB::getActivitiesData();
        } else if ($subpage === 'participants') {
            return EventDB::getParticipantsData();
        }
        return null;
    }

    public function showAdministratorSubpage($subpage)
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'ADMIN') {
                if(self::checkLink($subpage)) {
                    $code = Authenticator::generateLogoutCode();
                    $links = self::getLinks();
                    $data = self::getBdData($subpage);
                    $formCode = Authenticator::createFormCode($subpage);
                    Page::render('@admin/'.$subpage.'.html', ['logoutCode' => $code, 'links' => $links, 'data' => $data, 'formCode' => $formCode]);
                } else {
                    Page::showErrorHttpPage("404");
                    exit;
                }
            } else {
                $url = Authenticator::getUserURL();
                header("Location: $url/$subpage");
                exit;
            }
        } else {
            Page::showErrorHttpPage("401");
            exit;
        }
    }

    public function insertUser()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkFormCode($code, 'users')) {
            Authenticator::removeFormCode('users');
            $url = Authenticator::getUserURL();
            header("Location: $url/users");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function updateUser()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkFormCode($code, 'users')) {
            Authenticator::removeFormCode('users');
            $url = Authenticator::getUserURL();
            header("Location: $url/users");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function deleteUser()
    {
        $request = new Request;
        $code = $request->__get('form-code');
        if(Authenticator::checkFormCode($code, 'users')) {
            Authenticator::removeFormCode('users');
            $url = Authenticator::getUserURL();
            header("Location: $url/users");
            exit;
        } else {
            Page::showErrorHttpPage('401');
        }
    }
}