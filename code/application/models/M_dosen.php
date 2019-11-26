<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_dosen extends MY_Model {
    protected $pk_col = 'dos_id';
    protected $table = 'dosen';

    public function __construct()
    { parent::__construct(); }

    public function select(){
        if($this->default_select){
            $this->db->select('dos_id');
            $this->db->select('dep_nama as dos_departemen');
            $this->db->select('dos_nip');
            $this->db->select('dos_nama');
            $this->db->select('dos_nohp');
            $this->db->select('dos_alamat');
            $this->db->select('dos_status');
            $this->db->select('dos_created');
            $this->db->select('dos_updated');
        } else if($this->detail_select){
            //for detail get like sensitive information
        }
        $this->db->from($this->table);
        $this->db->join('departemen','dos_departemen = dep_id');
    }
}