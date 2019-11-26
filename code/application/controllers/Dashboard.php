<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if(!$this->auth->is_login()){
			redirect(base_url());
        }  
    }


    public function index(){

        $data = array();

        $data['page_title'] = 'Dashboard';
        $data['plugin'] = array();
        $data['custom_js'] = array(
            'data' => $data,
            'src'  => '__scripts/dashboard'
        );
        $data['assets_js'] = array();

        $this->load->view('__base/header_dashboard',$data);
        $this->auth->create_template_sidebar(NULL);
        $this->load->view('dashboard/index');
        $this->load->view('__base/footer_dashboard',$data);
    }

}
