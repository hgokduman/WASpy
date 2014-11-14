<?php
require_once('../config.inc.php');
require_once(WASPY_DIR . '/libs/functions.inc.php');
require_once(WASPY_DIR . '/libs/PushOver.class.php');

$GWorker= new GearmanWorker();
$GWorker->addServer();
$GWorker->setId(WASPY_GMAN . '_PushOver');

$GClient= new GearmanClient();
$GClient->addServer();


$Db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($Db->connect_errno) {
	doOutput("Failed to connect to MySQL: (" . $Db->connect_errno . ") " . $Db->connect_error);
}


$Push = new PushOver(PUSHOVER_API);

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

$GWorker->addFunction(WASPY_GMAN . '_sendPushMessage', function(GearmanJob $job) {
	echo 'push event' . PHP_EOL;
	GetPush()->sendMessage($job, true, false);
});
while ($GWorker->work());
?>