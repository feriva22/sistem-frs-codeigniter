<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_admin extends MY_Model {
    protected $pk_col = 'adm_id';
    protected $table = 'admin';
    
    function __construct()
    { parent::__construct(); }
    
    public function select(){
		if($this->_default_select === TRUE){
            $cols = array(
                'adm_id','adm_name','adm_username','adm_email','adm_password',
                'adm_lastlogin','adm_status','adm_created','adm_updated'
            );
		}else if($this->_default_select == 'detail'){
            $cols = array(
                'adm_id','adm_name','adm_username','adm_email',
                'adm_lastlogin','adm_status','adm_created','adm_updated'
            );
		}

		if($this->_default_from){
            $this->db->from($table);
        }
    }
    
    public function insert($adm_name=FALSE,
                    $adm_username=FALSE,
                    $adm_email=FALSE,
					$adm_password=FALSE,
					$adm_lastlogin=FALSE,
					$adm_status=FALSE
					){
		$data = array();
        if($adm_name              !== FALSE)$data['adm_name']              =trim($adm_name);
        if($adm_username          !== FALSE)$data['adm_username']          =trim($adm_username);
        if($adm_email             !== FALSE)$data['adm_email']             =trim($adm_email);
        if($adm_password          !== FALSE)$data['adm_password']          =trim($adm_password);
        if($adm_lastlogin         !== FALSE)$data['adm_lastlogin']         =($adm_lastlogin == '' ? NULL : $adm_lastlogin);
        if($adm_status            !== FALSE)$data['adm_status']            =$adm_status;
        $data['adm_created'] = date("Y-m-d H:i:s"); //created
		$this->db->insert('admin', $data);
		return $this->db->insert_id();
    }
    
    public function update($adm_id=FALSE,
					$adm_name=FALSE,
                    $adm_username=FALSE,
                    $adm_email=FALSE,
					$adm_password=FALSE,
					$adm_lastlogin=FALSE,
					$adm_status=FALSE
					){
		$data = array();
        if($adm_name              !== FALSE)$data['adm_name']              =trim($adm_name);
        if($adm_username          !== FALSE)$data['adm_username']          =trim($adm_username);
        if($adm_email             !== FALSE)$data['adm_email']             =trim($adm_email);
        if($adm_password          !== FALSE)$data['adm_password']          =trim($adm_password);
        if($adm_lastlogin         !== FALSE)$data['adm_lastlogin']         =($adm_lastlogin == '' ? NULL : $adm_lastlogin);
        if($adm_status            !== FALSE)$data['adm_status']            =$adm_status;
        $data['adm_updated'] = date("Y-m-d H:i:s"); //updated
		return $this->db->update('admin', $data, "adm_id = '$adm_id'");
	}
}