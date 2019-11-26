<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
        parent::__construct();
        //load model
        $this->load->model(array('m_mahasiswa','m_dosen'));
        //set default module
        $this->auth->set_default_module('login');
        
    }

    public function index(){
        if($this->auth->is_login()){
			redirect($this->config->item('auth_dashboard_page'));
        }        
        $data = array();

        $data['page_title'] = 'Login';
        $data['plugin'] = array();
        $data['custom_js'] = array(
            'data' => $data,
            'src'  => '__scripts/login'
        );
        $data['assets_js'] = array();


        $this->load->view('__base/header_login',$data);
        $this->load->view('login/index');
        $this->load->view('__base/footer_login',$data);
    }

    public function authenticate(){
        if(!$this->input->is_ajax_request()) show_404();
        //validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('group_id'         , 'Group id'      , 'required|integer');
		$this->form_validation->set_rules('user_id'          , 'User id'       , 'integer');

        if ($this->form_validation->run()){
            $group_id = $this->input->post('group_id');
            $user_id = $this->input->post('user_id');
            
            $login_res = $this->auth->login_less(intval($group_id),intval($user_id));  //login information

            if($login_res['success'] !== FALSE){
                echo json_encode(array("status" => "success",
                                        "message" => $login_res['message'],
                                        "redir_page" => $login_res['redir_page'],
                                        "session_info" => $this->auth->get_user(),
                                        "group_info" => $this->auth->get_usergroup())); 
                exit;
            }
            else{
                //failed to login
                echo json_encode(array("status" => "error","message" => $login_res['message']));
                exit;
            }
        } 
        else { 
            echo json_encode(array("status" => "error","message" => $this->form_validation->error_array())); exit;
        }
    }

    public function fetch_user(){
        if(!$this->input->is_ajax_request()) show_404();
        //validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('group_id'         , 'Group id'      , 'required|integer');
		$this->form_validation->set_rules('user_id'          , 'User id'       , 'integer');

        if ($this->form_validation->run()){
            $group_id = $this->input->post('group_id');
            $user_id = intval($this->input->post('user_id'));

            if($group_id == DOSEN){
                $dos_result = $this->m_dosen->get(NULL,"dos_departemen");
                if($dos_result !== NULL) { 
                    echo json_encode(array("status" => "success","data"=>$dos_result)); exit;
                }
            } else if($group_id == MAHASISWA){
                $mhs_result = $this->m_mahasiswa->get(NULL,"mhs_departemen");
                if($mhs_result !== NULL) { 
                    echo json_encode(array("status" => "success","data"=>$mhs_result)); exit;
                }
            } else{
                echo json_encode(array("status" => "error","message" => "Invalid Group ID")); exit;
            }
        } 
        else { echo json_encode(array("status" => "error","message" => $this->form_validation->error_array())); exit;}
    }

    public function logout(){
        $this->auth->logout();
        redirect(base_url());
    }
}