<?php 
require_once('./application/helpers/jwt_helper.php');

class User_model extends CI_Model 
{
    
    function __construct() {
    
        parent::__construct();
        $this->load->database();
    }

    public function getUser( $params = array() ){

        
        $this->db->select('*');
        $this->db->from('sh_user');

        //searching code
        if( array_key_exists("name",$params) && !empty($params['name'])){

          $this->db->like('name', $params['name']);
        }

        //searching code end

        $order = "DESC";
        $order_by = "created_on";
        
        $this->db->order_by($order_by, $order );
        
         
        if( is_array($params) && $params['returnType'] == 'count'){

            $result = $this->db->get()->num_rows();
        }else{

           
            if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit'],$params['start']);
            }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit']);
            }


            $query = $this->db->get();
            $result = $query->result_array();
        }

        
        return $result;

    }


    public function getuserById($user_id){

        $this->db->select('*');
        $this->db->from('sh_user');
        $this->db->where('user_id', $user_id); 
        $this->db->where('status', '0');        
        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
    }


    public function checkUniqueUser( $email ){

        $this->db->select('*');
        $this->db->from('sh_user');
        $this->db->where('email', $email );        
        $query = $this->db->get();
        $result = $query->row_array();

        
        if( is_array($result) && count($result) > 0 ){

            return true;
        }else{
            return false;
        }

    }

    public function validateuser( $email , $password ){

        $this->db->select('*');
        $this->db->from('sh_user');
        $this->db->where('email', $email );        
        $this->db->where('password', $password );        
        $query = $this->db->get();
        $result = $query->row_array();

        
        if( is_array($result) && count($result) > 0 ){

            $payload    = ["name" => $result['name'], "email" =>$result['email'], "dob" =>$result['dob']];
            $key        = "secret";
            $token      = JWT::encode($payload, $key, 'HS256');

            return $token;
            
        }else{
            return false;
        }
    }


    public function validatetoken(){
        
        $jwt = $this->input->request_headers();
        $key = "secret";
        $token = JWT::decode($jwt['token'], $key, array('HS256'));

        $this->db->select('*');
        $this->db->from('sh_user');
        $this->db->where('email', $token->email );        
             
        $query = $this->db->get();
        $result = $query->row_array();

        if(is_array($result) && count($result) > 0 ){
            return $result;
        }else{
            return false;
        }
    }


    public function getUserSetting( $user_id ){

        $this->db->select('*');
        $this->db->from('sh_setting');
        $this->db->where('user_id', $user_id );        
        $query = $this->db->get();
        $result = $query->row_array();

        
        if( is_array($result) && count($result) > 0 ){

            return $result;
            
        }else{
            return false;
        }
    }


    public function getUserTotalMoney( $user_id ){

        $this->db->select('*');
        $this->db->from('sh_purse');
        $this->db->where('user_id', $user_id );        
        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
        
    }
  
}