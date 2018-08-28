<?php


class Db_handler{

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


public function update_query($table, $columns, $values, $condition){
    $result=false; //return var


    $query=""; 
    
    //as colunas a alterar
    if(count($columns)==count($values)){
        $query="update $table set ";

        for($i=0; $i < count($columns)-1 ; $i++ ){
            $query.="$columns[$i]='$values[$i]', ";
        }
        $query.=end($columns)."='".end($values)."' where $condition;";

    }
    
    return $this->pass_query($query);
}


public function insert_query($table, $columns, $values){

    $query="insert into $table ("; 

    //as colunas a alterar
    for($i=0; $i < count($columns)-1 ; $i++ ){
        $query.=$columns[$i].",";
    }
    $query.=end($columns).") values (";


    //os valores a ser inseridos
    for($i=0; $i < count($values)-1 ; $i++ ){
        $query.="'".$values[$i]."',";
    }
    $query.="'".end($values)."');";

    return $this->pass_query($query);
}


public function search_query($column="*", $table, $condition){
    $query="select $column from $table where $condition;"; 

    return $this->pass_query($query);
}


public function pass_query($query){
    $result=false;

    if($this->connection_set){
        $execute=true;

        try{
            $prepare=$this->conn->prepare($query);
        }catch(PDOException $rip){
            echo 'invalid query';
            $execute=false;
        }

        if($execute){
            $prepare->execute();
            $result=$prepare->fetchAll();
        }

    }else{
        echo 'no connection';
    }

    return $result;
}


private function set_default_credentials(){

    $this->host = 'localhost';
    $this->db = 'ponchaadvisor';
    $this->charset = 'utf8';

    $this->user = 'root';
    $this->pass = '';
}

private function set_connection(){

    $success=true;

    
    $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";


    try{
        $this->conn = new PDO($dsn, $this->user, $this->pass);   
    }catch(PDOException $rip){
        echo 'couldnt connect to db';
        $success=false;
    }

    $this->connection_set=$success;
    return $success;
}



} //end class

?>
