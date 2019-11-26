<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tahunakademik extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if(!$this->auth->is_login()){
			redirect(base_url());
        }  
        $this->load->model(array('m_tahunakademik'));
    }

    public function index(){

        $data = array();

        $data['page_title'] = 'Tahun Akademik';
        $data['plugin'] = array();
        $data['custom_js'] = array(
            'data' => $data,
            'src'  => '__scripts/tahunakademik'
        );
        $data['assets_js'] = array();

        $this->load->view('__base/header_dashboard',$data);
        $this->auth->create_template_sidebar(NULL);
        $this->load->view('tahunakademik/index',$data);
        $this->load->view('__base/footer_dashboard',$data);
    }

    public function get_datatable(){
        //allow ajax only
        if(!$this->input->is_ajax_request()) show_404();

        $filter_cols = array();

        $this->m_tahunakademik->get_datatable(implode(" AND ",$filter_cols),"tak_tahun desc");
    }



}