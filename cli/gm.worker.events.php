<?php
require_once('../config.inc.php');
require_once(WASPY_DIR . '/libs/functions.inc.php');
require_once(WASPY_DIR . '/libs/PushOver.class.php');
require_once(WASPY_DIR . '/libs/WASpy.class.php');

$GWorker= new GearmanWorker();
$GWorker->addServer();
$GWorker->setId(WASPY_GMAN . '_Events');

$GClient= new GearmanClient();
$GClient->addServer();

$Push = new PushOver(PUSHOVER_API);

$Spy = new WASpy(DB_HOST, DB_USER, DB_PASS, DB_NAME, PHONE_NUMBER);

$Db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($Db->connect_errno) {
	doOutput("Failed to connect to MySQL: (" . $Db->connect_errno . ") " . $Db->connect_error);
}

function GetDb() {
    global $Db;
    return $Db;
}

function GetGClient() {
	global $GClient;
	return $GClient;
}

function GetPush() {
	global $Push;
	return $Push;
}

function GetSpy() {
	global $Spy;
	return $Spy;
}

$GWorker->addFunction(WASPY_GMAN . '_onPresence', function(GearmanJob $job) {
	$presence = unserialize($job->workload());
	if ($stmt = GetDb()->prepare('INSERT INTO ' . DB_PREFIX . 'presence (phone_rcpt, phone_from, status, received) VALUES (?, ?, ?, ?)')) {
		$stmt->bind_param('ssss', $presence[0], jid2phone($presence[1]), $presence[2], ts2date(time()));
		$stmt->execute();
		$stmt->close();
		// pushover test
		if(GetSpy()->needToNotify(jid2phone($presence[1]), 'onPresence', $presence[2])) {
			GetPush()->sendMessage(PUSHOVER_KEY, jid2phone($presence[1]) . ' is ' . $presence[2], Array());
		}
	} else {
		doOutput(GetDb()->error);
	}
});
$GWorker->addFunction(WASPY_GMAN . '_onGetMessage', function(GearmanJob $job) {
	$msg = unserialize($job->workload());
	if ($stmt = GetDb()->prepare('INSERT INTO ' . DB_PREFIX . 'messages (phone_rcpt, phone_from, msgid, msgtype, msgtime, sender, body, received) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {
		$bodyDB = $msg[3] == 'text' ? $msg[6] : null;
		$stmt->bind_param('ssssssss', $msg[0], jid2phone($msg[1]), $msg[2], $msg[3], Ts2Date($msg[4]), $msg[5], $bodyDB, Ts2Date(time()));
		$stmt->execute();
		$stmt->close();
		
		// pushover test
		if(GetSpy()->needToNotify(jid2phone($msg[1]), 'onGetMessage')) {
			GetPush()->sendMessage(PUSHOVER_KEY, 'Message received from ' . jid2phone($msg[1]), Array(), true);
		}
	} else {
		doOutput(GetDb()->db->error);
	}
});
$GWorker->addFunction(WASPY_GMAN . '_onGetRequestLastSeen', function(GearmanJob $job) {
	$lastseen = unserialize($job->workload());
	$lastseenTs = (int) substr($lastseen[2], strpos($lastseen[2], '-')+1, strrpos($lastseen[2], '-')-strpos($lastseen[2], '-')-1) - $lastseen[3];
	if ($stmt = GetDb()->prepare('INSERT INTO ' . DB_PREFIX . 'lastseen (phone_rcpt, phone_from, msgid, lastseen, received) VALUES (?, ?, ?, ?, ?)')) {
		$stmt->bind_param('sssss', $lastseen[0], jid2phone($lastseen[1]), $lastseen[2], ts2date($lastseenTs), ts2date(time()));
		$stmt->execute();
		$stmt->close();
	} else {
		doOutput(GetDb()->error);
	}
});
$GWorker->addFunction(WASPY_GMAN . '_EventDebug', function(GearmanJob $job) {
	if(!DEBUG_EVENTS) {
		return null;
	} 
	list($event, $workload) = unserialize($job->workload());
	
	
	// exclude events from being logged...
	if(in_array($event, array('onSendPong'))) {
		return null;
	}
	
	if ($stmt = GetDb()->prepare('INSERT INTO ' . DB_PREFIX . 'events (event_name, workload, inserted) VALUES (?, ?, ?)')) {
		$stmt->bind_param('sss', $event, serialize($workload), ts2date(time()));
		$stmt->execute();
		$stmt->close();
	} else {
		doOutput(GetDb()->error);
	}
});
$GWorker->addFunction(WASPY_GMAN . '_onConnect', function(GearmanJob $job) {
	if ($stmt = GetDb()->prepare('insert into ' . DB_PREFIX . 'presence (phone_rcpt, phone_from, status, received) select phone_rcpt, phone_from, \'start\' status, now() received from ' . DB_PREFIX . 'subscriptions_active')) {
		$stmt->execute();
		$stmt->close();
		
		//re-subscribe to active_subscribers
		if($stmt = GetDb()->prepare('SELECT phone_from FROM ' . DB_PREFIX . 'subscriptions_active')) {
			$stmt->execute();
			$stmt->bind_result($phone_from);
			//sleep(2);
			while($stmt->fetch()) {
				GetGClient()->addTaskBackground(WASPY_GMAN . '_PresenceSubscribe', $phone_from);
			}
			GetGClient()->runTasks();
			$stmt->close();
		}
	} else {
		doOutput(GetDb()->error);
	}	
	return true;
});
$GWorker->addFunction(WASPY_GMAN . '_onDisconnect', function(GearmanJob $job) {
	if ($stmt = GetDb()->prepare('insert into ' . DB_PREFIX . 'presence (phone_rcpt, phone_from, status, received) select phone_rcpt, phone_from, \'stop\' status, now() received from ' . DB_PREFIX . 'subscriptions_active')) {
		$stmt->execute();
		$stmt->close();
	} else {
		doOutput(GetDb()->error);
	}
	return true;
});
while ($GWorker->work());
?>