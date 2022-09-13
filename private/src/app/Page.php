<?php 
namespace App;

use Twig\Loader;
use Twig;

class Page {

    private static $viewer;

    public function __construct()
    {
        $this->initTwig();
    }

    protected static function initTwig()
    {
        if(empty(self::$viewer)){
            $loader = new \Twig\Loader\FilesystemLoader(DIR_TEMPLATES);
            $loader->addPath(DIR_TEMPLATES.'components/', 'components');
            $loader->addPath(DIR_TEMPLATES.'links/', 'links');
            $loader->addPath(DIR_TEMPLATES.'public/', 'public');
            $loader->addPath(DIR_TEMPLATES.'errors/', 'errors');
            $loader->addPath(DIR_TEMPLATES.'restricted/', 'restricted');
            $loader->addPath(DIR_TEMPLATES.'restricted/user/', 'user');
            $loader->addPath(DIR_TEMPLATES.'restricted/admin/', 'admin');
            $loader->addPath(DIR_TEMPLATES.'documents/', 'documents');
            $loader->addPath(DIR_TEMPLATES.'emails/', 'emails');
            $loader->addPath(DIR_TEMPLATES.'encup/', 'encup');
            $loader->addPath(DIR_TEMPLATES.'calendario/', 'calendario');
            self::$viewer = new \Twig\Environment($loader, ['cache' => false]);
            self::$viewer->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('America/Sao_Paulo');
        }
        return self::$viewer;
    }

    public static function render($pageName, $parameters = [])
    {
        $template = self::initTwig()->load($pageName);
        echo $template->render($parameters);
        exit();
    }

    public static function getRender($pageName, $parameters = [])
    {
        $template = self::initTwig()->load($pageName);
        return $template->render($parameters);
    }

    public static function showErrorHttpPage($code)
    { 
        $parameters = self::getErrorHttpDescription($code);
        echo self::initTwig()->render('errorHttp.html', $parameters);
        return;
    }

    public static function showInternalErrorPage($code)
    {
        $parameters = self::getInternalErrorDescription($code);
        echo self::initTwig()->render('internalError.html', $parameters);
        return;
    }

    public static function showUnderConstructionPage($title = null)
    {
        echo self::render('under-construction.html', ['title' => $title]);
        return;
    }

    public static function showSubpageLinks($title=null, $links=[])
    {
        echo self::render('subpage-links.html', ['title' => $title, 'links' => $links]);
        return;
    }

    public static function getSelectiveProcessPageType($department)
    {
        $department = strtolower($department);
        
        switch($department) {
            case 'alunos':
                return 'A';
            case 'gestores':
                return 'G';
            case 'professores':
                return 'P';
            default:
                return false;
        }
    }

    private static function getErrorHttpDescription($code)
    {
        $data = array();
        $data['description'] = "";
        $data['code'] = $code;
        switch($code){
            case 400:
                $data['name'] = "Requisição Inválida";
                break;
            case 401:
                $data['name'] = "Não autorizado";
                $data['description'] = "É necessário realizar o login para prosseguir.";
                break;
            case 402:
                $data['name'] = "Pagamento necessário";
                break;
            case 403:
                $data['name'] = "Acesso negado";
                $data['description'] = "Você não tem permissão para acessar essa página.";
                break;
            case 404:
                $data['name'] = "Página não encontrada";
                $data['description'] = "A página solicitada não foi encontrada no servidor.";
                break;
            case 405:
                $data['name'] = "Método não permitido";
                break;
            case 406:
                $data['name'] = "Não aceito";
                break;
            case 407:
                $data['name'] = "Autenticação de Proxy Necessária";
                break;
            case 408:
                $data['name'] = "Tempo de solicitação esgotado";
                break;
            case 409:
                $data['name'] = "Conflito";
                break;
            case 410:
                $data['name'] = "Perdido";
                break;
            case 411:
                $data['name'] = "Duração necessária";
                break;
            case 412:
                $data['name'] = "Falha de pré-condição";
                break;
            case 413:
                $data['name'] = "Solicitação da entidade muito extensa";
                break;
            case 414:
                $data['name'] = "Solicitação de URL muito Longa";
                break;
            case 415:
                $data['name'] = "Tipo de mídia não suportado";
                break;
            case 416:
                $data['name'] = "Solicitação de faixa não satisfatória";
                break;
            case 417:
                $data['name'] = "Falha na expectativa";
                break;
            case 418:
                $data['name'] = "Eu sou um bule de chá!";
                $data['description'] = "Por ser um bule de chá, eu não posso preparar café!";
                break;
            case 500:
                $data['name'] = "Erro interno do servidor";
                break;
            case 501:
                $data['name'] = "Não implementado";
                break;
            case 502:
                $data['name'] = "Porta de entrada ruim";
                break;
            case 503:
                $data['name'] = "Serviço indisponível";
                break;
            case 504:
                $data['name'] = "Tempo limite da Porta de Entrada";
                break;
            case 505:
                $data['name'] = "Versão HTTP não suportada";
                break;
            default:
                $data['name'] = "Erro desconhecido";
        }

        return $data;
    }

    private static function getInternalErrorDescription($code)
    {
        
    }
}