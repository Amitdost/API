<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {

	public function __construct()
    {
    	
        parent::__construct();
                
        if( !$this->session->userdata('admin') ){

			header("Location:".base_url()."admin");
			exit;
        }

       

    }


	public function index( $content = " " ){

		$data['page'] = "";
		$data['active_page'] = "";

		$page  					= $this->basic_model->getpagesOnchange( $content );
		$data['page'] 			= $page['description'];
		$data['active_page'] 	= $content;


		if( $this->input->post('pages') == 'add' ){

			$page_content 	= $this->input->post('page_content');
			$page_change 	= $this->input->post('page_change');

			$error_msg = array();

			if( !$page_content ){
				$error_msg[1] = "Please enter content";
			}

			if( !$page_change ){
				$error_msg[1] = "Please select page";
			}


			if( count( $error_msg ) == 0 ){

				$this->db->update('sh_content_management', array( 'description' => $page_content), array( 'page_name' => $content)	);

				$this->session->set_flashdata('success', "Updated Successfully");

				header("Location:".base_url()."content-management/".$content);
		 	}else{

		 		$this->session->set_flashdata('error', $error_msg );
		 		header("Location:".base_url()."content-management");

		 	}

		 	$page  					= $this->basic_model->getpagesOnchange( $content );
			$data['page'] 			= $page['description'];

			
			/*$content 		=  $this->input->post('page_change');
			
			$page_content 	= $this->input->post('page_content');

			$page  			= $this->basic_model->getpagesOnchange( $content );

			$data['page'] 			= $page['description'];
			$data['active_page'] 	= $content;

			$error_msg = array();

			if( !$page_content ){
				$error_msg[1] = "Please enter content";
			}

			
			if( count( $error_msg ) == 0 ){
				
				$this->db->update('sh_content_management', array( 'description' => $page_content), array( 'page_name' => $content));

				$page  			= $this->basic_model->getpagesOnchange( $content );
				$data['page'] 			= $page['description'];
			}*/

			

		}

		$data['title'] 	= "Content Pages";
		$data['admin']  = $this->basic_model->getAdmin();
		
		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('pages/page', $data);
		$this->load->view('superadmin/footer');

	}

	
}
