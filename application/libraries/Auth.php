<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Auth {

    protected $Facebook;
    
    public function __construct()
    {
        $CI =& get_instance();
        $CI->config->load('facebook');
        $this->Facebook = $CI->session->userdata('Facebook');
        $this->Facebook_admin = $CI->config->item('admin_uid', 'facebook');
        
        if(is_null($this->Facebook)) {
            $this->Facebook = Array();
        }
    }
    
    public function isLoggedIn()
    {
        if(!empty($this->Facebook['id'])) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getUserDetails()
    {
        return $this->Facebook;
    }
    
    public function isAdmin()
    {
        if($this->isLoggedIn() && $this->Facebook['id'] == $this->Facebook_admin) {
            return true;
        } else {
            return false;
        }
    }
}