<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PantryApi extends CI_Controller {


	public function __construct()
    {
    	
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('pantry_model');

        $header = $this->input->request_headers();

        if( !count($header['token']) ){
            
            $error = array("status"=>0, "message" => "Authentication failed..");
            echo json_encode($error);
            exit;
        }


        if( is_array($this->user_model->validatetoken()) && count($this->user_model->validatetoken()) == 0 ){

            $error = array("status"=>0, "message" => "Authentication Failed");
            echo json_encode($error);
            exit;
        }else{
             

            $this->token = $this->user_model->validatetoken(); 
        }
                

    }


    public function getpantry(){

    	$pantry = $this->pantry_model->getpantriesforuser();

    	if( is_array($pantry) && count( $pantry ) > 0 ){
            $success = array('status' => 1, 'pantry' => $pantry );
            echo json_encode($success);
            exit;
        }else{
            $error = array('status' => 0, 'message' => 'No pantry available' );
            echo json_encode($error);
            exit;
        } 
    }
	

}
