<?php
class OAuth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->config->load('facebook');
        $this->Facebook = $this->config->item('facebook');
    }


	public function index()
	{
		$data['redirect'] = 2000;
		$this->load->view('header');
		$this->load->view('oauth_status', $data);
		$this->load->view('footer');
	}
	
	public function login()
	{
	
		redirect($this->getOAuthLink(), 'location');
		
	}
	
	public function loginError($error = 1)
	{
		$text[1]	= Array('title' => 'Not authorized', 'message' => 'You are not authorized. Please login (with another account)');
		$text[2]	= Array('title' => 'Not logged in',  'message' => 'You are not logged in. Please login.');
		
		$data = $text[$error];
		$data['OAuthLink'] = $this->getOAuthLink();
		$this->load->view('header');
		$this->load->view('oauth_error', $data);
		$this->load->view('footer');		
	}
	
	public function auth()
	{
		if($this->input->get('code') != false) {
			$url =	sprintf('https://graph.facebook.com/oauth/access_token?client_id=%s&redirect_uri=%s&client_secret=%s&code=%s', 
							$this->Facebook['api_id'], 
							$this->Facebook['redirect_url'],
							$this->Facebook['app_secret'],
							urlencode($this->input->get('code'))
						);
			parse_str($this->get_fb_contents($url));
			redirect('/OAuth/auth?access_token=' . $access_token, 'location');
		} elseif($this->input->get('access_token') !== false) {
			$this->session->unset_userdata('Facebook');
			$url = 	sprintf('https://graph.facebook.com/me?access_token=%s',
							urlencode($this->input->get('access_token'))
						);
			$user['Facebook'] = json_decode($this->get_fb_contents($url), true);
      		$user['Facebook']['access_token'] = $this->input->get('access_token');
      		if(!empty($user['Facebook']['id'])) {
      			$this->session->set_userdata($user);
	      		redirect('/', 'location');
      		} elseif(!empty($user['Facebook']['error'])) {
      			// error
      			redirect('/OAuth/login', 'location');
      		} else {
      			// unknown
      		}
		}
	}
	
	public function logout()
	{
		$this->session->unset_userdata('Facebook');
		redirect('/OAuth/', 'location');
	}
	
	public function deauth()
	{
		
	}
	
	/**
	* calling facebook api using curl and return response.
	*/
	protected function get_fb_contents($url) {
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$response = curl_exec( $curl );
		curl_close( $curl );
		return $response;
	}
	
	protected function getOAuthLink() {
		$url =	sprintf('https://graph.facebook.com/oauth/authorize?client_id=%s&redirect_uri=%s&scope=%s', 
				$this->Facebook['api_id'], 
				$this->Facebook['redirect_url'], 
				implode(',', $this->Facebook['permissions'])
			);
			
		return $url;
	}
}
?>
