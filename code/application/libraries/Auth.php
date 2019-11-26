<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {
    private $CI;

    /*object handler for session */
	private $_current_session;
    /*session name for authentication*/
    private $__session_name;
    /*model fordosen*/
    private $__user_model_dosen;
    /*model for mahasiswa*/
    private $_user_model_mahasiswa;

    /*prefix */
    private $_pk_column = '';
    private $_user_prefix_dosen;
    private $_user_prefix_mahasiswa;

    /*name of dashboard page, after login*/
    private $_dashboard_page;

    /* access for all */
    private $_access;
    /* access limitation - default module */
    private $_default_module = '';


    /* constant untuk definisi akses */
	const ACCESS_VIEW = 'view';
	const ACCESS_ADD = 'add';
	const ACCESS_EDIT = 'edit';
	const ACCESS_DELETE = 'delete';


    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        
        //load from __framework config
        $this->_pk_column               = $this->CI->config->item('auth_pk_column');
        $this->_session_name            = $this->CI->config->item('auth_session_name');
        $this->_user_model_dosen        = $this->CI->config->item('auth_user_model_dosen');
        $this->_user_model_mahasiswa    = $this->CI->config->item('auth_user_model_mahasiswa');
        $this->_dashboard_page          = $this->CI->config->item('auth_dashboard_page');

        $this->_user_prefix_dosen       = $this->CI->config->item('auth_user_prefix_dosen');
        $this->_user_prefix_mahasiswa   = $this->CI->config->item('auth_user_prefix_mahasiswa');

        //load model for DOSEN and MAHASISWA
        $this->CI->load->model('m_dosen');
        $this->CI->load->model('m_mahasiswa');

        $this->__create_new_session_data();

    }

    /*create function for create new session data */
    private function __create_new_session_data(){
        $session_data = new stdClass();
        $session_data->profile = NULL;
        $session_data->group = FALSE;

        $this->_current_session = $session_data;
    }

    /*function for login without credentials  password*/
    public function login_less($group,$user=NULL){ 
            
        $user_login = NULL;
        if($group == ADMIN){
            $user_login = ADMIN;
        }
        else if($user !== NULL || $user !== ''){
            if($group == DOSEN){
                $dosen_res = $this->CI->{$this->_user_model_dosen}->get("dos_id = ".$user);
                $user_login = $dosen_res;
            }
            else if($group == MAHASISWA){
                $mahasiswa_res = $this->CI->{$this->_user_model_mahasiswa}->get("mhs_id = ".$user);
                $user_login = $mahasiswa_res;
            }
            else{
                echo json_encode(array("status" => "error","message" => "Group cannot found"));exit;
            }
        }
        else {
            echo json_encode(array("status" => "error","message" => "User cannot be null"));exit;
        }
        
        if($user_login !== NULL){
            $this->__set_login($user_login,$group);
            return array("success" => true,"message" => "Success Login" ,"redir_page" => $this->_dashboard_page);
        }
        else {
            return array("success" => false,"messsage" => "User not found");
        }
    }

    /*function for logout */
    public function logout(){
        if(!$this->is_login())
            return;
        
        //delete session data
        $session_data = $this->__get_session();
        if($session_data !== NULL){
            $this->_current_session->profile = NULL;
            $this->_current_session->group = FALSE;

            $this->CI->session->unset_userdata($this->_session_name);
            return TRUE;
        }

        return FALSE;
    }
    

    /*function to get session data */
    private function __get_session(){
        $session_data = $this->CI->session->userdata($this->_session_name);
        return $session_data;
    }

    /*function setter for set login */
    private function __set_login($user_login,$group=NULL){
        //$session_data = $this->__get_session();

        //create new data from login
        $this->_current_session->profile = $user_login;
        $this->_current_session->group = $group;
        $this->CI->session->set_userdata($this->_session_name, $this->_current_session);

        return TRUE;
    }

    /*function for get login  */
    public function is_login(){
        $session_data = $this->__get_session();
        return $session_data !== NULL;
    }

    /*get user group */
    public function get_usergroup(){
        if($this->is_login()){
            $group = $this->__get_session()->group;
            return $group;
        }
        return FALSE;
    }

    /*get user info*/
    public function get_user(){
		return $this->__get_session()->profile;
    }

    /*get user id*/
    public function get_user_id(){
		if(!$this->is_login()) return FALSE;
		if($this->is_admin()) return ADMIN;
		$pk_col = ($this->get_usergroup() == DOSEN ? $this->_user_prefix_dosen : $this->_user_prefix_mahasiswa) . $this->_pk_column;
		return $this->__get_session()->profile->{$pk_col};
    }
    

    /*functino to check is admin */
    public function is_admin(){
        return $this->__get_session()->group == ADMIN;
    }

    /*function to set sidebar*/
    public function create_template_sidebar($file_name=NULL,$data=NULL){
        if($file_name == NULL){
            if($this->get_usergroup() == ADMIN) {
                $sidebar = '_admin';
            } else if($this->get_usergroup() == DOSEN){
                $sidebar = '_dosen';
            }
            else{
                $sidebar = '_mahasiswa';
            }

            $this->CI->load->view('__base/sidebar'.$sidebar,$data);
        }
        else{
            $this->CI->load->view($file_name,$data);
        }
    }

    /*to set default module */
    public function set_default_module($module_name){
		$this->_default_module = $module_name;
	}

    /*set access for view */
    public function set_access_view($module=''){
        $this->set_access(self::ACCESS_VIEW, $module);
    }

    /*set access for add */
    public function set_access_add($module=''){
        $this->set_access(self::ACCESS_ADD, $module);
    }

    /*set access for edit */
    public function set_access_edit($module=''){
        $this->set_access(self::ACCESS_EDIT, $module);
    }

    /*set access for delete */
    public function set_access_delete($module=''){
        $this->set_access(self::ACCESS_DELETE, $module);
    }

    /*set access for module*/
    public function set_access($action,$module=''){
        if($module == '') $module = $this->_default_module;
		if(!isset($this->_access[$module]))
			$this->_access[$module] = array();
		$this->_access[$module][] = $action;
    }



}