<?php
// Clase roteadora, responsável por controlar as rotas do site
namespace Router;

//use Router\Request;
use Router\Dispacher;
use Router\RouteCollection;

class Router
{
    protected $route_collection;
 
    public function __construct()
    {
        $this->route_collection = new RouteCollection;
        $this->dispacher = new Dispacher;
    }
 
    // Adiciona uma rota do tipo GET a Coleção de Rotas
    public function get($pattern, $callback)
    {
        $this->route_collection->add('get', $pattern, $callback);
        return $this;
    }
 
    // Adiciona uma rota do tipo POST a Coleção de Rotas
    public function post($pattern, $callback)
    {
        $this->route_collection->add('post', $pattern, $callback);
        return $this;   
    }
 
    // Adiciona uma rota do tipo PUT a Coleção de Rotas
    public function put($pattern, $callback)
    {
        $this->route_collection->add('put', $pattern, $callback);
        return $this;   
    }
 
    // Adiciona uma rota do tipo DELETE a Coleção de Rotas
    public function delete($pattern, $callback)
    {
        $this->route_collection->add('delete', $pattern, $callback);
        return $this;   
    }
 
    // Procura por uma rota dentro da coleção
    public function find($request_type, $pattern)
    {
        return $this->route_collection->where($request_type, $pattern);
    }

    // Método que chama o despachante para atender a rota
    protected function dispach($route, $params, $namespace = "App\\Controller\\")
    { 
        return $this->dispacher->dispach($route->callback, $params, $namespace);
    }

    // Método que cham o despachante para retornar a página de erro 404 quando a rota não é encontrada
    protected function notFound()
    {
        $route = $this->find('get', 'error/404');
        $params = $route->callback['values'] ? $this->getValues('error/404', $route->callback['values']) : [];
     
        return $this->dispach($route, $params);
    }
    
    // Método que resolve a requisição e chama o despachante para atender a solicitação
    // Se a rota não for encontrada, chama o método notFound.
    public function resolve($request){
     
        $route = $this->find($request->method(), $request->uri());

        if($route) {
             
            $params = $route->callback['values'] ? $this->getValues($request->uri(), $route->callback['values']) : [];
     
            return $this->dispach($route, $params);
        }

        return $this->notFound();
    }

    // Método que pega os valores passados em posições {curingas} da rota
    protected function getValues($pattern, $positions)
    {
        $result = [];
    
        $pattern = array_filter(explode('/', $pattern));
    
        foreach($pattern as $key => $value) {
            if(in_array($key, $positions)) {
                $result[array_search($key, $positions)] = $value;
            }
        }
    
        return $result; 
    }

    // Método que recria o endereço de uma rota com base no seu nome e parâmetros
    public function translate($name, $params)
    {
        $pattern = $this->route_collection->isThereAnyHow($name);
        
        if($pattern) {
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $server = $_SERVER['SERVER_NAME'] . '/';
    
            return $protocol . $server . $this->route_collection->convert($pattern, $params);
        }
        return false;
    }
}
