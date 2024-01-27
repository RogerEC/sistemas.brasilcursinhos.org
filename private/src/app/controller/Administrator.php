<?php 
// Controlador para área restrita geral
namespace App\Controller;

use App\Authenticator;
use App\DataValidator;
use App\Page;
use Database\EventDB;
use Database\VotingDB;
use Router\Request;

class Administrator {

    private static function getLinks()
    {
        return array(
                    
                    //(object) array('name' => 'Gerenciar Usuários', 'url' => '/administrador/users'),
                    (object) array('name' => 'Gerenciar Votações', 'url' => '/administrador/votings'),
                    (object) array('name' => 'Gerenciar Atividades', 'url' => '/administrador/activities'),
                    (object) array('name' => 'Gerenciar Participantes', 'url' => '/administrador/participants'),
                    (object) array('name' => 'Código do participante', 'url' => '/administrador/participant'),
                    (object) array('name' => 'Gerenciar Presença', 'url' => '/administrador/presence'),
                    (object) array('name' => 'Relatório', 'url' => '/administrador/report'));
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
        } else if($subpage === 'presence') {
            return EventDB::getActivitiesData();
        } else if($subpage === 'report') {
            return EventDB::getPresenceTotal();
        } else if($subpage === 'votings') {
            return VotingDB::getVotings();
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
                    $parameters = array('logoutCode' => $code, 'links' => $links, 'data' => $data, 'formCode' => $formCode);
                    if($subpage === 'presence') {
                        $parameters['lastActivityId'] = (isset($_SESSION['lastActivityId']))? $_SESSION['lastActivityId']:null;
                        if(isset($_SESSION['insertPresence']) && $_SESSION['insertPresence'] === true) {
                            $parameters['lastInsertError'] = $_SESSION['insertPresenceError'];
                        }
                    }
                    Page::render('@admin/'.$subpage.'.html', $parameters);
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

    public function showVotingPage($idVoting)
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'ADMIN') {

                $voting = VotingDB::getVoting($idVoting);

                if($voting) {
                    $code = Authenticator::generateLogoutCode();
                    $links = self::getLinks();
                    $data = VotingDB::getPrecenceInVoting($idVoting);
                    $parameters = array('logoutCode' => $code, 'links' => $links, 'data' => $data, 'voting' => $voting);
                    Page::render('@admin/voting.html', $parameters);
                } else {
                    echo "Nenhuma votação encontrada<br><br><a href='/administrador/votings'>Voltar</a>";
                }
                
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

    static public function getVoters($idVoting, $type)
    {
        if(Authenticator::checkLogin()) {
            if(Authenticator::getUserType() === 'ADMIN') {

                $voting = VotingDB::getVoting($idVoting);

                if($voting) {
                    $voters = VotingDB::getPrecenceInVoting($idVoting);
                    
                    $file = fopen('php://output', 'w');
                    
                    if($type === 'ata') {
                        header('Content-Type: text/txt; charset=UTF-8');
                        header('Content-Disposition: attachment; filename="eleitores.txt";');

                        $date = DataValidator::validateDatetime($voting->datetime, 'Y-m-d H:i:s', 'd/m/Y H\hi');

                        fwrite($file, $voting->name . ' | ' . $date . PHP_EOL . PHP_EOL);

                        fwrite($file, 'Nome – CPF – CUP '  . PHP_EOL . PHP_EOL);

                        foreach ($voters as $voter) {
                            fwrite($file, $voter->name . ' – ' . substr($voter->cpf, 0, 3).'.'.substr($voter->cpf, 3, 3).'.'.substr($voter->cpf, 6, 3).'-'.substr($voter->cpf, 9) . ' - ' . $voter->cup . PHP_EOL);
                        }
                        
                    } else {
                        header('Content-Type: application/csv; charset=UTF-8');
                        header('Content-Disposition: attachment; filename="eleitores.csv";');

                        foreach ($voters as $voter) {
                            fwrite($file, 'password,'.$voter->user.','.$voter->email.','.$voter->name.PHP_EOL);
                        }
                    }

                    fclose($file);
                } else {
                    echo "Nenhuma votação encontrada<br><br><a href='/administrador/votings'>Voltar</a>";
                }
                
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
}