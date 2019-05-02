<?php 


class Basic_model extends CI_Model 
{
    
        function __construct() {
            parent::__construct();
            $this->load->database();
        }

       public function update( $table, $data, $condition ){

	       $this->db->update( $table, $data, $condition );

	       return  $this->db->affected_rows();

       }

       public function insert( $table,  $data ){

        $this->db->insert( $table, $data);
        return $this->db->insert_id();

       }

       public function delete( $table, $condition ){

        $this->db->delete( $table,$condition );

        return $this->db->affected_rows();

       }

       public function validateAdmin( $email, $password ){

          $this->db->select('*');
          $this->db->from('sh_administrator');
          $this->db->where('admin_email', $email);
          $this->db->where('admin_password', $password);

          $query = $this->db->get();
          $result = $query->row_array();
          
          if( is_array($result) && count($result) > 0 ){
            return true;
          }else{
            return false;
          }

       }


      public function getAdmin(){

          $this->db->select('*');
          $this->db->from('sh_administrator');
         
          $query = $this->db->get();
          $result = $query->row_array();

          return $result;

      }

      public function getlanguages(){

          $this->db->select('*');
          $this->db->from('sh_languages');
         
          $query = $this->db->get();
          $result = $query->result_array();

          return $result;

      }


      public function getpages(){

          $this->db->select('*');
          $this->db->from('sh_content_management');
         
          $query = $this->db->get();
          $result = $query->result_array();

          return $result;

      }

      public function getpagesOnchange( $page_name ){

          $this->db->select('*');
          $this->db->from('sh_content_management');
          $this->db->where( 'page_name' , $page_name );
         
          $query = $this->db->get();
          $result = $query->row_array();

          return $result;

      }


}