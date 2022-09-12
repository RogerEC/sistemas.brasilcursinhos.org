<?php 
// Controlador para área restrita geral
namespace App\Controller;

use App\Authenticator;
use App\Page;

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

    public function showAdministratorSubpage($subpage)
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'ADMIN') {
                if(self::checkLink($subpage)) {
                    $code = Authenticator::generateLogoutCode();
                    $links = self::getLinks();
                    Page::render('@admin/'.$subpage.'.html', ['logoutCode' => $code, 'links' => $links]);
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