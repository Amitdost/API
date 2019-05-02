<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('./application/helpers/jwt_helper.php');


class SignupLoginApi extends CI_Controller {


	public function __construct()
    {
    	
        parent::__construct();
        $this->load->model('user_model');        
        $this->load->helper('jwt');
    }


    public function signup(){

    	$name 		= trim(addslashes($this->input->post('name')));
    	$email 		= trim(addslashes($this->input->post('email')));
    	$dob 		= trim(addslashes($this->input->post('dob')));
    	$password 	= trim(addslashes($this->input->post('password')));

    	
       	if(!$name){
			$error = array("status" =>0, "message" =>"Please enter Name");
			echo json_encode($error);
			exit;
		}

		if(!$email){
			$error = array("status" =>0, "message" =>"Please enter Email");
			echo json_encode($error);
			exit;
		}

		if(!$dob){
			$error = array("status" =>0, "message" =>"Please enter Date of birth");
			echo json_encode($error);
			exit;
		}

    	if(!$password){
			$error = array("status" =>0, "message" =>"Please enter 4 Digit Pin code");
			echo json_encode($error);
			exit;
		}


        if( $password ){

            if(strlen($password) != 4 ){

                $error = array("status" =>0, "message" =>"Please enter 4 Digit Pin code");
                echo json_encode($error);
                exit;
            }
        }

    	$isunique   = $this->user_model->checkUniqueUser( $email );

    	if( !$isunique ){

    		$table 		= 'sh_user';

    		$addeddata	=  array(

    								'name'  		=> $name,
    								'email' 		=> $email,
    								'dob'			=> time($dob),
    								'password' 		=> md5($password),
    						);

    		$id = $this->basic_model->insert( $table, $addeddata );

           if( $id ){

                    $addsettingdata =   array(
                                        'user_id'               =>  $id,
                                        'language'              =>  'single',
                                        'primary_language'      =>  'en',
                                        'secondary_language'    =>  'en',
                                        'lowmoney_status'       =>  'alarm',
                                        'selfie_type'           =>  'same',
                                        'audio_moneymanagement' =>  '0',
                                        'audio_shopping'        =>  '0',
                                        'audio_moneyihave'      =>  '0',
                                        'audio_goshopping'      =>  '0',
                                        'shoppinglist_type'     =>  'column',
                                        'created_on '           =>  time(),
                                        'updated_on '           =>  time(),

                                );

                    $idsh_setting = $this->basic_model->insert("sh_setting", $addsettingdata);

                    $addpursedata =   array(
                                        'user_id'               =>  $id,
                                        'created_on '           =>  time(),
                                        'updated_on '           =>  time(),
                                );

                    $idsh_purse = $this->basic_model->insert("sh_purse", $addpursedata);
            }



    		$payload 	= ["name" => $name, "email" =>$email, "dob" =>$dob];
			$key 		= "secret";
			$token 		= JWT::encode($payload, $key, 'HS256');
            $sucess     = array("status" => "1" , "token" => $token);
            echo json_encode($sucess);
    		exit;


    	}else{
    		$error = array("status" =>0, "message" =>"You allready registered plese login");
			echo json_encode($error);
			exit;
    	}

    }


    public function login(){

    	$email 		= trim(addslashes($this->input->post('email')));
    	//$dob 		= trim(addslashes($this->input->post('dob')));
    	$password 	= trim(addslashes($this->input->post('password')));


        if(!$email){
            $error = array("status" =>0, "message" =>"Please enter Email");
            echo json_encode($error);
            exit;
        }

        /*if(!$dob){
            $error = array("status" =>0, "message" =>"Please enter Date of birth");
            echo json_encode($error);
            exit;
        }*/

        if(!$password){
            $error = array("status" =>0, "message" =>"Please enter 4 Digit Pin code");
            echo json_encode($error);
            exit;
        }


        $isvalidate       = $this->user_model->validateuser( $email, md5($password) );

        if( $isvalidate ){

            $sucess     = array("status" => "1" , "token" => $isvalidate);
            echo json_encode($sucess);
            exit;
        }else{
            $error = array("status" =>0, "message" =>"Please enter valid email or password");
            echo json_encode($error);
            exit;
        }

    }
	

}
