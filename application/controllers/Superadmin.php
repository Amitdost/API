<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Superadmin extends CI_Controller {

	public function __construct()
    {


        parent::__construct();

        
		$this->admin = $this->basic_model->getAdmin();
		$this->load->model('pantry_model');
           
    }


	public function index()
	{

		if( !empty($this->session->userdata('admin')) && $this->session->userdata('admin') ){

			header("Location:".base_url()."admin-dashboard");
			exit;
        }
        
		$data['admin'] = $this->admin;

		if( $this->input->post('login') == "dologin" ){

			$username = trim(addslashes(strtolower( $this->input->post('username') )));
			$password = md5( $this->input->post('password') );

			$validateadmin = $this->basic_model->validateAdmin( $username, $password );


			if( $validateadmin ){

				$this->session->set_userdata( 'admin', $data['admin'] );
				header("Location:".base_url()."admin-dashboard");
				exit;
				
			}else{

				$error = "Wrong email address or password";
				$this->session->set_flashdata('error', $error );

				header("Location:".base_url()."admin");
				exit;
			}

		}

		$this->load->view('superadmin/login', $data);
		
	}


	public function forgot_password(){


		if( $this->input->post('forgot') == "send" ){


			$email = trim(addslashes( strtolower($this->input->post('email'))));
			$admin    = $this->basic_model->getAdmin();

			
			if( $email && $email == $admin['admin_email'] ){

				$password = rand(00000000,99999999);

				$message = file_get_contents(base_url()."email_templates/forgetpassword.html");

				$message = str_replace("{NAME}", $admin['admin_name'] , $message);
				$message = str_replace("{EMAIL}", $admin['admin_email'] , $message);
				$message = str_replace("{PASSWORD}", $password , $message);
				$message = str_replace("{COMPANY_NAME}", "SHOPPING HELPER" , $message);
				
				
				$data = array(
					"name" => 'Amit Kumar',
					"from" => 'amit.kumar@appideasinc.com',
					"to"   => 'amit.kumar@appideasinc.com',
					"cc"   => stripslashes($admin['admin_email']),
					"subject" => "Forget Password",
					"message" => $message
				);

				$this->db->where('admin_email', $email);
	        	$update_data = array(
					"admin_password" => md5($password)
				);

        		$this->db->update('sh_administrator', $update_data); 

        		if( $this->db->affected_rows() > 0 ){

        			$res = $this->email_model->send_mail($data);

        			if( $res == "1"){
        				$success = "A password has been sent to your email address.";
						$this->session->set_flashdata('success', $success );
						header("Location:".base_url()."admin-forgot-password");
						exit;
        			}else{
        				$error = "Something went wrong. Please try again";
						$this->session->set_flashdata('error', $error );
						header("Location:".base_url()."admin-forgot-password");
						exit;
        			}	

        			
        		}

				

			}else{
				
				$error = "Email address does not exist.";
				$this->session->set_flashdata('error', $error );
				header("Location:".base_url()."admin-forgot-password");
				exit;
			}


		}

		$data['admin'] = $this->basic_model->getAdmin();
		$this->load->view('superadmin/forgot_password', $data);

	}

	
	public function logout(){

		$this->session->unset_userdata('admin');
		header("Location:".base_url()."admin");
		exit;
	}

	
	public function profile(){


        $data['title'] = "Edit Profile";

        if( $this->input->post('profile') == "update" ){
 
        	$site_name     = trim(addslashes($this->input->post('site_name')));
        	$from_email    = trim(addslashes($this->input->post('from_email')));
        	$admin_email   = trim(addslashes($this->input->post('admin_email'))); 
        	$old_site_logo = trim(addslashes($this->input->post('old_site_logo')));
        	$admin_address = trim(addslashes($this->input->post('admin_address')));
        	$admin_phone   = trim(addslashes($this->input->post('admin_phone')));
        	$admin_name    = trim(addslashes($this->input->post('admin_name')));
        	$admin_state   = trim(addslashes($this->input->post('admin_state')));
        	$admin_city    = trim(addslashes($this->input->post('admin_city')));
        	$admin_zip     = trim(addslashes($this->input->post('admin_zip')));

        	//upload image

			    $path      = $_FILES['sitelogo']['name'];
			    $site_logo = $old_site_logo;

			    if($path !='')
	        	{
	        		$new_name = time().uniqid().'.'.pathinfo($path, PATHINFO_EXTENSION); 
	          		$filepath = $new_name;

		      		  $config['file_name']            = $new_name;
			          $config['upload_path']          = 'adminassets/images/';
			          $config['allowed_types']        = 'gif|jpg|jpeg|png|JPEG|JPG|PNG';
			          $config['max_size']             = 200000;
			          $config['max_width']            = 10000;
			          $config['max_height']           = 10000;

			          $this->load->library('upload', $config);

			          if ( ! $this->upload->do_upload('sitelogo'))
			          {
			                  $error = array('error' => $this->upload->display_errors());
			                 
			                  
			          }else{
			          	$datasuccess = array('upload_data' => $this->upload->data());
			          	$site_logo = $filepath;

			          	//code to resize image

			          	$imgconfig['image_library'] = 'gd2';
						$imgconfig['source_image'] = 'adminassets/images/'.$filepath;
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

						$site_logo = $ext[0]."_thumb.".$ext[1];
						
			          	//code to resize image end

			          	if( file_exists( "adminassets/images/".$old_site_logo  ) ){
			          		@unlink( "adminassets/images/".$old_site_logo );
			          	}

			          	if( @file_exists("adminassets/images/".$filepath) ){
			          		@unlink( "adminassets/images/".$filepath );
			        	}
			          }
	        	}

	        	//upload image end

	        	$condition  = array("admin_id", "1");

	        	$update_data = array(
	        		"site_name"     => $site_name,
	        		"from_email"    => $from_email,
	        		"admin_email"   => $admin_email, 
	        		"site_logo"     => $site_logo,
	        		"admin_address" => $admin_address,
	        		"admin_phone" 	=> $admin_phone,
	        		"admin_name" 	=> $admin_name,
	        		"admin_state" 	=> $admin_state,
	        		"admin_city" 	=> $admin_city,
	        		"admin_zip" 	=> $admin_zip,
	        	);

	        	$this->basic_model->update("sh_administrator", $update_data, $condition);



	        	
	        	$success = "Profile Updated Successfully";
				$this->session->set_flashdata('success', $success );
				header("Location:".base_url()."admin-dashboard");
				exit;

        }

		
		$data['admin']  = $this->basic_model->getAdmin();

		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left');
		$this->load->view('superadmin/profile', $data);
		$this->load->view('superadmin/footer');

	}

	public function change_password(){

        if( $this->input->post('changepassword') == "changepassword" ){

        	if( !empty($this->input->post('password')) ){

        		$password    = $this->input->post('password');

        		$condition   = array("admin_id", "1");

        		$update_data = array("admin_password" => md5($password));
        		$this->basic_model->update("sh_administrator", $update_data, $condition);

	        	$success = "Password changed successfully";
				$this->session->set_flashdata('success', $success );

				header("Location:".base_url()."admin-dashboard");
				exit;
        	}else{

        		$error = "Password enter password";
				$this->session->set_flashdata('error', $error );

				header("Location:".base_url()."admin-change-password");
				exit;
        	}

        	

        }


		$title['title'] = "Change Password";
		$title['admin']  = $this->basic_model->getAdmin();

		$this->load->view('superadmin/header', $title);
		$this->load->view('superadmin/left', $title);
		$this->load->view('superadmin/change_password');
		$this->load->view('superadmin/footer');		

	}

	public function getdetail( $company_id ){

		if( !$this->session->userdata('superadmin') ){
            //redirect(base_url());
            header("Location:http://".$_SERVER['HTTP_HOST']);
            exit;
        }

		if( $company_id ){

			

			$sql 	  = "SELECT * FROM company WHERE  company_id = '".$company_id."'  ";
			$company = $this->db->query($sql) -> row_array();


            if(stripslashes($company['status']) == 1){
                $status  =  '<span style="color:green">Active</span>';
            }else{
                $status =  '<span style="color:red">Inactive</span>';
            }

            $state_name = $this->basic_model->getStateName(stripslashes($company['state']));

            if(strpos(stripslashes($company['url']), "http://") !== false){

              $url = stripslashes($company['url']);
            }else{
              $url = "http://".stripslashes($company['url']);
            }
                                 
            $subdomain_url = stripslashes($company['subdomain_url']);


			echo '<div class="modal-header">
                            <h4 class="modal-title" id="largeModalLabel">'.stripslashes($company['company_name']).'</h4>
                        </div>
                        <div class="modal-body">
                           <table style="border:1px solid black;width:100%;">
                           		<tr>
	                           		<td><b>Contact Name : </b>'.stripslashes($company['contact']).'</td>
	                           		<td><b>Email : </b>'.stripslashes($company['email']).'</td>
                           		</tr>

                           		<tr>
	                           		<td><b>Company Phone : </b>'.stripslashes($company['company_phone']).'</td>
	                           		<td><b>Cell Phone : </b>'.stripslashes($company['cell_phone']).'</td>
                           		</tr>

                           		<tr>
	                           		<td><b>Street Address : </b>'.stripslashes($company['address']).'</td>
	                           		<td><b>Zip : </b>'.stripslashes($company['zip']).'</td>
                           		</tr>

                           		<tr>
	                           		<td><b>State : </b>'.stripslashes( $state_name).'</td>
	                           		<td><b>City : </b>'.stripslashes($company['city']).'</td>
                           		</tr>

                           		<tr>
	                           		<td><b>Added On(M-D-Y) : </b>'.date("F d Y", stripslashes($company['created_on'])).'</td>
	                           		<td><b>Status : </b>'.$status.'</td>
                           		</tr>

                           		<tr>
	                           		<td colspan="2"><b>Company Url : </b>'.$url.'</td>
	                           		
                           		</tr>

                           		<tr>
	                           		<td colspan="2" ><b>Company Admin Url : </b>'.$subdomain_url.'.twp1touch.com</td>
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
