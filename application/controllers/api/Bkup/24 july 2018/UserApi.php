<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('./application/helpers/jwt_helper.php');


class UserApi extends CI_Controller {


	public function __construct()
    {
    	
        parent::__construct();
        $this->load->model('user_model');
       

        $header = $this->input->request_headers();

        if( !count($header['token']) ){
            
            $error = array("status"=>0, "message" => "Authentication failed..");
            echo json_encode($error);
            exit;
        }


        if( is_array($this->user_model->validatetoken()) && count($this->user_model->validatetoken()) == 0 ){

            $error = array("status"=>0, "message" => "Authentication Failed..");
            echo json_encode($error);
            exit;
        }else{
             

            $this->token = $this->user_model->validatetoken(); 
        }
                
       
    }

    public function profile(){

        $token  = $this->token;

        $user   = $this->user_model->getuserById( $token['user_id'] ); 


        if( is_array($user) && count( $user ) > 0 ){
            $success = array('status' => 1, 'profile' => $user );
            echo json_encode($success);
            exit;
        }else{
            $error = array('status' => 0, 'message' => 'Authentication Failed' );
            echo json_encode($error);
            exit;
        } 
            	
    }


    public function updateSelfie(){

        $token      = $this->token;

        $user_id    = $this->input->post('user_id');

        

        if( $user_id == $token['user_id']){

            $selfietype = $this->user_model->getUserSetting( $user_id );

            if( $_FILES ){
          
                if( array_key_exists("selfie1",$_FILES) && $_FILES['selfie1']['name'] ){

                    $path   = $_FILES['selfie1']['name'];

                    if( $path){

                        //image upload code
                        $new_name = time().uniqid().'.'.pathinfo($path, PATHINFO_EXTENSION); 
                        $filepath = $new_name;


                        $config['file_name']            = $new_name;
                        $config['upload_path']          = 'assets/userLibrary/';
                        $config['allowed_types']        = '*';
                        $config['max_size']             = 200000;
                       // $config['max_width']            = 10000;
                       // $config['max_height']           = 10000;

                        $this->load->library('upload', $config);

                        if ( ! $this->upload->do_upload('selfie1'))
                        {
                            $error = array('error' => $this->upload->display_errors());
                            $error_msg[9]  = $this->upload->display_errors(); 
                            print_r($error);
                              
                        }else{
                            
                                $datasuccess = array('upload_data' => $this->upload->data());
                                $user_path = $filepath;

                                //code to resize image

                                $imgconfig['image_library'] = 'gd2';
                                $imgconfig['source_image'] = 'assets/userLibrary/'.$filepath;
                                $imgconfig['create_thumb'] = TRUE;
                                $imgconfig['maintain_ratio'] = TRUE;
                                $imgconfig['width']         = 180;
                                $imgconfig['height']       = 60;

                                $this->load->library('image_lib', $imgconfig);

                                // resize image
                                 $this->image_lib->resize();
                                 // handle if there is any problem
                                 if ( ! $this->image_lib->resize()){
                                  echo $this->image_lib->display_errors();
                                  exit;
                                 }
                                

                                $ext = explode(".",$filepath);

                                $user_path = $ext[0]."_thumb.".$ext[1];

                                
                                //code to resize image end

                                if( @file_exists("assets/pantryLibrary/".$filepath) ){
                                    @unlink( "assets/pantryLibrary/".$filepath );
                                }


                                $adddata = array(
                                                    "profile_image"         => $user_path,
                                            );

                                $condition = array( 
                                                        "user_id"               => $user_id
                                                    );
                                if($selfietype['selfie_type'] == 'same'){

                                    $adddata['profile_image1'] = $user_path;
                                }

                                $update = $this->basic_model->update("sh_user", $adddata, $condition);

                                if( $update ){
                                    $success = array('status' => 1, 'message' => 'selfie updated successfully' );
                                    echo json_encode($success);
                                    exit;
                                }else{

                                    $error = array('status' => 0, 'message' => 'selfie not updated please try again' );
                                    echo json_encode($error);
                                    exit; 
                                }   
                            }
                    }
                }

                if( array_key_exists("selfie2",$_FILES) && $_FILES['selfie2']['name'] ){

                    $path1  = $_FILES['selfie2']['name'];

                    if( $path1){

                        //image upload code
                        $new_name = time().uniqid().'.'.pathinfo($path1, PATHINFO_EXTENSION); 
                        $filepath = $new_name;

                        $config['file_name']            = $new_name;
                        $config['upload_path']          = 'assets/userLibrary/';
                        $config['allowed_types']        = '*';
                        $config['max_size']             = 200000;
                       // $config['max_width']            = 10000;
                       // $config['max_height']           = 10000;

                        $this->load->library('upload', $config);

                        if ( ! $this->upload->do_upload('selfie2'))
                        {
                            $error = array('error' => $this->upload->display_errors());
                            $error_msg[9]  = $this->upload->display_errors(); 
                            print_r($error);
                              
                        }else{
                            
                                $datasuccess = array('upload_data' => $this->upload->data());
                                $user_path1 = $filepath;

                                //code to resize image

                                $imgconfig['image_library'] = 'gd2';
                                $imgconfig['source_image'] = 'assets/userLibrary/'.$filepath;
                                $imgconfig['create_thumb'] = TRUE;
                                $imgconfig['maintain_ratio'] = TRUE;
                                $imgconfig['width']         = 180;
                                $imgconfig['height']       = 60;

                                $this->load->library('image_lib', $imgconfig);

                                // resize image
                                 $this->image_lib->resize();
                                 // handle if there is any problem
                                 if ( ! $this->image_lib->resize()){
                                  echo $this->image_lib->display_errors();
                                  exit;
                                 }
                                

                                $ext = explode(".",$filepath);

                                $user_path1 = $ext[0]."_thumb.".$ext[1];

                                
                                //code to resize image end

                                if( @file_exists("assets/pantryLibrary/".$filepath) ){
                                    @unlink( "assets/pantryLibrary/".$filepath );
                                }

                                $adddata = array(
                                                    "profile_image1"         => $user_path1,
                                            );

                                $condition = array( 
                                                        "user_id"               => $user_id
                                                    );


                                $upload = $this->basic_model->update("sh_user", $adddata, $condition);  

                                if( $upload ){
                                    $success = array('status' => 1, 'message' => 'selfie updated successfully' );
                                    echo json_encode($success);
                                    exit;
                                }else{

                                    $error = array('status' => 0, 'message' => 'selfie not updated please try again' );
                                    echo json_encode($error);
                                    exit; 
                                } 

                        }


                    }
                }

            }else{
                $error = array('status' => 0, 'message' => 'Please capture a selfie' );
                echo json_encode($error);
                exit;
            } 

         die();

           
        }
        
    }


    public function updateSelfieType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type       = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == 'same' || $type == 'independent' ){

                $adddata = array(
                                                    "selfie_type"            => $type,
                                                    "updated_on"             => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }


    public function updateGoShoppingAudioType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type       = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == '0' || $type == '1' ){

                $adddata = array(
                                                    "audio_goshopping"         => $type,
                                                    "updated_on"               => time() 

                                            );

                $condition = array( 
                                "user_id"               => $user_id
                            );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 


            }else{

                $error = array('status' => 0, 'message' => 'Invalid Audio Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }



    public function updateShoppingAudioType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type       = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == '0' || $type == '1' ){

                $adddata = array(
                                                    "audio_shopping"            => $type,
                                                    "updated_on"                => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Audio Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }



    public function updateShoppingType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type       = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == 'repeated' || $type == 'column' ){

                $adddata = array(
                                                    "shoppinglist_type"            => $type,
                                                    "updated_on"                   => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }
	


    public function updateMoneyManagementAudioType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type       = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == '0' || $type == '1' ){

                $adddata = array(
                                                    "audio_moneymanagement"         => $type,
                                                    "updated_on"                    => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Audio Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }



    public function updateMymoneyAudioType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type     = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == '0' || $type == '1' ){

                $adddata = array(
                                                    "audio_moneyihave"         => $type,
                                                    "updated_on"               => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Audio Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }




    public function updateLowMoneyWarningType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type     = $this->input->post('type');

        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication Failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == 'alarm' || $type == 'tune' ){

                $adddata = array(
                                                    "lowmoney_status"          => $type,
                                                    "updated_on"               => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

               
                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }



    public function updateLanguageType(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $type     = $this->input->post('type');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $type     = $this->input->post('type');


            if( $type == 'single' || $type == 'dual' ){

                $adddata = array(
                                                    "language"                => $type,
                                                    "updated_on"              => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid Type' );
                echo json_encode($error);
                exit;
            }


            
        }
    }



    public function updateName(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $name     = $this->input->post('name');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $name     = $this->input->post('name');


            if( $name ){

                $adddata = array(
                                                    "name"                => $name,
                                                    "updated_on"          => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_user", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid name' );
                echo json_encode($error);
                exit;
            }


            
        }
    }




    public function updateLowMoney(){


        $token      = $this->token;

        $user_id    = $this->input->post('user_id');
        $money       = $this->input->post('money');

       
        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $money     = $this->input->post('money');


            if( $money && is_numeric($money) ){

                $adddata = array(
                                                    "lowmoney_value"      => $money,
                                                    "updated_on"          => time() 
                                            );

                $condition = array( 
                                        "user_id"               => $user_id
                                    );


                $upload = $this->basic_model->update("sh_setting", $adddata, $condition);

                if( $upload ){
                    $success = array('status' => 1, 'message' => 'updated successfully' );
                    echo json_encode($success);
                    exit;
                }else{

                    $error = array('status' => 0, 'message' => 'Not updated please try again' );
                    echo json_encode($error);
                    exit; 
                } 

            }else{

                $error = array('status' => 0, 'message' => 'Invalid money' );
                echo json_encode($error);
                exit;
            }


            
        }
    }




    public function getlanguages(){

        $language = $this->basic_model->getlanguages();

        if(is_array($language) && count( $language ) > 0 ){
                $success = array('status' => 1, 'languages' => $language );
                echo json_encode($success);
                exit;
        }else{

                $error = array('status' => 0, 'message' => 'No languages found' );
                echo json_encode($error);
                exit;
            }
    }


    public function getUserSetting(){

        $token      = $this->token;

        $user_id    = $this->input->post('user_id');

        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $setting = $this->user_model->getUserSetting( $user_id );

            if(is_array($setting) && count( $setting ) > 0 ){
                    $success = array('status' => 1, 'setting' => $setting );
                    echo json_encode($success);
                    exit;
            }else{

                    $error = array('status' => 0, 'setting' => 'No setting found' );
                    echo json_encode($error);
                    exit;
            }
        }

        
    }


    public function getUserTotalMoney(){

        $token      = $this->token;

        $user_id    = $this->input->post('user_id');

        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            $money = $this->user_model->getUserTotalMoney( $user_id );

            if(is_array($money) && count( $money ) > 0 ){
                    $success = array('status' => 1, 'money' => $money );
                    echo json_encode($success);
                    exit;
            }else{

                    $error = array('status' => 0, 'setting' => 'No money found' );
                    echo json_encode($error);
                    exit;
            }
        }

    }

    public function updateusermoney(){


        $token      = $this->token;

        $user_id            = $this->input->post('user_id');
        $cent5coin          = $this->input->post('5centcoin');
        $cent10coin         = $this->input->post('10centcoin');
        $cent20coin         = $this->input->post('20centcoin');
        $cent50coin         = $this->input->post('50centcoin');
        $dollar1coin        = $this->input->post('1dollarcoin');
        $dollar2coin        = $this->input->post('2dollarcoin');
        $dollar5note        = $this->input->post('5dollarnote');
        $dollar10note       = $this->input->post('10dollarnote');
        $dollar20note       = $this->input->post('20dollarnote');
        $dollar50note       = $this->input->post('50dollarnote');
        $dollar100note      = $this->input->post('100dollarnote');

        $money      = number_format($this->input->post('money'), 2, '.', '');

        $total_amount = ($cent5coin / 100) + ($cent10coin / 100) + ($cent20coin / 100 ) + ( $cent50coin / 100 ) + ($dollar1coin)  + ($dollar2coin * 2) + ($dollar5note * 5 )+ ($dollar10note *10 )+ ($dollar20note * 20)  + ($dollar50note * 50 ) + ($dollar100note * 100);

        if( $user_id != $token['user_id'] ){

            $error = array('status' => 0, 'message' => 'Authentication failed' );
            echo json_encode($error);
            exit;
        }else{

            

            if( $total_amount == $money ){

                 $data        = array(      'total_amount'                  => $money,
                                            'total_5cent_coin'               => $cent5coin,
                                            'total_10cent_coin'              => $cent10coin,
                                            'total_20cent_coin'              => $cent20coin,
                                            'total_50cent_coin'              => $cent50coin,
                                            'total_1dollar_coin'             => $dollar1coin,
                                            'total_2dollar_coin'             => $dollar2coin,
                                            'total_5dollar_note'             => $dollar5note,
                                            'total_10dollar_note'            => $dollar10note,
                                            'total_50dollar_note'            => $dollar50note,
                                            'total_100dollar_note'           => $dollar100note,
                                            'updated_on'                    => time()

                            );

            $condition   = array( 'user_id' => $user_id );

            $update = $this->basic_model->update('sh_purse', $data, $condition );

            }else{

                $error = array('status' => 0, 'message' => 'Total amount not match' );
                echo json_encode($error);
                exit;
            }
 
           

            if( $update > 0 ){
                    $success = array('status' => 1, 'message' => 'Update Successfully' );
                    echo json_encode($success);
                    exit;
            }else{

                    $error = array('status' => 0, 'message' => 'Try agian' );
                    echo json_encode($error);
                    exit;
            }
        }

    }


}
