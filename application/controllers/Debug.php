<?php
class Debug extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }


	public function index()
	{
		$this->load->view('header');
		$this->load->view('debug');
		$this->load->view('footer');
	}
}
?>