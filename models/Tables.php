<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tables extends CI_Model {
    public function __construct() {
        parent::__construct();
        
        /**
         * Type: Array
         * 
         * Use: an array of pseudonyms for all tables in the database.
         *      Makes it possible to use abbreviations or multiple names for tables.
         *      Also (potentially) reduces the need for the engineer to remember every table name
         * 
         * Editable!
         */
        $this->tables = array(
            'users' => 'users'
            );
            
        /**
         * Type: Array
         * 
         * Use: Stores schema for database tables
         * 
         * Editable!
         */
        $this->schemas = array(
            'users' => 'sn: SERIAL<br>full_name: VARCHAR(120).NN<br>email: VARCHAR(120).NN<br>pass: CHAR(60).NN<br>created_at: DATETIME.NN<br>user_id: VARCHAR(20)'
            );
    }
    
    public function get($table) {
        return $this->tables[$table];
    }
    
    public function schema($table) {
        $table = $this->get($table);
        return $this->schemas[$table];
    }
    
}