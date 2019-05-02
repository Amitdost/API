<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {


	public function __construct()
    {
    	
        parent::__construct();
                
        if( !$this->session->userdata('admin') ){

			header("Location:".base_url()."admin");
			exit;
        }

    }
	
	public function index()
	{

		$data['title'] = "Dashboard";

		$data['admin']  = $this->basic_model->getAdmin();

		

		$this->load->view('superadmin/header', $data);
		$this->load->view('superadmin/left', $data);
		$this->load->view('superadmin/dashboard' );
		$this->load->view('superadmin/footer');
	}
}
