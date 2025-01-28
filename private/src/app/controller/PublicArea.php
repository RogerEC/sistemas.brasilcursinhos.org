<?php 
// Controlador para área pública
namespace App\Controller;

use App\Authenticator;
use App\DataValidator;
use App\Page;
use Database\InterviewsDB;
use Database\VotingDB;
use DateInterval;
use DateTime;
use DateTimeZone;
use Router\Request;

class PublicArea {
    
    // exibe a página inicial do site
    public function showHomePage()
    {   
        Page::render('@public/homepage.html');
    }

    public function showPresenceVotingPage($code, $error = false)
    {
        $voting = VotingDB::getVoting($code);
        
        if($voting) {
            $cups = VotingDB::getCups();
            $timezone = new DateTimeZone('America/Sao_Paulo');
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $voting->datetime, $timezone);
            $date = $datetime->format('d/m/Y');
            $time = $datetime->format('H\hi');
            $formCode = Authenticator::createFormCode('voting');
            Page::render('@public/voting.html', ['data' => $voting, 'cups' => $cups, 'code' => $code,
            'date' => $date, 'time' => $time, 'formCode' => $formCode, 'error' => $error]);
        } else {
            Page::render('@public/voting.html', ['data' => $voting, 'error' => $error]);
        }
    }

    public function savePresenceVoting($code)
    {
        $request = new Request;
        $formCode = $request->__get('form-code');
        
        if(Authenticator::checkFormCode($formCode, 'voting')) {
            
            Authenticator::removeFormCode('voting');
            
            $voting = VotingDB::getVoting($code);

            if($voting) {
                $timezone = new DateTimeZone('America/Sao_Paulo');
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $voting->datetime, $timezone);
                $date = $datetime->format('d/m/Y');
                $time = $datetime->format('H\hi');
                if($voting->status === 'A') {
                    $data = DataValidator::validatePresenceVoting($request->all());
                    if(!$data->error) {
                        $insert = VotingDB::insertPresenceVoting($voting->id, $data);
                        if(!$insert->error) {
                            Page::render('@public/voting-result.html', ['data' => $voting, 'error' => false, 'code' => $code, 'date' => $date, 'time' => $time]);
                        } else {
                            if($insert->errorCode === 'DUPLICATE') {
                                $message = 'Já existe um representante cadastrado para o seu CUP nesta votação (nome: '.$insert->name.'). Apenas um membro por CUP pode votar nas Assembleias da Brasil Cursinhos.';
                            } else if ($insert->errorCode === 'DUPLICATE_CPF') {
                                $message = 'Já existe um representante cadastrado com o mesmo número de CPF ou endereço de e-mail nesta votação (nome: '.$insert->name.'). Um membro pode representar apenas um CUP em cada votação.';
                            } else {
                                $message = 'Houve um erro ao inserir os seus dados no banco de dados, tente novamente mais tarde';
                            }
                            Page::render('@public/voting-result.html', ['data' => $voting, 'error' => true, 'message' => $message, 'code' => $code, 'date' => $date, 'time' => $time]);
                        }
                    } else {
                        Page::render('@public/voting-result.html', ['data' => $voting, 'error' => true, 'message' => 'Os dados enviados no formulário estavam incorretor, tente preencher novamente.', 'code' => $code, 'date' => $date, 'time' => $time]);
                    }
                } else {
                    Page::render('@public/voting-result.html', ['data' => $voting, 'error' => true, 'message' => 'A votação encontra-se fechada e não está aceitando novas inscrições no momento.', 'code' => $code, 'date' => $date, 'time' => $time]);
                }
            } else {
                Page::render('@public/voting-result.html', ['data' => $voting, 'error' => true, 'message' => 'Nenhuma votação foi encontrada com os parâmetros informados. Verifique sua URL e tente novamente']);
            }
            
        } else {
            Page::showErrorHttpPage('401');
        }
    }

    public function showLoginInterviewPage($error = null) {
        $formCode = Authenticator::createFormCode('login-interviews');
        Page::render('@public/login-interviews.html', ['error' => $error, 'formCode' => $formCode]);
    }

    public function checkLoginInterview()
    {
        $request = new Request;
        $formCode = $request->__get('form-code');
        
        if(Authenticator::checkFormCode($formCode, 'login-interviews')) {
            $cpf = DataValidator::validateCpf($request->__get('cpf'));
            $password = DataValidator::validateNumber($request->__get('password'));

            $candidate = InterviewsDB::getCandidateId($cpf);

            if($candidate) {
                if($password === substr($cpf, 0, 6)) {
                    $_SESSION['isCandidate'] = $candidate->id;
                    header("Location: /entrevista/agendamento");
                } else {
                    $error = array(
                        'password' => true,
                        'passwordError' => 'Senha incorreta!'
                    );
                    $this->checkLoginInterview((object)$error);
                }
            } else {
                $error = array(
                    'cpf' => $cpf,
                    'cpfError' => 'CPF não encontrado na base de dados'
                );
                $this->checkLoginInterview((object)$error);
            }
        } else {
            echo "Erro de autenticação do servidor";
        }
    }

    public function showScheduleInterviewPage()
    {
        if(isset($_SESSION['isCandidate'])) {
            $formCode = Authenticator::createFormCode('interview');
            $logoutCode = Authenticator::createFormCode('logout');
            $candidate = InterviewsDB::getCandidateInfo($_SESSION['isCandidate']);
            $now = new DateTime('now');
            $now->add(new DateInterval('P1D'));
            $times = InterviewsDB::getTimesSchedule($now->format('Y-m-d'));
            $data = array(
                'logoutCode' => $logoutCode, 
                'formCode' => $formCode, 
                'candidate' => $candidate,
                'times' => $times,
                'today' => $now->format('Y-m-d')
            );
            Page::render('@public/interview-schedule.html', $data);
        } else {
            Page::showErrorHttpPage(401);
        }
    }

    public function makeLogout() {
        $request = new Request;
        $formCode = $request->__get('logout-code');

        if(Authenticator::checkFormCode($formCode, 'logout')) {
            header("Location: /entrevista/login");
            Authenticator::makeLogout(true);
        } else {
            header("Location: /entrevista/agendamento");
        }
    }
}