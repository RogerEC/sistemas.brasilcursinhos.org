<?php
// Classe dispachante, responsável por verificar qual classe/função deve atender a solicitação do cliente

namespace Router;

class Dispacher
{
    public function dispach($callback, $params = [], $namespace = "App\\Controller\\")
    {   
        // Se o callback for uma chamada de função retorna a execução dela
        if(is_callable($callback['callback']))
        {
            return call_user_func_array($callback['callback'], array_values($params));
        
        // Caso contrário, chama a classe/método responsável por tratar a solicitação   
        } elseif (is_string($callback['callback'])) {
        
            if(!!strpos($callback['callback'], '@') !== false) {
    
    
                if(!empty($callback['namespace']))
                {
                    $namespace = $callback['namespace'];
                }
            
                $callback['callback'] = explode('@', $callback['callback']);
                $controller = $namespace.$callback['callback'][0];
                $method = $callback['callback'][1];
    
                $rc = new \ReflectionClass($controller);
    
                if($rc->isInstantiable() && $rc->hasMethod($method))
                {
                    return call_user_func_array(array(new $controller, $method), array_values($params));
                
                } else {
    
                    throw new \Exception("Erro ao despachar: controller não pode ser instanciado, ou método não exite");                
                }
            }
        }
        throw new \Exception("Erro ao despachar: método não implementado");
    }
}
