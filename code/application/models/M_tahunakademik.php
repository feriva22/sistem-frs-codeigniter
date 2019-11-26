<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_tahunakademik extends MY_Model {
    protected $pk_col = 'tak_id';
    protected $table = 'tahun_akademik';

    public function __construct()
    { parent::__construct(); }

    public function select(){
        if($this->default_select){
            $this->db->select('tak_id');
            $this->db->select('tak_tahun');
            $this->db->select('tak_isganjil');
        } else if($this->detail_select){
            //for detail get like sensitive information
        }
        $this->db->from('tahun_akademik');
    }

    


}