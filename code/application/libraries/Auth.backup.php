<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {
	private $CI;

	/*
	 * Auth constant, change this depend on your need
	 * Restriction, session table must have :
	 * session id = varchar 40
	 * session ip = varchar 16
	 * session user agent = varchar 120
	 * session lastactivity = integer unsigned
	 * session content = text
	 * session visible data = varchar 50
	 *
	 * model must have name : m_tablename, if not, modify this file
	 */
	/* set session table name */
	private $_session_model_pemilik = '';
	private $_session_model_admin = '';
	/* prefix table session */
	private $_session_prefix_pemilik = '';
	private $_session_prefix_admin = '';

	/* set user table for login query */
	private $_user_model_pemilik = '';
	private $_user_model_admin = '';
	/* prefix table user */
	private $_user_prefix_pemilik = '';
	private $_user_prefix_admin = '';
	/* set visible column in user table for session visible data */
	private $_pk_column = '';
	/* column setting for user username */
	private $_username_column = '';
	/* column setting for user password */
	private $_password_column = '';
	/* column setting for user name / description */
	private $_name_column = '';
	/* column setting for user status */
	private $_status_column = '';
	/* column setting for user failed login attempt */
	private $_failed_login_column = '';
	/* column setting for user last ip used */
	private $_lastip_column = '';
	/* column setting for user last login time */
	private $_lastlogin_column = '';
	/* default redirect page */
	private $_expired_page = '';
	/* default redirect page parameter */
	private $_redir_param = '';
	/* dashboard, if user is login but don't have access */
	private $_dashboard_page = '';

	/* block setting */
	/* berapa lama di block dari last login */
	private $_block_time = 900;
	/* kelipatan berapa block tambah lama */
	private $_block_multiple = 5;

	/*
	 * private object handler
	 */
	private $_current_session;
	/* current session cookie */
	private $_current_session_cookie;
	/* cookie name */
	private $_cookie_name;
	/* session expiration */
	private $_sess_expiration;
	/* session remember in second */
	private $_sess_remember_length = 0;
	/* access limitation - default module */
	private $_default_module = '';
	/*
	 * access limitation - all access
	 */
	private $_access = array();

	/* set if we use root account */
	private $_use_root_account = TRUE;
	/* root account username */
	private $_root_username = '';
	/* root account password */
	private $_root_password = '';
	/* set true di production */
	private $_validate_access = TRUE;
	/* jika session ilang tapi cookie ada boleh buat session lagi apa ga */
	private $_allow_regenerate = FALSE;
	/* untuk menampung array my_access */
	private $_my_access = NULL;
	/* untuk menampung module terakhir yang dicari */
	private $_last_module_access = NULL;
	/* jika ini false, maka user bisa login di beberapa komputer */
	private $_single_login = TRUE;
	/* jika ini true, maka tiap decode akan update activity */
	private $_auto_update_activity = FALSE;
	/* jika ini true, maka password akan di hash dulu sebelum dicocokkan */
    private $_root_password_hash = FALSE;

    private $_admin_column = 'is_admin';
    private $_pemilik_flag = '';

	/* constant untuk definisi akses */
	const ACCESS_VIEW = 'view';
	const ACCESS_ADD = 'add';
	const ACCESS_EDIT = 'edit';
	const ACCESS_DELETE = 'delete';

	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->helper('cookie');
		$this->CI->load->library('encryption');

		$this->_cookie_name     = $this->CI->config->item('sess_cookie_name');
		$this->_sess_expiration = $this->CI->config->item('sess_expiration');
		//load from __framework config
		$this->_session_model_admin    = $this->CI->config->item('auth_session_model_admin');
		$this->_session_model_pemilik  = $this->CI->config->item('auth_session_model_pemilik');
		$this->_session_prefix_admin   = $this->CI->config->item('auth_session_prefix_admin');
		$this->_session_prefix_pemilik = $this->CI->config->item('auth_session_prefix_pemilik');
		$this->_user_model_admin       = $this->CI->config->item('auth_user_model_admin');
        $this->_user_model_pemilik     = $this->CI->config->item('auth_user_model_pemilik');
		$this->_user_prefix_admin      = $this->CI->config->item('auth_prefix_admin');
        $this->_user_prefix_pemilik    = $this->CI->config->item('auth_prefix_pemilik');
		$this->_pk_column              = $this->CI->config->item('auth_pk_column');
		$this->_username_column        = $this->CI->config->item('auth_username_column');
		$this->_password_column        = $this->CI->config->item('auth_password_column');
		$this->_name_column            = $this->CI->config->item('auth_name_column');
		$this->_status_column          = $this->CI->config->item('auth_status_column');
		$this->_failed_login_column    = $this->CI->config->item('auth_failed_column');
		$this->_lastip_column          = $this->CI->config->item('auth_lastip_column');
		$this->_lastlogin_column       = $this->CI->config->item('auth_lastlogin_column');
		$this->_expired_page           = $this->CI->config->item('auth_expired_page');
		$this->_redir_param            = $this->CI->config->item('auth_redir_param');
		$this->_dashboard_page         = $this->CI->config->item('auth_dashboard_page');
		$this->_sess_remember_length   = $this->CI->config->item('auth_session_remember');
		$this->_pemilik_flag           = $this->CI->config->item('auth_pemilik_flag');

		$this->_block_time           = $this->CI->config->item('auth_block_time');
		$this->_block_multiple       = $this->CI->config->item('auth_block_multiple');
		//end of load config

		$this->CI->load->model($this->_session_model_admin);
		$this->CI->load->model($this->_session_model_pemilik);
		$this->CI->load->model($this->_user_model_admin);
		$this->CI->load->model($this->_user_model_pemilik);

		//get root account
		if($this->_use_root_account){
			//use this if setting from config file
			$this->_root_username = $this->CI->config->item('root_username');
			$this->_root_password = $this->CI->config->item('root_password');

			//otherwise you can use setting database
			//$this->CI->load->model('m_setting');
			//$this->_root_username = $this->CI->m_setting->get_by_column('root_username', 'stg_name')->stg_value;
			//$this->_root_password = $this->CI->m_setting->get_by_column('root_password', 'stg_name')->stg_value;
		}

		//delete session expire
		$expire_time = time() - $this->_sess_expiration;
        $this->CI->{$this->_session_model_admin}->permanent_delete_custom("{$this->_session_prefix_admin}lastactivity < {$expire_time}");
        $this->CI->{$this->_session_model_pemilik}->permanent_delete_custom("{$this->_session_prefix_pemilik}lastactivity < {$expire_time}");

		if($this->__decode_user() === FALSE)
			$this->__create_new_session_data();

		//check maintenance
		check_maintenance($this->is_login(), $this->is_root());
    }

	public function add_session($session_name, $session_value){
		$this->_current_session->user_session[$session_name] = $session_value;
		//save to session
		if($this->is_login()){
			$this->__set_login($this->_current_session->profile, $this->_current_session->remember, $this->_current_session->is_root);
		}else{
			$this->__set_login();
		}
	}

	public function add_flash_session($session_name, $session_value){
		$this->_current_session->flash_session[$session_name] = $session_value;
		//save to session
		if($this->is_login()){
			$this->__set_login($this->_current_session->profile, $this->_current_session->remember, $this->_current_session->is_root);
		}else{
			$this->__set_login();
		}
	}

	public function get_my_menu(){
		return $this->get_usergroup(TRUE);
	}

	public function get_usergroup($just_menu = TRUE){
		if($this->is_login() && !$this->is_root()){
            if($this->is_admin())
                return $this->CI->{$this->_user_model_admin}->get_my_usergroup($this->get_user_id(), $just_menu);
            else{
                $current_user = $this->get_user();
                if(!$just_menu)
                    return $current_user->{$this->_pemilik_flag};

                if($current_user->{$this->_pemilik_flag} == YES)
                    return $this->CI->load->view('__base/sidebar_pemilik', '', TRUE);
                else
                    return $this->CI->load->view('__base/sidebar_operator', '', TRUE);
            }
		}
		return FALSE;
	}

	public function get_session($session_name){
		if(isset($this->_current_session->user_session[$session_name]))
			return $this->_current_session->user_session[$session_name];
		else
			return FALSE;
	}

	public function get_flash_session($session_name){
		if(isset($this->_current_session->flash_session[$session_name])){
			$flash_value = $this->_current_session->flash_session[$session_name];
			unset($this->_current_session->flash_session[$session_name]);

			if($this->is_login()){
				$this->__set_login($this->_current_session->profile, $this->_current_session->remember, $this->_current_session->is_root);
			}else{
				$this->__set_login();
			}

			return $flash_value;
		}else
			return FALSE;
	}

	public function get_raw(){
		return $this->_current_session;
	}

	public function get_user(){
		return $this->_current_session->profile;
	}

	public function get_user_id(){
		if(!$this->is_login()) return FALSE;
		if($this->is_root()) return 0;
		$pk_col = ($this->is_admin() ? $this->_user_prefix_admin : $this->_user_prefix_pemilik) . $this->_pk_column;
		return $this->_current_session->profile->{$pk_col};
    }

    public function is_admin(){
        if(!$this->is_login()) return FALSE;
		if($this->is_root()) return TRUE;
		return $this->_current_session->{$this->_admin_column};
    }

	public function get_user_name($strip_length=FALSE){
		if(!$this->is_login()) return FALSE;
		if($this->is_root()) return $this->_root_username;
		$name_col = ($this->is_admin() ? $this->_user_prefix_admin : $this->_user_prefix_pemilik) . $this->_name_column;
		$name = $this->_current_session->profile->{$name_col};
		return $strip_length !== FALSE && strlen($name) > $strip_length ? substr($name, 0, $strip_length-3) . '...' : $name;
	}

	public function is_login(){
		return $this->_current_session->profile !== FALSE || $this->is_root();
	}

	public function is_root(){
		return $this->_current_session->is_root === TRUE;
    }

	public function login_enforce($login_user){
		return $this->__set_login($login_user);
	}

	public function login($username, $password, $remember=FALSE){
		$login_result = $this->__create_result_template();

		if($this->_use_root_account){
            $root_pass_match = FALSE;
            if($this->_root_password_hash)
                $root_pass_match = password_verify($password, $this->_root_password);
            else
                $root_pass_match = $password == $this->_root_password;

			if($username == $this->_root_username && $root_pass_match){
				//set is root
				$this->__set_login(NULL, FALSE, TRUE);
				return $this->__create_result_template(TRUE, $this->CI->lang->line('auth_login_success'));
			}
		}

        $is_admin = FALSE;
        //login ke pemilik dulu
        $user_login = $this->CI->{$this->_user_model_pemilik}->get_by_multiple_column(
												array(
													($this->_user_prefix_pemilik . $this->_username_column) => $username,
													($this->_user_prefix_pemilik . $this->_status_column) . " !=" => DELETED
											 	)
                                             );

        if($user_login === NULL){
            $user_login = $this->CI->{$this->_user_model_admin}->get_by_multiple_column(
                                                    array(
                                                        ($this->_user_prefix_admin . $this->_username_column) => $username,
                                                        ($this->_user_prefix_admin . $this->_status_column) . " !=" => DELETED
                                                    )
                                                );
            if($user_login !== NULL)
                $is_admin = TRUE;
        }

		if($user_login == NULL){
			/* no user found */
			$login_result = $this->__create_result_template(FALSE, $this->CI->lang->line('auth_login_failed'));
		}else{
            $user_table = $is_admin ? $this->_user_model_admin : $this->_user_model_pemilik;
            $col_prefix = $is_admin ? $this->_user_prefix_admin : $this->_user_prefix_pemilik;
			$status_column = $col_prefix . $this->_status_column;
			$failedlogin_column = $col_prefix . $this->_failed_login_column;
            $last_login_column = $col_prefix . $this->_lastlogin_column;
			//check if user status is block
			if($user_login->{$status_column} == BLOCK){
				//cek apakah user masih dalam status block
				$block_multiply = floor($user_login->{$failedlogin_column} / $this->_block_multiple);

				$block_until = DateTime::createFromFormat('Y-m-d H:i:s', $user_login->{$last_login_column});
				$block_until->add(new DateInterval('PT' . ($block_multiply * $this->_block_time) .'S'));

				if($block_until->format('Y-m-d H:i:s') > now())
					return $this->__create_result_template(FALSE, $this->CI->lang->line('auth_user_not_active') . $block_until->format('Y-m-d H:i:s'));
			}else if($user_login->{$status_column} == PERMANENT_BLOCK){
				return $this->__create_result_template(FALSE, $this->CI->lang->line('auth_user_permanent_block'));
			}
			$pk_col = $col_prefix . $this->_pk_column;
			$password_column = $col_prefix . $this->_password_column;
			if(!password_verify($password, $user_login->{$password_column})){
				//increment failed login attempt
				$user_login->{$failedlogin_column}++;
				if($user_login->{$failedlogin_column} > 0 &&
				   $user_login->{$failedlogin_column} % $this->_block_multiple == 0){
					//block user
					$this->CI->{$user_table}->update_multiple_column(
										array(
											$last_login_column => now(),
											$failedlogin_column => $user_login->{$failedlogin_column},
											$status_column => BLOCK
										),
										$user_login->{$pk_col}
									);
				}else{
					//save increment failed login
					$this->CI->{$user_table}->update_single_column(
										$failedlogin_column,
										$user_login->{$failedlogin_column},
										$user_login->{$pk_col}
									);
				}

				return $this->__create_result_template(FALSE, $this->CI->lang->line('auth_login_failed'));
			}

			//update user last login
			$this->CI->{$user_table}->update_multiple_column(
										array(
											$failedlogin_column => 0,
											$last_login_column => now(),
											$col_prefix . $this->_lastip_column => $this->CI->input->ip_address(),
											$status_column => ACTIVE
										),
										$user_login->{$pk_col}
									);

			//set login
			$login_result = $this->__create_result_template(TRUE, $this->CI->lang->line('auth_login_success'));
			if($this->__set_login($user_login, $remember) === FALSE){
				$login_result = $this->__create_result_template(FALSE, $this->CI->lang->line('auth_login_failed'));
			}
		}

		return $login_result;
	}

	public function logout(){
        if(!$this->is_login())
            return;

        $is_admin = $this->is_admin();

		$session_table = $is_admin ? $this->_session_model_admin : $this->_session_model_pemilik;
		$user_table = $is_admin ? $this->_user_model_admin : $this->_user_model_pemilik;
        $pk_col = ($is_admin ? $this->_user_prefix_admin : $this->_user_prefix_pemilik) . $this->_pk_column;
        $session_prefix = ($is_admin ? $this->_session_prefix_admin : $this->_session_prefix_pemilik);

		//delete session from db
		$session_cookie = $this->__get_session_cookie();

		if($session_cookie !== FALSE){
			if($this->is_root())
				$this->CI->{$session_table}->permanent_delete($session_cookie['session_id']);
			else{
				if($this->_single_login)
					$this->CI->{$session_table}->permanent_delete($session_cookie['user_id'], "{$session_prefix}user");
				else
					$this->CI->{$session_table}->permanent_delete($session_cookie['session_id']);
			}
		}
		//delete cookie
		delete_cookie($this->_cookie_name);
	}

	public function set_default_module($module_name){
		$this->_default_module = $module_name;
	}

	public function set_access_view($module=''){
		$this->set_access(self::ACCESS_VIEW, $module);
	}

	public function set_access_add($module=''){
		$this->set_access(self::ACCESS_ADD, $module);
	}

	public function set_access_edit($module=''){
		$this->set_access(self::ACCESS_EDIT, $module);
	}

	public function set_access_delete($module=''){
		$this->set_access(self::ACCESS_DELETE, $module);
	}

	public function set_access($action, $module=''){
		if($module == '') $module = $this->_default_module;
		if(!isset($this->_access[$module]))
			$this->_access[$module] = array();
		$this->_access[$module][] = $action;
	}

    private function __check_pemilik_access($all_access, $is_pemilik){
        $pemilik_access = $this->CI->config->item('pemilik_access');
        $config_access = $pemilik_access[$is_pemilik];

        foreach($all_access as $module => $action){
            if(!isset($config_access[$module]))
                continue;
            if(is_string($config_access[$module]) && $config_access[$module] == '*')
                return TRUE;
			foreach($action as $c_action){
                if(is_string($config_access[$module]) && $config_access[$module] == $c_action ||
                    is_array($config_access[$module]) && in_array($c_action, $config_access[$module]))
                    return TRUE;
			}
        }
        return FALSE;
    }

	/*
	 * custom this function, i will provide some commented example
	 */
	public function validate($redirect=FALSE, $just_login=FALSE){
        //if not login, redirect to expired page
		if(!$this->is_login()){
			$redir_url = $this->_expired_page .
						  (strpos($this->_expired_page, '?') !== false ? '&' : '?') .
						  $this->_redir_param . get_page_url();
			if($redirect){
				redirect($redir_url);
			}else{
				ajax_response('expired', base_url() . $this->_expired_page);
			}
        }

		//if user is root, login as root
		if($this->is_root()) return TRUE;

        $is_admin = $this->is_admin();
		if($just_login || !$this->_validate_access)
			return TRUE;

        $user_table = $is_admin ? $this->_user_model_admin : $this->_user_model_pemilik;
        $have_access = FALSE;
        if($is_admin)
            $have_access = $this->CI->{$this->_user_model_admin}->check_access($this->_access, $this->get_user_id());
        else{
            $current_user = $this->get_user();
            $have_access = $this->__check_pemilik_access($this->_access, $current_user->{$this->_pemilik_flag});
        }

		if(!$have_access){
			if($redirect){
				error_message($this->CI->lang->line('cannot_access'));
			}else{
				ajax_response('error', $this->CI->lang->line('cannot_access'));
			}
		}

		return TRUE;
	}

	public function reload_user(){
		if(!$this->is_login() || $this->is_root())
            return;

		$current_user = $this->get_user();
        $is_admin = $this->is_admin();
		$pk_column = ($is_admin ? $this->_user_prefix_admin : $this->_user_prefix_pemilik) . $this->_pk_column;
		$pk_id = $current_user->{$pk_column};

		$user_model = $is_admin ? $this->_user_model_admin : $this->_user_model_pemilik;

		$new_user = $this->CI->{$user_model}->get_by_column($pk_id);

		if($new_user == NULL)
			return;

		$this->__set_login($new_user);
	}

	private function __create_new_session_data(){
		$session_data = new stdClass();
		$session_data->{$this->_admin_column} = FALSE; //defaultnya adalah pemilik, supaya ga jadi admin
		$session_data->profile = FALSE;
		$session_data->remember = FALSE;
		$session_data->is_root = FALSE;
		$session_data->user_session = array();
		$session_data->flash_session = array();

		$this->_current_session = $session_data;
	}

	private function __create_result_template($success=FALSE, $message='initialization object'){
		$result = new stdClass();
		$result->success = $success;
		$result->message = $message;

		return $result;
	}

    //pending, sek urusin cookienya
	private function __decode_user(){
		$session_cookie = $this->__get_session_cookie();
		if($session_cookie === FALSE) return FALSE;

		/* check decoded cookie expired information */
		if($session_cookie['cookie_expired'] < time()) return FALSE;
        $is_admin = $session_cookie['is_admin'] === TRUE;

		$session_table = $is_admin ? $this->_session_model_admin : $this->_session_model_pemilik;
		$user_table = $is_admin ? $this->_user_model_admin : $this->_user_model_pemilik;
        $session_obj = $this->CI->{$session_table}->get_by_column($session_cookie['session_id']);

		if($session_obj == NULL){
			if(!$this->_allow_regenerate){
				delete_cookie($this->_cookie_name);
				return FALSE;
			}
			if($session_cookie['user_id'] != '0'){
				$login_user = $this->CI->{$user_table}->get_by_column($session_cookie['user_id']);
				if($login_user != NULL){
					$this->__create_new_session_data();
					return $this->__set_login($login_user, $session_cookie['remember'] == '1' ? TRUE : FALSE);
				}else{
					delete_cookie($this->_cookie_name);
					return FALSE;
				}
			}else if($session_cookie['user_id'] == '0'){
				$this->__create_new_session_data();
				return $this->__set_login(NULL, FALSE, TRUE);
			}
		}

		$this->_current_session_cookie = $session_cookie;

		$data_column = ($is_admin ? $this->_session_prefix_admin : $this->_session_prefix_pemilik) . 'content';
		$session_data = unserialize($session_obj->{$data_column});
		if(empty($session_data)){
			//error in decoding json
			return FALSE;
		}else{
			if($this->_auto_update_activity)
				$this->update_session_activity();
			//set session data
			$this->_current_session = $session_data;
		}

		return TRUE;
	}

	public function update_session_activity(){
		if(!$this->is_login())
			return;

        $is_admin = $this->is_admin();

		$pk_col = ($is_admin ? $this->_user_prefix_admin : $this->_user_prefix_pemilik) . $this->_pk_column;
        $session_table = ($is_admin ? $this->_session_model_admin : $this->_session_model_pemilik);
        $session_prefix = ($is_admin ? $this->_session_prefix_admin : $this->_session_prefix_pemilik);
		//$session_id =  "{$session_prefix}id";
		//update session
		$this->CI->{$session_table}->update_single_column(
										"{$session_prefix}lastactivity",
										time(),
                                        $this->_current_session_cookie['session_id']
                                    );

		$saved_id = NULL;
		if($this->is_root()){
			$saved_id = '0';
		}else if($this->_current_session->profile !== FALSE){
			$saved_id = $this->_current_session->profile->{$pk_col};
		}

		//update cookie
		$expire = time() + ($this->_current_session_cookie['remember'] == '1' ? $this->_sess_remember_length : $this->_sess_expiration);
		$cookie = array(
					   'name'   => $this->_cookie_name,
					   'value'  => $this->CI->encryption->encrypt(
                                                            $this->_current_session_cookie['session_id'] . "|" .
															($saved_id == NULL ? '' : $saved_id) . "|" .
															$this->_current_session_cookie['remember'] . "|" .
															$expire . "|" .
                                                            ($this->is_admin() ? '1' : '0')
                                                        ),
					   'expire' => $this->_current_session_cookie['remember'] == '1' ? $this->_sess_remember_length : $this->_sess_expiration
				  );
		set_cookie($cookie);
	}

	private function __generate_session_id(){
		$session_id = guid() . guid();
		$session_id .= $this->CI->input->ip_address();
		return substr(md5(uniqid($session_id, TRUE)) . guid(), 0, 40);
	}

	private function __get_session_cookie(){
		$cookie = get_cookie($this->_cookie_name);
		if($cookie === FALSE) return FALSE;

		$cookie_encrypted = $this->CI->encryption->decrypt($cookie);
		if($cookie_encrypted === FALSE) return FALSE;

		$session_data = explode('|', $cookie_encrypted);
		if(count($session_data) != 5) return FALSE;
		return array(
                     'session_id' => $session_data[0], //session id
					 'user_id' => intval($session_data[1]), //pk user
					 'remember' => $session_data[2], //true or false
					 'cookie_expired' => $session_data[3], //true or false
                     'is_admin' => $session_data[4] == '1' ? TRUE : FALSE
                    ); //cookie expired
	}

	private function __set_login($user_login=NULL, $remember=FALSE, $is_root=FALSE){
		$session_cookie = $this->__get_session_cookie();

		//if current cookie exist
        $is_admin = $is_root;
        $session_table = $is_root === TRUE ? $this->_session_model_admin : $this->_session_model_pemilik;
        $session_prefix = $is_root === TRUE ? $this->_session_prefix_admin : $this->_session_prefix_pemilik;
		if($session_cookie !== FALSE){
            $is_admin = $session_cookie['is_admin'];
            $session_table = $is_admin ? $this->_session_model_admin : $this->_session_model_pemilik;
            $session_prefix = $is_admin ? $this->_session_prefix_admin : $this->_session_prefix_pemilik;

			//deleted current session, if exist
			$this->CI->{$session_table}->permanent_delete($session_cookie['session_id']);

			//delete user in session data == user in cookie
			if($this->_single_login)
				$this->CI->{$session_table}->permanent_delete($session_cookie['user_id'], "{$session_prefix}user");
		}
		if($this->_single_login && $is_root)
			$this->CI->{$this->_session_model_admin}->permanent_delete_custom("{$this->_session_prefix_admin}user is null");

        $user_id = FALSE;
        if($user_login != NULL){
            //delete same user session with user == login user
            $is_admin = isset($user_login->{$this->_user_prefix_admin . $this->_pk_column});
            $session_table = $is_admin ? $this->_session_model_admin : $this->_session_model_pemilik;
            $session_prefix = $is_admin ? $this->_session_prefix_admin : $this->_session_prefix_pemilik;
            $user_id = ($is_admin ? $this->_user_prefix_admin : $this->_user_prefix_pemilik) . $this->_pk_column;

			if($this->_single_login)
				$this->CI->{$session_table}->permanent_delete($user_login->{$user_id}, "{$session_prefix}user");
			//resetting session data
			$this->_current_session->profile = $user_login;

			if($session_cookie !== FALSE)
				$remember = $session_cookie['remember'] == '1' ? TRUE : FALSE;
		}
		$this->_current_session->remember = $remember;
        $this->_current_session->is_root = $is_root;
        $this->_current_session->{$this->_admin_column} = $is_admin;
		$session_id = $this->__generate_session_id();

		$current_time = time();

        $saved_id = NULL;
        //user_id ada di if ini
		if($user_login != NULL && !$is_root){
			$saved_id = $user_login->{$user_id};
        }else if($is_root)
			$saved_id = 0;

		$this->CI->{$session_table}->insert(
                                        $session_id,
										$this->CI->input->ip_address(),
										substr($this->CI->input->user_agent(), 0, 120),
										$current_time,
										serialize($this->_current_session),
                                        $saved_id
                                    );
		$expire = time() + ($remember ? $this->_sess_remember_length : $this->_sess_expiration);
		$cookie = array(
					   'name'   => $this->_cookie_name,
					   'value'  => $this->CI->encryption->encrypt(
                                                            $session_id . "|" .
															($saved_id == NULL ? '' : $saved_id) . "|" .
															($remember == TRUE ? '1' : '0') . "|" .
                                                            $expire . "|" .
															($is_admin ? '1' : '0')
                                                        ),
					   'expire' => $remember ? $this->_sess_remember_length : $this->_sess_expiration
				  );
		set_cookie($cookie);
		return TRUE;
	}

	public function is_valid_access(){
		return count($this->_access) > 0;
	}

	/*
	 * fungsi untuk mendapatkan akses default module
	 */
	public function get_module_access($module='', $useDefaultAccess=TRUE){
        if(!$this->is_login())
            return array();

		if($module == '')
			$module = $this->_default_module;

		if($this->_my_access !== NULL && $this->_last_module_access == $module)
			return $this->_my_access;

		$this->_last_module_access = $module;

		$user_table = $this->is_admin() ? $this->_user_model_admin : $this->_user_model_pemilik;
		$access = array();
		if(!$this->is_root()){
            if($this->is_admin()){
		        $actions = $this->CI->{$user_table}->master_data_access($module);
                $user_access = $this->CI->{$user_table}->master_data_access($module, $this->get_user_id());

                foreach($user_access as $c_access){
				    $access[$c_access->act_name] = TRUE;
                }

                foreach($actions as $c_action){
                    if(!isset($access[$c_action->act_name]))
                        $access[$c_action->act_name] = FALSE;
                }
            }else{
                $current_user = $this->get_user();
                $pemilik_access = $this->CI->config->item('pemilik_access');
                $user_access = $pemilik_access[$current_user->{$this->_pemilik_flag}];
                if(isset($user_access[$module])){
                    foreach($user_access[$module] as $c_accessK => $c_accessV){
                        if(is_array($c_accessV)){
                            foreach($c_accessV as $i_access)
                                $access[c_accessV] = TRUE;
                        }else
                            $access[c_accessV] = TRUE;
                    }
                }
            }
		}else{
            $actions = $this->CI->{$user_table}->master_data_access($module);
			foreach($actions as $c_action){
				$access[$c_action->act_name] = TRUE;
			}
		}

		if($useDefaultAccess){
			if(!isset($access[self::ACCESS_VIEW]))
				$access[self::ACCESS_VIEW] = $this->is_root();
			if(!isset($access[self::ACCESS_ADD]))
				$access[self::ACCESS_ADD] = $this->is_root();
			if(!isset($access[self::ACCESS_EDIT]))
				$access[self::ACCESS_EDIT] = $this->is_root();
			if(!isset($access[self::ACCESS_DELETE]))
				$access[self::ACCESS_DELETE] = $this->is_root();
		}

		$json_access = array();
		foreach($access as $c_accessK => $c_accessV){
			$json_access[] = "$c_accessK:" . ($c_accessV ? "true" : "false");
		}

		$module_access = array(
						'access' => $access,
						'json_access' => '{' . implode(",", $json_access) . '}'
					 );

		return $module_access;
	}

	public function is_have_access_to_action($action, $module=''){
        if(!$this->is_login())
            return FALSE;

		if($module == '')
			$module = $this->_default_module;

		$module_access = $this->get_module_access($module);
        $my_access = $module_access['access'];

        if(isset($my_access[$action]))
            return $my_access[$action];

        if(!$this->is_admin() && isset($my_access['*']))
            return TRUE;

		return FALSE;
	}
}
