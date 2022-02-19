<?php

class DB_Access extends Logging {
    /** PDO DB access object
     * @var PDO */
    protected PDO $_db;

    /** The driver for the database (mysql, sqlite, etc)
     * @var string */
    protected string $_driver;

    /** The hostname where the database is located
     * @var string */
    protected string $_hostname;

    /** The port to connect to the database
     * @var int */
    protected int $_port;

    /** The database name
     * @var string */
    protected string $_database;

    /** The user to access the database (if any)
     * @var string|null */
    protected ?string $_user;

    /** The password to access the database (if any)
     * @var string|null */
    protected ?string $_password;

    /** The number of started transactions, will only commit or rollback if there's one active transaction left
     * @var int */
    protected int $_intransaction;

    /**
     * Get a DB_Access instance to access the underlying database
     *
     * @param string $driver The driver for the database (mysql, sqlite, etc)
     * @param string $hostname The hostname where the database is located
     * @param integer $port The port to connect to the database
     * @param string $db_name The database name
     * @param string|null $user The user to access the database (if any)
     * @param string|null $pwd The password to access the database (if any)
     */
    public function __construct(string $driver, string $hostname, int $port, string $db_name, ?string $user=null, ?string $pwd=null){
        $this->_driver = $driver;
        $this->_hostname = $hostname;
        $this->_port = $port;
        $this->_user = $user;
        $this->_password = $pwd;
        $this->_database = $db_name;
        $this->_intransaction = 0;
        parent::__construct();

        $this->connect();
    }

    /**
     * Connect to database
     *
     * @return boolean true if connection could be established, otherwise false
     */
    private function connect(): bool {
        switch($this->_driver){
            case 'mysql':
            case 'pgsql':
                $dsn = $this->_driver.":dbname=".$this->_database.";host=".$this->_hostname;
                if($this->_port)
                    $dsn .= ";port=".$this->_port;
                break;
            case 'sqlite':
                $dsn = $this->_driver.":".$this->_database;
                break;
        }
        
        $this->_db = new PDO($dsn, $this->_user, $this->_password);

        switch($this->_driver){
            case 'mysql':
                $this->_db->exec('SET NAMES utf8');
                break;
            case 'sqlite':
                $this->_db->exec('PRAGMA foreign_keys = ON');
                break;
        }

        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return true;
    }

    /**
     * Starts a transaction
     *
     * @return void
     */
    public function startTransaction(): void {
        if($this->_intransaction == 0){
            $this->_db->beginTransaction();
        }
        $this->_intransaction++;
    }

    /**
     * Rollbacks a transaction
     *
     * @return void
     */
    public function rollbackTransaction(): void {
        if($this->_intransaction == 1){
            $this->_db->rollBack();
        }
        $this->_intransaction--;
    }

    /**
     * Commits a transaction
     *
     * @return void
     */
    public function commitTransaction(): void {
        if($this->_intransaction == 1){
            $this->_db->commit();
        }
        $this->_intransaction--;
    }

    /**
     * Return the ID of the last instert record.
     * 
     * NOTE THAT if using transactions, you shoud call this method **BEFORE YOU COMMIT THE TRANSACTION**  
     * (source: https://www.php.net/manual/en/pdo.lastinsertid.php#107622)
     *
     * @return int ID used in last autoincrement
     */
    public function getInsertID(): int {
        return intval($this->_db->lastInsertId());
    }

    /**
     * Get the last error message
     *
     * @return string
     */
    public function getErrorMsg(): string {
        return strval($this->_db->errorInfo()[2]);
    }

    /**
     * Replace the tokens ":param" with their values with PDOStatement::bindValue().
     *
     * @param string $queryStr A string with parameters
     * @param array $substitutions An array with the substitutions for the parameters
     * in the query.
     * 
     * @return PDOStatement Returns a statement to execute.
     */
    private function replaceTokens(string $queryStr, array $substitutions){
        try{
            $statement = $this->_db->prepare($queryStr);
            if($statement == false){
                $this->error_logger->error("Error preparing statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
                return false;
            }
        }catch(PDOException $e){
            $this->error_logger->error("Error preparing statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
            $this->error_logger->error($e->getMessage());
            return false;
        }
        
        try{
            foreach($substitutions as $param => $value){
                $statement->bindValue($param, $value);
            }
        }catch(PDOException $e){
            $this->error_logger->error("Error binding values", array("queryStr" => $queryStr, "substitutions" => $substitutions));
            $this->error_logger->error($e->getMessage());
            return false;
        }

        return $statement;
    }

    /**
     * Function to execute a SQL query and return the result as an array.
     * If you want to execute a SQL query without returning anything (like UPDATE, INSERT, etc)
     * please use getResultPrepared() instead.
     * 
     * The $queryStr must be a query to use with the variables to substitute
     * like :param. The array $substitutions contains the :param and the value
     * to substitute. An example would be:
     * $queryStr = "SELECT * FROM exampleTable WHERE id LIKE :id";
     * $substitutions = (":id" => 15877);
     *
     * @param string $queryStr String with the query and the parameters like :param.
     * @param array $substitutions Array with the parameters :param and their substitutions. Case sensitive.
     * 
     * @return array|false If everything goes OK, the array with the result is returned
     * (empty if no result is returned), on error returns false.
     */
    function getResultArrayPrepared(string $queryStr, array $substitutions=[]){
        $resArr = array();
        
        $statement = $this->replaceTokens($queryStr, $substitutions);

        if(is_bool($statement)){
            $this->error_logger->error("Error with database statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
            $this->error_logger->error($this->getErrorMsg());

            return false;
        }

        try{
            $res = $statement->execute();
            if($res === false){
                $this->error_logger->error("Error executing statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
                $this->error_logger->error(print_r($statement->errorInfo(), true));
                $this->error_logger->error($this->getErrorMsg());
                return false;
            }
            $resArr = $statement->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            $this->error_logger->error("Error executing statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
            $this->error_logger->error("Statement error info: ".print_r($statement->errorInfo(), true));
            $this->error_logger->error("errorMsg: ".$this->getErrorMsg());
            $this->error_logger->error("Exception message: ".$e->getMessage());
            return false;
        }

        
        return $resArr;
    }

    /**
     * Execute SQL query without expecting any result.
     * This function does not return the result.
     * 
     * If you want to execute a SQL query like SELECT and get the result 
     * use getResultArrayPrepared() instead.
     * 
     * @param string $queryStr String with the query and the parameters like :param.
     * @param array $substitutions Array with the parameters :param and their substitutions. The names are case sensitives.
     * 
     * @return boolean Returns true if the query was executed correctly, false otherwise.
     */
    function getResultPrepared(string $queryStr, array $substitutions=[]){
        $res = null;

        $statement = $this->replaceTokens($queryStr, $substitutions);

        if(is_bool($statement)){
            $this->error_logger->error("Error with database statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
            $this->error_logger->error($this->getErrorMsg());

            return false;
        }

        try{
            $res = $statement->execute();
            if($res === false){
                $this->error_logger->error("Error executing statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
                $this->error_logger->error("Statement error info ".print_r($statement->errorInfo(), true));
                $this->error_logger->error("errorMsg: ".$this->getErrorMsg());
                return false;
            }
        }catch(PDOException $e){
            $this->error_logger->error("Error executing statement", array("queryStr" => $queryStr, "substitutions" => $substitutions));
            $this->error_logger->error("Statement error info: ".print_r($statement->errorInfo(), true));
            $this->error_logger->error("errorMsg: ".$this->getErrorMsg());
            $this->error_logger->error("Exception message: ".$e->getMessage());

            return false;
        }
        
        return true;
    }
}

?>