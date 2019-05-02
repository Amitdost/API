<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'gtranslate.php';



class Pantry extends CI_Controller {


	public function __construct()
    {
    	
        parent::__construct();
                
        if( !$this->session->userdata('admin') ){

			header("Location:".base_url()."admin");
			exit;
        }

        $this->perPage = 20;
       
		$this->load->model('pantry_model');
		$this->load->library('pagination');

    }
	
	public function index(){

		$data['title'] = "Pantry";

		$data['admin']  = $this->basic_model->getAdmin();
		
		$conditions['returnType'] = 'count';
        $totalRec = $this->pantry_model->getPentry($conditions);

        if( $this->input->post('search_pantry')){

        	$conditions['en_pantry'] = $this->input->post('search_pantry');
        }


		//pagination config
        $config['base_url']    = '../admin-pantry';
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
        
        $data['pantry']  = $this->pantry_model->getPentry( $conditions );
        $data['pages']     = $this->pagination->create_links();


		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('pantry/pantry_list', $data );
		$this->load->view('superadmin/footer');
	}


	public function addPantry(){

		if( $this->input->post("addpantry") == "addpantry"){

        	
        	$pantry_name       	= trim(addslashes($this->input->post('pantry_name')));
        	//$pantry_cost 		= trim(addslashes($this->input->post('pantry_cost')));
        	//$pantry_weight  	= trim(addslashes($this->input->post('pantry_weight')));
        	//$pantry_description = trim(addslashes($this->input->post('pantry_description')));
        	$pantry_visibility  = trim(addslashes($this->input->post('pantry_visibility')));

        
        	$path = $_FILES['pantry_path']['name'];

        	
            $error_msg = array();
            $i = 0;


			if( !$pantry_name ){
				$error_msg[$i] = "Please enter pantry title";
                $i++;
			}

			/*if( !$pantry_cost ){
				$error_msg[$i] = "Please enter pantry cost";
                $i++;
			}
*/
			/*if( !$pantry_description ){
				$error_msg[$i] = "Please enter pantry description";
                $i++;
			}*/

			if( $pantry_visibility == "" ){
				$error_msg[$i] = "Please select pantry visibility";
                $i++;
			}

			if( !$path ){
				$error_msg[$i] = "Please select pantry to upload";
                $i;
			}

        	if( count($error_msg) == 0 ){

        		$pantry_path = "";
        		//image upload code
        		$new_name = time().uniqid().'.'.pathinfo($path, PATHINFO_EXTENSION); 
          		$filepath = $new_name;


      		    $config['file_name']            = $new_name;
	            $config['upload_path']          = 'assets/pantryLibrary/';
	            $config['allowed_types']        = '*';
	            $config['max_size']             = 200000;
	           // $config['max_width']            = 10000;
	           // $config['max_height']           = 10000;

	            $this->load->library('upload', $config);

	            if ( ! $this->upload->do_upload('pantry_path'))
	            {
	                $error = array('error' => $this->upload->display_errors());
	                $error_msg[9]  = $this->upload->display_errors(); 
	                print_r($error);
	                  
	            }else{
	          		
	          			$datasuccess = array('upload_data' => $this->upload->data());
			          	$pantry_path = $filepath;

			          	//code to resize image

			          	$imgconfig['image_library'] = 'gd2';
						$imgconfig['source_image'] = 'assets/pantryLibrary/'.$filepath;
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

						$pantry_path = $ext[0]."_thumb.".$ext[1];

						
			          	//code to resize image end

			          	
			          	if( @file_exists("assets/pantryLibrary/".$filepath) ){
			          		@unlink( "assets/pantryLibrary/".$filepath );
			        	}

	            }
		          //code end

	            $gt=new gtranslate();

	            

	            $adddata = array(
	        		"en_pantry"                 	=> $pantry_name,
	        		"el_pantry"                 	=> $gt->translate( $pantry_name , 'el','en',true),
	        		"cmn_pantry"                 	=> $gt->translate( $pantry_name , 'cmn','en',true),
	        		"zh_pantry"                 	=> $gt->translate( $pantry_name , 'zh','en',true),
	        		"ar_pantry"                 	=> $gt->translate( $pantry_name , 'ar','en',true),
	        		"vi_pantry"                 	=> $gt->translate( $pantry_name , 'vi','en',true),
	        		"it_pantry"                 	=> $gt->translate( $pantry_name , 'it','en',true),
	        		"es_pantry"                 	=> $gt->translate( $pantry_name , 'es','en',true),
	        		"hi_pantry"                 	=> $gt->translate( $pantry_name , 'hi','en',true),
	        		"pa_pantry"                 	=> $gt->translate( $pantry_name , 'pa','en',true),
	        		"fil_pantry"                 	=> $gt->translate( $pantry_name , 'fil','en',true),
	        		//"pantry_description"           	=> $pantry_description,
	        		"pantry_image"                  => $pantry_path,
	        		//"pantry_cost"                  	=> $pantry_cost,
	        		//"pantry_weight"                 => $pantry_weight,
	        		"status"            			=> $pantry_visibility,
	        		"created_on"                    => time()
	        	);


	        	$id = $this->basic_model->insert("sh_pantry", $adddata);

                
	        	if( $id > 0 ){
	        		$success =  "Pantry Added Successfully";
					$this->session->set_flashdata('success', $success );

                    header("Location:".base_url()."admin-pantry");
					exit;
	        	}else{
	        		$error_msg[0] = "Something went wrong. Please try again";
					$this->session->set_flashdata('error', $error_msg );

                    header("Location:".base_url()."admin-add-pantry");
					exit;

	        	}



        	}else{ 
        		$error = $error_msg;
				$this->session->set_flashdata('error', $error );

                 header("Location:".base_url()."admin-add-pantry");
				
				exit;

        	}	

        }

		$data['title'] = "Pantry";

		$data['admin']  = $this->basic_model->getAdmin();
	
		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('pantry/add_pantry', $data );
		$this->load->view('superadmin/footer');
	}


	public function editPantry($pantry_id){

		$data['title'] = "Pantry";
		$data['admin']  	= $this->basic_model->getAdmin();
		$data['pantry']  	= $this->pantry_model->getpentryById($pantry_id);

		if( $this->input->post("editpantry") == "editpantry"){

        	
        	$pantry_name       	= trim(addslashes($this->input->post('pantry_name')));
        	//$pantry_cost 		= trim(addslashes($this->input->post('pantry_cost')));
        	//$pantry_weight  	= trim(addslashes($this->input->post('pantry_weight')));
        	//$pantry_description = trim(addslashes($this->input->post('pantry_description')));
        	$pantry_visibility  = trim(addslashes($this->input->post('pantry_visibility')));

        
        	$path = $_FILES['pantry_path']['name'];

        	
            $error_msg = array();
            $i = 0;


			if( !$pantry_name ){
				$error_msg[$i] = "Please enter pantry title";
                $i++;
			}

			/*if( !$pantry_cost ){
				$error_msg[$i] = "Please enter pantry cost";
                $i++;
			}*/

			/*if( !$pantry_description ){
				$error_msg[$i] = "Please enter pantry description";
                $i++;
			}*/

			if( $pantry_visibility == "" ){
				$error_msg[$i] = "Please select pantry visibility";
                $i++;
			}


			if( count($error_msg) == 0 ){

				$pantry_path = "";

				if( $path ){

	        		//image upload code
	        		$new_name = time().uniqid().'.'.pathinfo($path, PATHINFO_EXTENSION); 
	          		$filepath = $new_name;


	      		    $config['file_name']            = $new_name;
		            $config['upload_path']          = 'assets/pantryLibrary/';
		            $config['allowed_types']        = '*';
		            $config['max_size']             = 200000;
		           // $config['max_width']            = 10000;
		           // $config['max_height']           = 10000;

		            $this->load->library('upload', $config);

		            if ( ! $this->upload->do_upload('pantry_path'))
		            {
		                $error = array('error' => $this->upload->display_errors());
		                $error_msg[9]  = $this->upload->display_errors(); 
		                print_r($error);
		                  
		            }else{
		          		
		          			$datasuccess = array('upload_data' => $this->upload->data());
				          	$pantry_path = $filepath;

				          	//code to resize image

				          	$imgconfig['image_library'] = 'gd2';
							$imgconfig['source_image'] = 'assets/pantryLibrary/'.$filepath;
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

							$pantry_path = $ext[0]."_thumb.".$ext[1];

							
				          	//code to resize image end

				          	if( @file_exists("assets/pantryLibrary/".$filepath) ){
				          		@unlink( "assets/pantryLibrary/".$filepath );
				        	}

	            	}

				}else{

					$pantry_path = $data['pantry']['pantry_image'];
				}

        		
		          //code end

	            $gt=new gtranslate();

	            

	            $adddata = array(
	        		"en_pantry"                 	=> $pantry_name,
	        		"el_pantry"                 	=> $gt->translate( $pantry_name , 'el','en',true),
	        		"cmn_pantry"                 	=> $gt->translate( $pantry_name , 'cmn','en',true),
	        		"zh_pantry"                 	=> $gt->translate( $pantry_name , 'zh','en',true),
	        		"ar_pantry"                 	=> $gt->translate( $pantry_name , 'ar','en',true),
	        		"vi_pantry"                 	=> $gt->translate( $pantry_name , 'vi','en',true),
	        		"it_pantry"                 	=> $gt->translate( $pantry_name , 'it','en',true),
	        		"es_pantry"                 	=> $gt->translate( $pantry_name , 'es','en',true),
	        		"hi_pantry"                 	=> $gt->translate( $pantry_name , 'hi','en',true),
	        		"pa_pantry"                 	=> $gt->translate( $pantry_name , 'pa','en',true),
	        		"fil_pantry"                 	=> $gt->translate( $pantry_name , 'fil','en',true),
	        		//"pantry_description"           	=> $pantry_description,
	        		"pantry_image"                  => $pantry_path,
	        		//"pantry_cost"                  	=> $pantry_cost,
	        		//"pantry_weight"                 => $pantry_weight,
	        		"status"            			=> $pantry_visibility,
	        		"created_on"                    => time()
	        	);

	            $condition 	= array(
	            	"id"	=> $pantry_id,
	            );

	        	$upload = $this->basic_model->update("sh_pantry", $adddata, $condition);

                
	        	if( $upload > 0 ){
	        		$success =  "Pantry Updated Successfully";
					$this->session->set_flashdata('success', $success );

                    header("Location:".base_url()."admin-pantry");
					exit;
	        	}else{
	        		$error_msg[0] = "Something went wrong. Please try again";
					$this->session->set_flashdata('error', $error_msg );

                    header("Location:".base_url()."admin-edit-pantry/".$pantry_id);
					exit;

	        	}



        	}else{ 
        		$error = $error_msg;
				$this->session->set_flashdata('error', $error );

                 header("Location:".base_url()."admin-edit-pantry/".$pantry_id);
				
				exit;

        	}	

        }
		
		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('pantry/edit_pantry', $data );
		$this->load->view('superadmin/footer');
	}


	public function view_pantry( $pantry_id){

		$languages = $this->basic_model->getlanguages();


		if( $pantry_id ){
    		$pantry = $this->pantry_model->getpentryById($pantry_id);

    		$image = '<img src='.base_url().'assets/pantryLibrary/'.stripslashes($pantry['pantry_image']).'>';

    		$pantry_weight = '';

    		if( $pantry['pantry_weight'] ){

    			$pantry_weight = '<tr> 
    								<th style=" padding: 5px;">Pantry Weight</th>    
                                    <td>: '.number_format( $pantry['pantry_weight'], 2 ).' Kg</td>
                                 </tr>';

    		}


    		
			$option = '';					

			foreach ($languages as $key => $value) {
										
				$option .= '<option value="'.$value['language_code'].'">'.$value['language'].'</option>';						
			}	

			$select_languages = '<select name = "languages" id="languages" class="languages">
								 '.$option.'
								</select>';

			$cost = '<tr>  
                    	<th style=" padding: 5px;">Pantry Cost</th>   
                        <td>: '.number_format( $pantry['pantry_cost'], 2 ).'</td>
                    </tr>';					





			
    		if( is_array($pantry) && count($pantry) > 0 ){

    			$description      = '<tr>
                                	<th style=" padding: 5px;">Pantry Description</th> 
                                    <td>: '.stripslashes(nl2br( $pantry['pantry_description'] )).'</td>
                                </tr>;'	;

    			 echo '<div class="modal-header">
    			 			'.$image.'
    			 			<input type="hidden" name ="pantry_id" id="pantry_id" value ="'.$pantry_id.'">
                            <h4 class="modal-title" id="largeModalLabel">'.stripslashes($pantry['en_pantry']).'</h4>
                        </div>

                        <div class="modal-body">
                           <table style="border:1px solid black;width:100%;">

                           		 <tr>  
                           		 	<th style="padding: 5px;">Select Language</th> 
                                    <td>:  '.$select_languages.'</td>
                                </tr>
                                	
                                <tr> 
                                	<th style=" padding: 5px;">Entered Date</th>   
                                    <td>: '.date(' F d Y', $pantry['created_on']).'</td>
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


	public function deletepantry( $pantry_id ){

		if( $pantry_id ){
    		$pantry = $this->pantry_model->getpentryById($pantry_id);

    		if( is_array($pantry) &&  count($pantry) > 0 ){

    			$pantry_path = stripslashes( $document['pantry_image'] );
               
    			if( @file_exists("assets/pantryLibrary/".$pantry_path)){
    				@unlink("assets/pantryLibrary/".$pantry_path);
    			}	

    			$this->basic_model->delete("sh_pantry", array("id" => $pantry_id));

    			$success =  "Pantry Deleted Successfully";
				$this->session->set_flashdata('success', $success );


                header("Location:".base_url()."admin-pantry");
				exit;
    		}
    	}
	}


	public function getPantryName( ){

		$pantry_id = $this->input->post('pantry_id');
		$languages = $this->input->post('languages');

		$languages_code = $languages."_pantry";

		$pantry = $this->pantry_model->getpentryById($pantry_id);

		echo $pantry[$languages_code];
	}


	public function uniquename( ){

		$pantry_name 	= $this->input->post('pantry_name');
		$pantry_id 		= $this->input->post('pantry_id');

		$pantry = $this->pantry_model->isuniquename($pantry_name, $pantry_id);

		echo $pantry;
		exit;
	}
}
