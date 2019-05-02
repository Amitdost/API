<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'gtranslate.php';



class User extends CI_Controller {


	public function __construct()
    {
    	
        parent::__construct();
                
        if( !$this->session->userdata('admin') ){

			header("Location:".base_url()."admin");
			exit;
        }
        $this->perPage = 20;

        $this->load->model('user_model');
        $this->load->library('pagination');


    }
	
	public function index(){

		$data['title'] 	= "Users";
		$data['admin']  = $this->basic_model->getAdmin();
		
		$conditions['returnType'] = 'count';
        $totalRec = $this->user_model->getUser($conditions);

        if( $this->input->post('search_user')){

        	$conditions['name'] = $this->input->post('search_user');
        }


		//pagination config
        $config['base_url']    = '../admin-users';
        $config['uri_segment'] = 2;
        $config['total_rows']  = $totalRec;
        $config['per_page']    = $this->perPage;
        
        //styling
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['next_tag_open'] = '<li class="pg-next">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="pg-prev">';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        
        //initialize pagination library
        $this->pagination->initialize($config);
        
        //define offset
        $page = $this->uri->segment(2);

        $offset = !$page?0:$page;

        //get rows
        $conditions['returnType'] = '';
        $conditions['start'] = $offset;
        $conditions['limit'] = $this->perPage;
        
        $data['user']  = $this->user_model->getUser( $conditions );
        $data['pages']     = $this->pagination->create_links();



		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('user/user_list', $data );
		$this->load->view('superadmin/footer');
	}


	public function addUser(){

		if( $this->input->post("adduser") == "adduser"){

        	$user_email       	= trim(addslashes($this->input->post('user_email')));
        	$user_pin       	= trim(addslashes($this->input->post('user_pin')));
        	$user_name       	= trim(addslashes($this->input->post('user_name')));
        	$dob 				= trim(addslashes($this->input->post('dob')));
        	$user_visibility  	= trim(addslashes($this->input->post('user_visibility')));
        	

        
        	$path = $_FILES['user_path']['name'];
        	$path1 = $_FILES['user_path1']['name'];

        	
            $error_msg = array();
            $i = 0;


			if( !$user_name ){
				$error_msg[$i] = "Please enter user name";
                $i++;
			}

			if( !$dob ){
				$error_msg[$i] = "Please enter user date of birth";
                $i++;
			}

			
			if( $user_visibility == "" ){
				$error_msg[$i] = "Please select user visibility";
                $i++;
			}

			if( !$path ){
				$error_msg[$i] = "Please select user profile image to upload";
                $i;
			}

        	if( count($error_msg) == 0 ){

        		$user_path = " ";

        		$user_path1 = " ";
				
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

			            if ( ! $this->upload->do_upload('user_path'))
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
		        			}
        			}


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

		            if ( ! $this->upload->do_upload('user_path1'))
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

		            }


	       		}

		
	            $adddata = array(
	        		"name"                 	=> $user_name,
	        		"email"					=> $user_email,
	        		"password"				=> md5($user_pin),
	        		"profile_image"         => $user_path,
	        		"profile_image1"        => $user_path1,
	        		"dob"					=> time($dob),
	        		"status"            	=> $user_visibility,
	        		"created_on"            => time()
	        	);

	        	

	        	$id = $this->basic_model->insert("sh_user", $adddata);



	        	if( $id ){

	        		$addsettingdata =	array(
	        							'user_id'				=>	$id,
	        							'language'				=> 	'single',
	        							'primary_language'		=> 	'en',
	        							'secondary_language'	=> 	'en',
	        							'lowmoney_status'		=> 	'alarm',
	        							'selfie_type'			=> 	'same',
	        							'audio_moneymanagement'	=> 	'0',
	        							'audio_shopping'		=> 	'0',
	        							'audio_moneyihave'		=> 	'0',
	        							'audio_goshopping'		=> 	'0',
	        							'shoppinglist_type'		=> 	'column',
	        							'created_on	'			=> 	time(),
	        							'updated_on	'			=> 	time(),

	        					);

	        		$id = $this->basic_model->insert("sh_setting", $addsettingdata);
	        	}
	        	

                
	        	if( $id > 0 ){
	        		$success =  "User Added Successfully";
					$this->session->set_flashdata('success', $success );

                    header("Location:".base_url()."admin-users");
					exit;
	        	}else{
	        		$error_msg[0] = "Something went wrong. Please try again";
					$this->session->set_flashdata('error', $error_msg );

                    header("Location:".base_url()."admin-add-users");
					exit;

	        	}



        	}else{ 
        		$error = $error_msg;
				$this->session->set_flashdata('error', $error );

                 header("Location:".base_url()."admin-add-users");
				
				exit;

        	}	

        }

		$data['title'] = "Add User";

		$data['admin']  = $this->basic_model->getAdmin();
	
		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('user/add_user', $data );
		$this->load->view('superadmin/footer');
	}


	public function editusers($user_id){

		$data['title'] = "Edit User";
		$data['admin']  	= $this->basic_model->getAdmin();
		$data['user']  	= $this->user_model->getuserById($user_id);

		$user_path = $data['user']['profile_image'];

        $user_path1 = $data['user']['profile_image1'];

		if( $this->input->post("edituser") == "edituser"){

        	$user_email       	= trim(addslashes($this->input->post('user_email')));
        	$user_pin       	= trim(addslashes($this->input->post('user_pin')));
        	$user_name       	= trim(addslashes($this->input->post('user_name')));
        	$dob 				= trim(addslashes($this->input->post('dob')));
        	$user_visibility  	= trim(addslashes($this->input->post('user_visibility')));
        	

        
        	$path = $_FILES['user_path']['name'];
        	$path1 = $_FILES['user_path1']['name'];


        	
            $error_msg = array();
            $i = 0;


			if( !$user_name ){
				$error_msg[$i] = "Please enter user name";
                $i++;
			}

			if( !$dob ){
				$error_msg[$i] = "Please enter user date of birth";
                $i++;
			}

			
			if( $user_visibility == "" ){
				$error_msg[$i] = "Please select user visibility";
                $i++;
			}

			$filepath = "";
			$filepath = "";
        	if( count($error_msg) == 0 ){

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
			            $this->upload->initialize($config);

			            if ( ! $this->upload->do_upload('user_path'))
			            {
			                $error = array('error' => $this->upload->display_errors());
			                $error_msg[9]  = $this->upload->display_errors(); 
			               
			            }else{
			          		
			          			$datasuccess = array('upload_data' => $this->upload->data());
					          	$user_path = $filepath;

					          	//code to resize image

					          	$imgconfig['image_library'] 	= 'gd2';
					          	$imgconfig['new_image'] 		= 'assets/userLibrary/';
								$imgconfig['source_image'] 		= 'assets/userLibrary/'.$filepath;
								$imgconfig['create_thumb'] 		= TRUE;
								$imgconfig['maintain_ratio'] 	= TRUE;
								$imgconfig['width']         	= 180;
								$imgconfig['height']       		= 60;
								

								$this->load->library('image_lib', $imgconfig);

								// resize image
								 $this->image_lib->resize();
								 // handle if there is any problem
								 if ( ! $this->image_lib->resize()){
								  echo $this->image_lib->display_errors();
								  exit;
								 }
								 $this->image_lib->clear();
								

								$ext = explode(".",$filepath);

								$user_path = $ext[0]."_thumb.".$ext[1];

								
					          	//code to resize image end

				          		
		        		}
        		}

        		

        		if( $path1){

	        		//image upload code
	        		$new_name = time().uniqid().'.'.pathinfo($path1, PATHINFO_EXTENSION); 
	          		$filepath1 = $new_name;

	      		    $config1['file_name']            = $new_name;
		            $config1['upload_path']          = 'assets/userLibrary/';
		            $config1['allowed_types']        = '*';
		            $config1['max_size']             = 200000;
		           // $config['max_width']            = 10000;
		           // $config['max_height']           = 10000;

		            

		            $this->load->library('upload', $config1);
		            $this->upload->initialize($config1);

		            if ( ! $this->upload->do_upload('user_path1'))
		            {
		                $error = array('error' => $this->upload->display_errors());
		                $error_msg[9]  = $this->upload->display_errors(); 
		                print_r($error);
		                  
		            }else{
		          		
		          			$datasuccess = array('upload_data' => $this->upload->data());
				          	$user_path1 = $filepath;

				          	//code to resize image

				          	$imgconfig1['image_library'] = 'gd2';
							$imgconfig1['source_image'] = 'assets/userLibrary/'.$filepath1;
							$imgconfig1['create_thumb'] = TRUE;
							$imgconfig1['maintain_ratio'] = TRUE;
							$imgconfig1['width']         = 180;
							$imgconfig1['height']       = 60;


							$this->load->library('image_lib', $imgconfig1);
							$this->image_lib->initialize($imgconfig1);

							// resize image
							 $this->image_lib->resize();
							 // handle if there is any problem
							 if ( ! $this->image_lib->resize()){
							  echo $this->image_lib->display_errors();
							  exit;
							 }
							

							$ext = explode(".",$filepath1);

							$user_path1 = $ext[0]."_thumb.".$ext[1];

							
				          	//code to resize image end

				          	

		            }


	       		}
		          //code end
	       		//echo $user_path;
	       		//echo '<br>'.$user_path1;exit;

	            $adddata = array(
	        		"name"                 	=> $user_name,
	        		"email"					=> $user_email,
	        		"password"				=> md5($user_pin),
	        		"profile_image"         => $user_path,
	        		"profile_image1"        => $user_path1,
	        		"dob"					=> time($dob),
	        		"status"            	=> $user_visibility,
	        		"created_on"            => time()
	        	);

	        	$condition = array( 
	        		"user_id"				=> $user_id
	        	);


	        	$id = $this->basic_model->update("sh_user", $adddata, $condition);

	        	if( @file_exists("assets/userLibrary/".$filepath) ){
	          		@unlink( "assets/userLibrary/".$filepath );
	        	}

                if( @file_exists("assets/userLibrary/".$filepath1) ){
				          		@unlink( "assets/userLibrary/".$filepath1 );
				        	}
	        	if( $id > 0 ){
	        		$success =  "User Edit Successfully";
					$this->session->set_flashdata('success', $success );

                    header("Location:".base_url()."admin-users");
					exit;
	        	}else{
	        		$error_msg[0] = "Something went wrong. Please try again";
					$this->session->set_flashdata('error', $error_msg );

                    header("Location:".base_url()."admin-edit-users/".$user_id);
					exit;

	        	}



        	}else{ 
        		$error = $error_msg;
				$this->session->set_flashdata('error', $error );

                 header("Location:".base_url()."admin-edit-users/".$user_id);
				
				exit;

        	}	



        }
		
		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('user/edit_user', $data );
		$this->load->view('superadmin/footer');
	}


	public function viewuser( $user_id){

		
		if( $user_id ){

			//die("gdfgdfgdf");
    		$user = $this->user_model->getuserById($user_id);

    	
    		if( is_array($user) && count($user) > 0 ){

    			
    			 echo '<div class="modal-header">
    			 			<img src='.base_url().'assets/userLibrary/'.stripslashes($user['profile_image']).'>
                            <h4 class="modal-title" id="largeModalLabel">'.stripslashes($user['name']).'</h4>
                        </div>

                        <div class="modal-body">
                           <table style="border:1px solid black;width:100%;">

                           		 <tr>  
                           		 	<th style="padding: 5px;">Name</th> 
                                    <td>:  '.stripslashes($user['name']).'</td>
                                </tr>
                               
                                <tr> 
                                	<th style=" padding: 5px;">Entered Date</th>   
                                    <td>: '.date(' F d Y', $user['created_on']).'</td>
                                </tr>


                           </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>';

             exit;


    			
    		}
    	}

		
	}


	public function deleteuser( $user_id ){

		if( $user_id ){
    		$user = $this->user_model->getUser($user_id);

    		if( is_array($user) &&  count($user) > 0 ){

    			/*$pantry_path = stripslashes( $document['pantry_image'] );
               
    			if( @file_exists("assets/pantryLibrary/".$pantry_path)){
    				@unlink("assets/pantryLibrary/".$pantry_path);
    			}*/	

    			$this->basic_model->update("sh_user",array( "status" => '1' ) ,array("user_id" => $user_id));

    			$success =  "User Deleted Successfully";
				$this->session->set_flashdata('success', $success );


                header("Location:".base_url()."admin-users");
				exit;
    		}
    	}
	}

	public function uniqueUser(){

		$email = $this->input->post('email');

		$isunique   = $this->user_model->checkUniqueUser( $email );

		echo $isunique;
	}
}
