<?php 
// Controlador para área pública
namespace App\Controller;

use App\Authenticator;
use App\DataValidator;
use App\Email;
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
                    $this->showLoginInterviewPage((object)$error);
                }
            } else {
                $error = array(
                    'cpf' => $cpf,
                    'cpfError' => 'CPF não encontrado na base de dados.'
                );
                $this->showLoginInterviewPage((object)$error);
            }
        } else {
            echo "Erro de autenticação do servidor";
        }
    }

    public function showScheduleInterviewPage($date = null)
    {
        if(isset($_SESSION['isCandidate'])) {

            $interview = InterviewsDB::getInterviewTimeId($_SESSION['isCandidate']);
            $logoutCode = Authenticator::createFormCode('logout');
            $candidate = InterviewsDB::getCandidateInfo($_SESSION['isCandidate']);

            if($interview) {

                $time = InterviewsDB::getTimeInfo($interview->id);
                
                $data = array(
                    'logoutCode' => $logoutCode, 
                    'candidate' => $candidate,
                    'time' => $time
                );

                Page::render('@public/interview-schedule.html', $data);

            } else {

                $formCode = Authenticator::createFormCode('interview');
                
                if($date) {
                    $today = DateTime::createFromFormat('Y-m-d', $date);
                } else {
                    $today = new DateTime('now');
                    $today->add(new DateInterval('P1D'));
                }
    
                $now = new DateTime('now');
                $now->add(new DateInterval('P1D'));
    
                $prev = ($today->format('Y-m-d') > $now->format('Y-m-d'))? true:false;
                $next = ($today->format('Y-m-d') < '2025-02-07')? true:false;
    
                $times = InterviewsDB::getTimesSchedule($today->format('Y-m-d'));
    
                $data = array(
                    'logoutCode' => $logoutCode, 
                    'formCode' => $formCode, 
                    'candidate' => $candidate,
                    'times' => $times,
                    'date' => $today->format('Y-m-d'),
                    'next' => $next,
                    'prev' => $prev
                );

                Page::render('@public/interview-schedule.html', $data);
            }
            
        } else {
            header("Location: /entrevista/login");
        }
    }

    public function saveScheduleInterview()
    {
        $request = new Request;
        $formCode = $request->__get('form-code');
        
        if(Authenticator::checkFormCode($formCode, 'interview') && isset($_SESSION['isCandidate'])) {
            
            $type = DataValidator::validateString($request->__get('type'));

            if($type === 'prev') {

                $date = DataValidator::validateString($request->__get('date'));
                $today = DateTime::createFromFormat('Y-m-d', $date);
                $today->sub(new DateInterval('P1D'));
                $this->showScheduleInterviewPage($today->format('Y-m-d'));

            } else if ($type === 'next') {

                $date = DataValidator::validateString($request->__get('date'));
                $today = DateTime::createFromFormat('Y-m-d', $date);
                $today->add(new DateInterval('P1D'));
                $this->showScheduleInterviewPage($today->format('Y-m-d'));

            } else if ($type === 'confirm') {

                $idTime = DataValidator::validateInt($request->__get('time-id'));

                $formCode = Authenticator::createFormCode('interview');
                $logoutCode = Authenticator::createFormCode('logout');
                $candidate = InterviewsDB::getCandidateInfo($_SESSION['isCandidate']);
                $time = InterviewsDB::getTimeInfo($idTime);
                $data = array(
                    'logoutCode' => $logoutCode, 
                    'formCode' => $formCode, 
                    'candidate' => $candidate,
                    'time' => $time
                );
                Page::render('@public/interview-confirmation.html', $data);

            } else if ($type === 'save') {

                $formCode = Authenticator::createFormCode('interview');
                $logoutCode = Authenticator::createFormCode('logout');
                $idTime = DataValidator::validateInt($request->__get('time-id'));
                $insert = InterviewsDB::insertInterviewSchedule($_SESSION['isCandidate'], $idTime);
                $candidate = InterviewsDB::getCandidateInfo($_SESSION['isCandidate']);
                $time = InterviewsDB::getTimeInfo($idTime);

                Email::sendInterviewConfirmation($candidate, $time);

                $data = array(
                    'logoutCode' => $logoutCode,
                    'formCode' => $formCode, 
                    'candidate' => $candidate,
                    'time' => $time,
                    'error' => $insert->error
                );

                Page::render('@public/interview-result.html', $data);
            }
        } else {
            header("Location: /entrevista/agendamento");
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