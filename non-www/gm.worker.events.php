<?php
require_once('../config.inc.php');
require_once(WASPY_DIR . '/libs/functions.inc.php');

$GWorker= new GearmanWorker();
$GWorker->addServer();
$GWorker->setId(WASPY_GMAN . '_Events_' . uniqid(true));

$Db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($Db->connect_errno) {
	doOutput("Failed to connect to MySQL: (" . $Db->connect_errno . ") " . $Db->connect_error);
}
function GetDb() {
    global $Db;
    return $Db;
}

$GWorker->addFunction(WASPY_GMAN . '_onPresence', function(GearmanJob $job) {
	$presence = unserialize($job->workload());
	if ($stmt = GetDb()->prepare('INSERT INTO ' . DB_PREFIX . 'presence (phone_rcpt, phone_from, status, received) VALUES (?, ?, ?, ?)')) {
		$stmt->bind_param('ssss', $presence[0], jid2phone($presence[1]), $presence[2], ts2date(time()));
		$stmt->execute();
		$stmt->close();
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
while ($GWorker->work());
?>