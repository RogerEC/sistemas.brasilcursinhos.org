<?php
// classe que gerencia a conexão com o banco de dados
namespace Database;

use App\Log;
use PDO;
use PDOException;

class Database
{
    private static $connection;

    // Construtor da classe, carrega os dados de acesso ao banco na memória
    public function __construct($parameters = [])
    {
        if(!empty($parameters)){
            self::connect($parameters);
        }
    }

    // destrutor da classe, libera a memória das variáveis utilizadas
    public function __destruct() {
        self::disconnect();
    }

    // efetua a conexão com o banco ou exibe uma mensagem de erro
    public static function connect($parameters = [])
    {
        try {
            if (is_null(self::$connection)){

                require_once DIR_PASSWORDS . '/database.php';
        
                $databaseType = (isset($parameters['databaseType']))? $parameters['databaseType']:DB_DATABASE_TYPE;
                $host = (isset($parameters['host']))? $parameters['host']:DB_HOST;
                $port = (isset($parameters['post']))? $parameters['post']:DB_PORT;
                $user = (isset($parameters['user']))? $parameters['user']:DB_USER;
                $password = (isset($parameters['password']))? $parameters['password']:DB_PASSWORD;
                $database = (isset($parameters['database']))? $parameters['database']:DB_DATABASE;

                self::$connection = new PDO("$databaseType:host=$host;port=$port;dbname=$database", $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

                return self::$connection;
            } else {
                return self::$connection;
            }
        } catch(PDOException $exception) {
            $message = "Erro ao iniciar banco de dados.";
            Log::error($message, 'database.log', $exception->getMessage());
            echo $message;
            exit();
        }
    }

    // efetua a desconeção com o banco
    public static function disconnect()
    {
        self::$connection = null;
    }

    // efetua um select genérico e retorna o resultado como um vetor de objetos
    public static function selectDatabase($sql, $parameters = null)
    {
        $query = self::connect()->prepare($sql);
        $query->execute($parameters);
        
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        
        self::disconnect();
        
        return $result;
    }

    // efetua um insert genérico e retorna o id do último elemento inserido
    public static function insertDatabase($sql, $parameters = null)
    {
        $connection = self::connect();
        $query = $connection->prepare($sql);
        $query->execute($parameters);
        
        $result = $connection->lastInsertId() or die(print_r($query->errorInfo(), true));
        
        self::disconnect();
        
        return $result;
    }

    // efetua um update genérico e retorna o número de linhas afetado
    public static function updateDatabase($sql, $parameters = null)
    {
        $query = self::connect()->prepare($sql);
        $query->execute($parameters);
        
        $result = $query->rowCount() or die(print_r($query->errorInfo(), true));
        
        self::disconnect();
        
        return $result;
    }
}
