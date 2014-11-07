<?php
require_once(WASPY_DIR . '/libs/WhatsAPI/src/whatsprot.class.php');

class WhatsAPIPlus extends WhatsProt {
	protected $_contactSync;
	public $contacts;

	/**
	* Constructor method
	*
	* @param string $config
	*   Array holding 'pass', 'phone', 'ident' and 'name'
	* @param bool $debug
	*   true to enable debug
	*/
	public function __construct($config, $debug = false) {
		$this->password = $config['pass'];
		parent::__construct($config['phone'], $config['ident'], $config['name'], $debug);
	}

	public function connectAndLogin() {
		$this->connect();
		parent::LoginWithPassword($this->password);
	}

	/**
	* Send presence unsubscription, disable receiving presence updates.
	*
	* @param string $to
	*   Phone number.
	*/
	public function sendPresenceUnsubscription($to) {
		$node = new ProtocolNode("presence", array("type" => "unsubscribe", "to" => $this->getJID($to)), null, "");
		$this->sendNode($node);
	}

	/**
	* Toggle debug
	*
	* @param int $active
	*   1 to enable debug, 0 to disable debug
	*/
	public function toggleDebug($active = 0) {
		$this->debug = ($active == 1) ? true : false;
	}
}
?>