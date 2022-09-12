<?php
// Classe responsável por armazenar os dados da requisição enviada pelo cliente
namespace Router;

class Request
{
    protected $files = [];
    protected $base;
    protected $uri;
    protected $method;
    protected $protocol;
    protected $data = [];
 
    // Inicia setando o valor dos atributos relativos a requisição
    public function __construct()
    {
        $this->base = $_SERVER['REQUEST_URI'];
        $this->uri  = $_REQUEST['uri'] ?? '/';
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
        $this->setData();
 
        if(count($_FILES) > 0) {
            $this->setFiles();
        }
 
    }

    // pega os dados enviados na requisição POST/GET e doloca em $data
    protected function setData()
    {
        switch($this->method)
        {
            case 'post':
            $this->data = $_POST;
            break;
            case 'get':
            $this->data = $_GET;
            break;
            case 'head':
            case 'put':
            case 'delete':
            case 'options':
            parse_str(file_get_contents('php://input'), $this->data);
        }
    }
 
    // pega os arquivos enviados na requisição e coloca em $files
    protected function setFiles()
    {
        foreach ($_FILES as $key => $value) {
            $this->files[$key] = $value;
        }
    }
    
    // retorna a RESQUEST_URI
    public function base()
    {
        return $this->base;
    }
 
    // retorna a URL recebida pelo GET do redirecionamento .htaccess
    public function uri()
    {
        return $this->uri;
    }
 
    // retorna o método da requisição (POST, GET, DELETE...)
    public function method()
    {
        return $this->method;
    }
    
    // Retorna as dados da requisição
    public function all()
    {
        return $this->data;
    }
 
    // Retorna se o dado com a chave $key foi ou não definido
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }
    
    // Retorna o dado relativo a chave $key, se ele foi definido 
    public function __get($key)
    {
        if(isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }
 
    // Retorna se o arquivo relativo a chave $key foi ou não definido
    public function hasFile($key)
    {
        return isset($this->files[$key]);
    }
    
    // Retorna o arquivo relativo a chave $key, se ele estiver definido
    public function file($key)
    {  
        if(isset($this->files[$key])) {
            return $this->files[$key];
        }
        return null;
    }

    public function allFiles()
    {
        return $this->files;
    }
}
