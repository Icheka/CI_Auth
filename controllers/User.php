<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->library('session');
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
        
        
    }
    
    /**
     * create_user(): void
     * 
     *  HTTP Method: POST
     *  Action: Create A New User
     * 
     *  $params --> Parameters to check for in POST payload.
     *              A request that is missing a parameter will be rejected.
     */ 
    public function create_user() {
        $params = array(
            'full_name',
            'email',
            'password',
            'created_at'
        );
        
        $data = $this->input->post();
        
        $data['created_at'] = date("YmdHis");
        
        if ($this->check_params($params, $data, true) == true) {
            // can continue with creating user as params are correct
            $data['pass'] = password_hash($data['password'], PASSWORD_BCRYPT); // To enable easy insertion in SQL-based DBs
            unset($data['password']);
        
            if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) == true) {
                // can continue with creating user as email is valid
                if ($this->user_model->isUserExists($data['email']) == false) {
                    // user does not exist. Can continue creating user
                    $data['user_id'] = date("Ymd") . mt_rand(10000000000000, 99999999999999);
                    $this->user_model->createUser($data);
                    $data = $this->user_model->fetchUser($data['email'], 'email');
                    $this->echo_success('Success', $data);
                    return;
                    die();
                    
                } else {
                    // user already exists. Fail gracefully with HTTP response
                    $this->echo_error('User Already Exists', 'A user with that email address already exists.', 409);
                    return;
                    die();
                }
            } else {
                // email is invalid. Fail gracefully with HTTP response
                $this->echo_error('Invalid Email Supplied', 'An invalid email was supplied.', 406);
                return;
                die();
            }
        } else {
            $this->echo_error('Incomplete Parameters In Request Body', 'Some required parameters are missing in your request body.', 406);
        }
    }
    
    /**
     * signin(): void
     * 
     * HTTP Method: POST
     * Action: Handles sign in for users
     */
    public function signin() {
        $data = $this->input->post();
        $params = array(
            'email',
            'password'
            );
        if ($this->check_params($params, $data, true) == true) {
            // passed params check. Can continue.
            $user = $this->admin_model->fetchUser($data['email'], 'email');
            
            if (count($user) == 0) {
                // user does not exist.
                $this->echo_error('Not Found', 'No user with that email address was found.', 404);
                
            } else {
                // user does exist.
                if (password_verify($data['password'], $user['pass']) == true) {
                    // passwords match!
                    // fetch user details and return.
                    // set user_id to session
                    $this->session->set_userdata('user_id', $user['user_id']);
                    
                    $this->echo_success('Success', $user);
                } else {
                    // passwords do not match!
                    $this->echo_error('Password Error', 'The passwords do not match!', 401);
                }
            }
        } else {
            // failed params check. Fail gracefully.
            $this->echo_error('Failed', 'The request was not well formed.', 406);
            return;
            die();
        }
    }
    
    // =======================
    // PRIVATES 
    
    private function echo_success(string $header, array $data, int $code=200) {
        http_response_code($code);
        echo json_encode(
            array(
                'header' => $header,
                'data' => $data,
                )
            );
        return;
        die();
    }
    
    private function echo_error(string $header, string $message, int $code=400) {
        http_response_code($code);
        echo json_encode(array('message' => $message));
        return;
        die();
    }
    
    private function check_params(array $params, array $test, bool $strict) {
        if ($strict == true) {
            if (count($test) !== count($params)) return false;
        }
        
        foreach($params as $key) {
            if (!isset($test[$key])) return false;
        }
        
        return true;
    }
    
}