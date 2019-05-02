<?php 


class Pantry_model extends CI_Model 
{
    
    function __construct() {
    
        parent::__construct();
        $this->load->database();
    }

    public function getPentry( $params = array() ){

        $this->db->select('*');
        $this->db->from('usersrole');


        //searching code
        if( array_key_exists("title",$params) && !empty($params['title'])){

          $this->db->like('title', $params['title']);
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


    public function getpentryById($pantry_id){

        $this->db->select('*');
        $this->db->from('usersrole');
        $this->db->where('id', $pantry_id);         
        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
    }

    public function isuniquename( $pantry_name, $pantry_id ){

        $this->db->select('*');
        $this->db->from('sh_pantry');
        $this->db->where('en_pantry ', $pantry_name);
        
        if( $pantry_id != '0' ){

            $this->db->where('id !=', $pantry_id);
        }

        $query = $this->db->get();
        $result = $query->row_array();

    
        if( is_array($result) && count($result) >0 ){
            return true;
        }else{
            return false;
        }
    }


    public function getpantriesforuser(){

        $this->db->select('*');
        $this->db->from('sh_pantry');
        $this->db->where('status', '1');  
        $this->db->order_by('created_on', 'DESC' );       
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }  
}