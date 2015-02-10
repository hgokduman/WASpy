<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presence extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('presence_model');
		if(!$this->auth->isLoggedIn()) {
			redirect('/OAuth/login');
		} elseif(!$this->auth->isAdmin()) {
        	redirect('/OAuth/login/1');
        }
    }


	public function index($phone_number = null)
	{

		$data['presence'] = $this->presence_model->getPresenceData(250, 7, $phone_number);
		$data['phone_number'] = $phone_number;
		if(!is_null($phone_number)) {
			$data['detail'] = 'full';
			$this->session->set_userdata('presence_detail', 'full');
		}
		$this->load->view('header', $data);
		$this->load->view('presence', $data);
		$this->load->view('footer');
	}

	public function stats($phone_number, $detail)
	{
		switch($detail) {
			case 'daily':
				$data['stats'] = $this->presence_model->getDailyStats($phone_number);
				$data['periodInSeconds'] = 24*60*60;
				break;

			case 'hourly':
				$data['stats'] = $this->presence_model->getHourlyStats($phone_number);
				$data['periodInSeconds'] = 60*60;
				break;

			default:
				redirect('/');
		}

	$data['phone_number'] = $phone_number;
	$data['detail'] = $detail;
	$this->session->set_userdata('presence_detail', $detail);
	$this->load->view('header', $data);
	$this->load->view('presence_stats', $data);
	$this->load->view('footer');
	}
}
