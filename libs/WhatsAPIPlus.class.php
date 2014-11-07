<?php
require_once(WASPY_DIR . '/libs/WhatsAPI/src/whatsprot.class.php');

class WhatsAPIPlus extends WhatsProt {
	public function __construct($number, $ident, $alias, $pass, $debug = false) {
		$this->password = $pass;
		parent::__construct($number, $ident, $alias, $debug);
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