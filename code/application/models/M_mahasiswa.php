<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_mahasiswa extends MY_Model {
    protected $pk_col = 'mhs_id';
    protected $table = 'mahasiswa';

    public function __construct()
    { parent::__construct(); }

    public function select(){
        if($this->default_select){
            $this->db->select('mhs_id');
            $this->db->select('mhs_nim');
            $this->db->select('mhs_nama');
            $this->db->select('mhs_nohp');
            $this->db->select('mhs_alamat');
            $this->db->select('mhs_tahunmasuk');
            $this->db->select('dep_nama AS mhs_departemen');
            $this->db->select('mhs_status');
            $this->db->select('mhs_created');
            $this->db->select('mhs_updated');
        } else if($this->detail_select){
            //for detail get like sensitive information
        }
        $this->db->from('mahasiswa');
        $this->db->join('departemen','mhs_departemen = dep_id');
    }
}