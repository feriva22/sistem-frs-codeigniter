<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/*
	 * constructor class
	 */
	public function __construct() {
		parent::__construct();

		//load this page model

	}

	public function index()
	{
		$this->load->view('welcome_message');
	}
}
