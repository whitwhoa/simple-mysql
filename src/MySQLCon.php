<?php

namespace commanderZiltoid\SimpleMySQL;


use PDO;
use PDOException;

class MySQLCon {


    private $_show_errors;
    private $_mysql;
    private $_query;
    private $_bind;
    private $_resultset;
    private $_fetch_style;
    private $_single;
    private $_statement;


    function __construct($con, $show_errors = FALSE){
        $this->_show_errors = $show_errors;
        try{
            $this->_mysql = new PDO('mysql:host=' . $con[0] . ';dbname=' . $con[3] . ';', $con[1], $con[2]);
            if($this->_show_errors){
                $this->_mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (PDOException $e) {
            if($this->_show_errors){
                echo 'PDOException: ' . $e->getMessage() . '<br/>';
            }
            die("An error occurred while processing this request");
        }
    }

    /**
     * Close connection on destruct
     */
    function __destruct() {
        $this->close_connection();
    }

    /**
     * Set private property $_query equal to value passed via parameter $query, and
     * $_bind equal to value passed via parameter $bind.
     *
     * Variables will be used to generate result set via method called further down the pipe
     *
     * @param $query
     * @param array $bind
     * @return MySQLCon
     */
    public function query($query, $bind = array()) {
        $this->_query = $query;
        $this->_bind = $bind;
        return $this;
    }

    /**
     *
     * @return MySQLCon
     */
    public function asArray() {
        $this->_fetch_style = 'FETCH_ASSOC';
        $this->_single = false;
        return $this;
    }

    /**
     *
     * @return MySQLCon
     */
    public function singleAsArray() {
        $this->_fetch_style = 'FETCH_ASSOC';
        $this->_single = true;
        return $this;
    }

    /**
     *
     * @return MySQLCon
     */
    public function asObj() {
        $this->_fetch_style = 'FETCH_OBJ';
        $this->_single = false;
        return $this;
    }

    /**
     *
     * @return MySQLCon
     */
    public function singleAsObj() {
        $this->_fetch_style = 'FETCH_OBJ';
        $this->_single = true;
        return $this;
    }

    /**
     *  Execute
     */
    public function exec() {
        $qt = strtolower(explode(' ', trim($this->_query))[0]);
        if(!in_array($qt, ['select', 'insert', 'update', 'delete'])){
            if($this->_show_errors){
                echo 'Query not a select, insert, update, delete, or unable to parse query type';
                die;
            }
            return false;
        }
        switch($qt){
            case 'select':
                $this->_generate_mysql_statement();
                $this->_generate_mysql_resultset();
                if($this->_single){
                    return $this->_resultset[0];
                }
                return $this->_resultset;
            case 'insert':
                $this->_generate_mysql_statement();
                return $this->_mysql->lastInsertId();
            case 'update':
            case 'delete':
                $this->_generate_mysql_statement();
                return;
        }
    }

    // useful when query utilizes IN()
    public function generate_bind_string_for_array($bindArray = array()){
        $returnString = '';
        $count = 1;
        foreach($bindArray as $val) {
            $returnString .= '?';
            if($count != count($bindArray)){
                $returnString .= ",";
            }
            $count++;
        }
        return $returnString;
    }

    public function close_connection(){
        $this->_mysql = null;
    }

    public function get_connection_status() {
        return $this->_mysql->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    }

    /**
     * Automatically generate the prepared statement based on the variable types
     * provided in parameter $bindArray and execute
     *
     * @return void
     */
    private function _generate_mysql_statement(){
        $this->_statement = $this->_mysql->prepare($this->_query);
        if(count($this->_bind) > 0){
            for($i = 0; $i < count($this->_bind); $i++){
                $type = gettype($this->_bind[$i]);
                if($type === 'integer'){
                    $this->_statement->bindParam(($i + 1), $this->_bind[$i], PDO::PARAM_INT);
                }
                elseif($type === 'NULL'){
                    $this->_statement->bindParam(($i + 1), $this->_bind[$i], PDO::PARAM_NULL);
                }
                else {
                    // if not integer or null then default to string
                    // http://php.net/manual/en/pdo.constants.php
                    $this->_statement->bindParam(($i + 1), $this->_bind[$i], PDO::PARAM_STR);
                }
            }
        }
        $this->_statement->execute();
    }

    /**
     * Generate a result set using the provided $statement and $fetch_style
     *
     * @return void
     */
    private function _generate_mysql_resultset(){
        if($this->_fetch_style === 'FETCH_ASSOC'){
            while($row = $this->_statement->fetch(PDO::FETCH_ASSOC)){
                $this->_resultset[] = $row;
            }
        }
        elseif($this->_fetch_style === 'FETCH_OBJ'){
            while($row = $this->_statement->fetch(PDO::FETCH_OBJ)){
                $this->_resultset[] = $row;
            }
        }
        else {
            if($this->_show_errors){
                echo "PDO fetch style not supported";
                die;
            }
        }
    }


/* END OF CLASS
*******************************************************************************/
}