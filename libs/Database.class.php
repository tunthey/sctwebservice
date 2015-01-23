<?php


class Database extends PDO{

    private  $_db_driver;
    private  $_db_host;
    private  $_db_port;
    private  $_db_name;
    private  $_db_user;
    private  $_db_password;
    private  $_dsn;
    private  $_dbh;
    private  $_stmt;
    private  $_sql;

    function __construct(){
        if (!extension_loaded('pdo'))
            throw new DatabaseException("The pdo extension is not enabled");
    }

    function  __destruct(){
        $this->dbh = null;
    }
    
    public function set_variables($db_driver, $db_host, $db_port, $db_name, $db_user, $db_password){
        $this->_db_driver = $db_driver;
        $this->_db_host = $db_host;
        $this->_db_port = $db_port;
        $this->_db_name = $db_name;
        $this->_db_user = $db_user;
        $this->_db_password = $db_password;

        if (!extension_loaded('pdo_' . $this->_db_driver))
            throw new DatabaseException('The given pdo driver s not enabled');

        $this->_dsn = $this->_db_driver . ':host=' . $this->_db_host . '; port='.$this->_db_port .'; dbname=' . $this->_db_name;
        self::_establish_connection();
    }

    protected function _establish_connection(){
        try{
            $this->_dbh = parent::__construct($this->_dsn, $this->_db_user, $this->_db_password, array(
                parent::ATTR_PERSISTENT => true,
                parent::ATTR_ERRMODE => parent::ERRMODE_EXCEPTION
            ));
        }
        catch(PDOException $e){
            $baseLogger = new Logger(LOG_PATH . 'error.log');
            $baseLogger->log($e->__toString(), Logger::FATAL);
        }
    }

    public function select($table, array $select_fields = null, $conditions = null,
                           array $order = null, array $limit = null){
        if ($select_fields == null)
            $select_fields = '*';
        else
            $select_fields = implode(", ", array_values($select_fields));

        if ($conditions == null)
            $conditions = "";
        else
            $conditions = " WHERE " . $conditions;

        if (count($order) > 2)
            throw new DatabaseException("Order array can have only 2 element");
        if ($order == null)
            $order = "";
        else
            $order = " ORDER BY " . implode(" ", array_values($order));

        if ($limit == 0)
            $limit  = '';
        else
            $limit = " LIMIT " . implode(", ", array_values($limit));

        try{
            $this->_sql = "SELECT " . $select_fields . " FROM " . $table . $conditions . $order . $limit;
            $this->_filter_sql_();
            return $this->_query();
        }
        catch (PDOException $e){
            $baseLogger = new Logger(LOG_PATH . 'error.log');
            $baseLogger->log($e->__toString(), Logger::FATAL);
        }
    }

    public function select_free($sql){
        try{
            $this->_sql = $sql;
            $this->_filter_sql_();
            return $this->_query();
        }

        catch (PDOException $e){
            $baseLogger = new Logger(LOG_PATH . 'error.log');
            $baseLogger->log($e->__toString(), Logger::FATAL);
        }
    }

    public function update($table, array $update, $condition){
        parent::beginTransaction();
        $field_value ='';
        foreach ($update as $k => $v)
            $field_value .= ',' . $k . '=' . $v;
        $field_value = substr($field_value, strlen(','));
        $this->_sql = 'UPDATE ' . $table . ' SET ' . $field_value . ' WHERE ' . $condition;
        $this->_filter_sql_();
        return $this->_stmt = parent::prepare($this->_sql);
    }

    public function insert($table, array $table_fields, array $values){
        parent::beginTransaction();
        if (count($table_fields) != count($values))
            throw new DatabaseException('Size of $fields array and $values must be equal');
        $table_fields = implode(", ", array_values($table_fields));
        $values = implode(", ", array_values($values));
        $this->_sql = 'INSERT INTO ' . $table . ' (' . $table_fields . ') VALUES (' . $values . ')';
        return $this->_stmt = parent::prepare($this->_sql);
    }

    public function delete($table, $condition){
        parent::beginTransaction();
        $this->_sql = 'DELETE FROM ' . $table . ' WHERE ' . $condition;
        $this->_filter_sql_();
        return $this->_stmt = parent::prepare($this->_sql);
    }

    protected function _filter_sql_(){
        #TODO This function can be improved
        $this->_sql = preg_replace('/\s\s+|\t\t+/', ' ', trim($this->_sql));

        //Now control for sql injection. Union, sql comment, sleep and benchmark are best practises for sql injection.
        if (strpos($this->_sql, 'union') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $this->_sql) != 0)
            throw new DatabaseException("SQL statement contains malformed input (union)");
        elseif (strpos($this->_sql, '/*') > 2 || strpos($this->_sql, '--') !== false || strpos($this->_sql, ';') !== false)
            throw new DatabaseException("SQL statement contains malformed input (comment)");
        elseif (strpos($this->_sql, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $this->_sql) != 0)
            throw new DatabaseException("SQL statement contains malformed input (sleep)");
        elseif (strpos($this->_sql, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $this->_sql) != 0)
            throw new DatabaseException("SQL statement contains malformed input (benchmark)");
        elseif (preg_match('~\([^)]*?select~s', $this->_sql) != 0)
            throw new DatabaseException("SQL statement contains malformed input");
        else{
            return $this;
        }
    }

    public function get_next_auto_increment($table_name){

        $stmt = parent::prepare("SHOW TABLE STATUS LIKE :table_name");
        $stmt->bindValue(':table_name', $table_name, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['Auto_increment'];
    }

    protected function _query(){
        return parent::query($this->_sql)->fetch(parent::FETCH_ASSOC);
    }
    public function  get_sql(){
        return $this->_sql;
    }
}