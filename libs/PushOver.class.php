<?php
class PushOver {
    private $db;
    private $gmClient;
    private $api_key;
    private $api_url = 'https://api.pushover.net/1/messages.json';
    private $enabled = false;

    /**
     * Constructor
     * 
     */
	public function __construct($api_key) {
		
		/*$this->db = new mysqli($host, $user, $pass, $name);
		if ($this->db->connect_errno) {
			doOutput('Failed to connect to MySQL: (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
		}*/
		
		$this->api_key = $api_key;
		$this->gmClient	= new GearmanClient();
		$this->gmClient->addServer();
		
		if(!empty($api_key)) {
			$this->enabled = true;
		}
	}
	

	public function sendMessage($user, $message, $userFields=null, $queued = true) {
	    $defaultFields  = Array('user'      => $user,
	                            'message'   => $message);

        $finalFields = is_array($userFields) ? array_merge($defaultFields, $userFields) : $defaultFields;
        $this->sendMessageArray($finalFields, false, $queued);
	} 
	
	public function sendMessageArray($userFields, $serialized = false, $queued = true) {
		
		if(!$this->enabled) {
			return;
		}
		
	    // implement check for existents of fields user & message
        
        if($serialized) {
            $userFields = unserialize($userFields);
        }
        
        //queuing doesn't work.
        $this->doSendMessage($userFields);
        return null;
        
	    if(!$queued || $queued) {
	        $this->doSendMessage($userFields);
	    } else {
            $this->gmClient->doNormal(WASPY_GMAN . '_sendPushMessage', serialize($userFields));
	    }
	} 
	
	private function doSendMessage($userFields) {
	    $defaultFields = array('token' => $this->api_key, 'timestamp' => time());
	    $postFields = array_merge($defaultFields, $userFields);
        curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => $this->api_url,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_SAFE_UPLOAD => true,
        ));
        curl_exec($ch);
        curl_close($ch);
	}
}
?>
