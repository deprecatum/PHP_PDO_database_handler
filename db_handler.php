<?php


class db_handler{
// Hold the class instance.
//private static $instance = null;

private $connection_set=false;
private $custom_credentials_set=false;

private $db='';
private $charset='';

private $host = '';
private $user = '';
private $pass = '';
private $name = '';


private $conn;


public function is_connected(){
    return $this->connection_set;
}

/* If custom_credentials changed to true, 
*  set_custom_credentials must be called.
*  To check if connection is active
*  you must call connection_status
*/ 

//by default connects with the hardcoded credentials and returns true if connected
public function __construct($custom_credentials=false){

    $this->custom_credentials_set=$custom_credentials;

    if(!$this->custom_credentials_set){
        $this->set_default_credentials();
        $this->set_connection();
    }

    return $this->connection_set;
}


public function set_custom_credentials($db,$user,$pass){

    $this->host = '127.0.0.1';
    $this->db = $db;
    $this->charset = 'utf8';

    $this->user = $user;
    $this->pass = $pass;

    return $this->set_connection();
}


public function pass_query($query){
    $result=false;

    if($this->connection_set){

        $prepare=$this->conn->prepare($query);

        if( $prepare->execute() ){
            $result=$prepare->fetchAll();
        }else{
            echo 'rip';
        }

    }else{
        echo 'no connection';
    }

    return $result;
}


private function set_default_credentials(){

    $this->host = '127.0.0.1';
    $this->db = 'ponchaadvisor';
    $this->charset = 'utf8';

    $this->user = 'root';
    $this->pass = '';
}

private function set_connection(){

    $success=true;

    //Permite definir a configuração da base de dados
    $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

    $opt = array(
        //Em caso de erro ocorre excepcao
        PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,   
        //Define o comportamento por defeito do fetch
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //Ativa/desativa emular prepared statements     
        PDO::ATTR_EMULATE_PREPARES   => false,                
    );

    try{
        $this->conn = new PDO($dsn, $this->user, $this->pass, $opt);   
    }catch(PDOException $rip){
        echo 'couldnt connect to db';
        $success=false;
    }

    $this->connection_set=$success;
    return $success;
}



/*
 * Retorna a instancia unica do databa
 * @return [type] [description]

public static function getInstance()
{
  if(!self::$instance)
  {
  self::$instance = new ConnectDb();
}

return self::$instance;
}

public function getConnection()
{
  return $this->conn;
}*/


} //end class

?>
