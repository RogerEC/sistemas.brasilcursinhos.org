<?php 
// Controlador para os usuários
namespace App\Controller;

use App\Authenticator;
use App\Page;
use Database\EventDB;

class User {

    private static function getLinks()
    {
        return array(
                    (object) array('name' => 'Gerenciar Perfil', 'url' => '/usuario/profile'),
                    (object) array('name' => 'Gerenciar Atividades', 'url' => '/usuario/activities'),
                    (object) array('name' => 'Gerenciar Participantes', 'url' => '/usuario/participants'));
    }

    private static function checkLink($subpage) 
    {
        $links = self::getLinks();
        $links = array_map(function($it) {
            return $it->url;
        }, $links);
        return (array_search('/usuario/'.$subpage, $links) === false)? false:true;
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
    
    public function showUserPage()
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'U') {
                $code = Authenticator::generateLogoutCode();
                $links = self::getLinks();
                Page::render('@user/home.html', ['logoutCode' => $code, 'links' => $links]);
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

    public function showUserSubpage($subpage)
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'U') {
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
}