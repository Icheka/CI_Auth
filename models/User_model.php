<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model("Tables");
    }
    
    /**
     * createUser(array $data): void
     * 
     *  Creates a new user by inserting user data in the database
     */
    public function createUser($data) {
        $this->db->insert($this->Tables->get("users"), $data);
    }
    
    /**
     * fetchUser(string $param, string $column): array
     * 
     * Fetches all info about a user from the database by matching a value (e.g an email address) to a column (e.g the 'email' column)
     */
    public function fetchUser(string $param, string $column) {
        $this->db->select("*");
        $this->db->from($this->Tables->get("users"));
        $this->db->where($column, $param);
        return $this->db->get()->result_array();
    }
    
    /**
     * isUserExists(): bool
     * 
     * Check whether a user exists.
     * Return true/false.
     */
     public function isUserExists($param) {
         $this->db->select("*");
         $this->db->where('email', $param);
         $data = $this->db->get($this->Tables->get("users"))->result_array();
         
         return count($data) !== 0 ? true : false;
     }
    
}