<?php
class WASpy {
    private $db;
    private $gmClient;
    private $phone_number;
    
    /**
     * Constructor
     * 
     */
	public function __construct($host, $user, $pass, $name, $phone_number='000000') {
		
		$this->db = new mysqli($host, $user, $pass, $name);
		if ($this->db->connect_errno) {
			doOutput('Failed to connect to MySQL: (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
		}
		
		$this->phone_number = $phone_number;
		$this->gmClient	= new GearmanClient();
		$this->gmClient->addServer();

	}
	
	/**
	 * getMessage
	 * @param int $id
	 *   messageId (not msgid!)
	 */
	 public function getMessage($id) {
		if ($stmt = $this->db->prepare('SELECT phone_rcpt, phone_from, body from ' . DB_PREFIX . 'messages where id=?')) {
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($phone_rcpt, $phone_from, $body);
			if($stmt->num_rows == 1) {
				$stmt->fetch();
				$stmt->close();		
    			return Array($phone_rcpt, $phone_from, $body);
			}
		}
		return null;
	}
	
	/**
	 * addSubscriber
	 * @param string $phone
	 *   phone_number
	 */
	public function addSubscriber($phone) {
	    // phone number needs to be added into subscriptions_active table
	    // if we're not subscribed to this number
	    if(!$this->isSubscribed($phone)) {
			$stmt = $this->db->prepare('INSERT INTO ' . DB_PREFIX . 'subscriptions_active (phone_rcpt, phone_from, activated) VALUES (?, ?, ?)');
			$stmt->bind_param('sss', $this->phone_number, $phone, ts2date(time()));
			$stmt->execute();
			$stmt->close();
	    }
        $this->gmClient->doNormal(WASPY_GMAN . '_PresenceSubscribe', $phone);
	}
	
	/**
	 * delSubscriber
	 * 
	 * @param string $phone
	 *   phone_number
	 * @param string $reason
	 *   reason for unsubscribing, default 'default'
	 */
	public function delSubscriber($phone, $reason = 'default') {
	    // Delete entry from subscriptions_active and add new one
	    // in subscriptions_history if we're subscribed...
	    if($this->isSubscribed($phone)) {
    		if ($stmt = $this->db->prepare('SELECT id, phone_rcpt, phone_from, activated from ' . DB_PREFIX . 'subscriptions_active where phone_from=? and phone_rcpt=?')) {
    			$stmt->bind_param('ss', $phone, $this->phone_number);
    			$stmt->execute();
    			$stmt->store_result();
    			$stmt->bind_result($id, $phone_rcpt, $phone_from, $activated);
    			if($stmt->num_rows == 1) {
    				$stmt->fetch();
    				$stmt->close();	
    				
    				// add row into table subscriptions_history
        			$stmt = $this->db->prepare('INSERT INTO ' . DB_PREFIX . 'subscriptions_history (id, phone_rcpt, phone_from, activated, deactivated, reason) VALUES (?, ?, ?, ?, ?, ?)');
        			$stmt->bind_param('ssssss', $id, $phone_rcpt, $phone_from, $activated, ts2date(time()), $reason);
        			$stmt->execute();
        			$stmt->close();
        			
        			// delete row from table subscriptions_active
            		$stmt = $this->db->prepare('DELETE FROM ' . DB_PREFIX . 'subscriptions_active where phone_from=? and phone_rcpt=?');
        			$stmt->bind_param('ss', $phone, $this->phone_number);
            		$stmt->execute();
            		$stmt->close();
    			}
    		}
	    }
        $this->gmClient->doNormal(WASPY_GMAN . '_PresenceUnsubscribe', $phone);
	}
	
	/**
	 * isSubscribed
	 * 
	 * @param string $phone
	 *   phone_number
	 */
	public function isSubscribed($phone) {
		if($stmt = $this->db->prepare('SELECT count(*) from ' . DB_PREFIX . 'subscriptions_active where phone_rcpt=? and phone_from=?')) {
			$stmt->bind_param('ss', $this->phone_number, $phone);
			$stmt->execute();
			$stmt->bind_result($rows);
			$stmt->fetch();
			$stmt->close();
			if($rows == 1) {
				return true;
			} else {
			    return false;
			}
		} 
		return null;
	}
	
	/**
	 * checkPresence
	 * 
	 * @param string $phone
	 *   phone_number
	 * 
	 */
	private function checkPresence($phone) {
	    if($this->isSubscribed) {
	        $this->addSubscriber($phone);
	    } else {
	        $this->addSubscriber($phone);
	        $this->delSubscriber($phone, 'checkPresence');
	    }
	}
	
	/**
	 * getPresence
	 * 
	 * @param string $phone
	 *   phone_number
	 * 
	 */
	public function getPresence($phone) {
	    $this->checkPresence($phone);
	    usleep(500);
	    
	    // get data from db...
	    
	}
	
	/**
	 * getLastSeen
	 * 
	 * @param string $phone
	 *   phone_number
	 * 
	 */
	public function getLastSeen($phone) {
	    $this->gmClient->doNormal(WASPY_GMAN . '_RequestLastSeen', $phone);
	    usleep(500);
	    
	    // get data from db...
	}
	
	/**
	 * sendMessage
	 * 
	 * @param string $rcpt
	 *   phone_number to send message to
	 * 
	 * @param string $txt
	 *   text to send
	 * 
	 */
	public function sendMessage($rcpt, $txt) {
	    $this->gmClient->doNormal(WASPY_GMAN . '_SendMessage', serialize(Array($rcpt, $txt)));
	}
	
	/**
	 * getPresenceData
	 * 
	 * @param string $phone
	 *   phone_number
	 * 
	 * @param int $rows
	 *   number of rows
	 * 
	 */
	public function getPresenceData($phone=null, $rows=100) {
		$rows = Array();
		if($stmt = $this->db->prepare('SELECT id, phone_from phone_number, status, received FROM ' . DB_PREFIX . 'presence WHERE phone_from like ? order by id desc limit 100')) {
			$stmt->bind_param('s', is_null($phone) ? '%' : $phone);
			$stmt->execute();
			$stmt->bind_result($id, $phone_number, $status, $received);
			while($stmt->fetch()) {
				$rows[] = Array('id' => $id, 'phone_number'=> $phone_number, 'status'=>$status, 'received' => $received);
			}
			$stmt->close();
		}
		return $rows;
	}
	
	public function needToNotify($phone, $event, $value = '%') {
		if($stmt = $this->db->prepare('SELECT count(*) FROM ' . DB_PREFIX . 'notifications WHERE phone_from = ? and eventName = ? and eventValue like ?')) {
			$stmt->bind_param('sss', $phone, $event, is_null($value) ? '%' : $value);
			$stmt->execute();
			$stmt->bind_result($rows);
			$stmt->fetch();
			$stmt->close();
			
			// check if a notification was sent for the same event in past PUSHOVER_NAG minutes. If so, return false.
			if($rows > 0) {
				return true;
			}
		}
		return false;
	}
}
?>