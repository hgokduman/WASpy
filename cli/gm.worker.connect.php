<?php
require_once('../config.inc.php');
require_once(WASPY_DIR . '/libs/functions.inc.php');
require_once(WASPY_DIR . '/libs/WhatsAPIPlus.class.php');
require_once(WASPY_DIR . '/libs/WhatsAPIPlusEventListener.class.php');

$GWorker= new GearmanWorker();
$GWorker->addServer();
$GWorker->setId(WASPY_GMAN . '_Connect');

$WhatsApp = new WhatsAPIPlus(PHONE_NUMBER, PHONE_IDENT, PHONE_ALIAS, PHONE_PASS, DEBUG_PROTO);
$WhatsApp->eventManager()->addEventListener(new WhatsAPIPlusEventListener());
$WhatsApp->connectAndLogin();
function GetWhatsApp() {
    global $WhatsApp;
    return $WhatsApp;
}

$GWorker->addFunction(WASPY_GMAN . '_RequestLastSeen', function(GearmanJob $job) {
	GetWhatsApp()->sendGetRequestLastSeen($job->workload());
});
$GWorker->addFunction(WASPY_GMAN . '_PresenceSubscribe', function(GearmanJob $job) {
	GetWhatsApp()->sendPresenceSubscription($job->workload());
});
$GWorker->addFunction(WASPY_GMAN . '_PresenceUnsubscribe', function(GearmanJob $job) {
	GetWhatsApp()->sendPresenceUnsubscription($job->workload());
});
$GWorker->addFunction(WASPY_GMAN . '_PollMessages', function(GearmanJob $job) {
	while(GetWhatsApp()->pollMessage());
});
$GWorker->addFunction(WASPY_GMAN . '_SendPong', function(GearmanJob $job) {
	GetWhatsApp()->sendPong(time());
});
$GWorker->addFunction(WASPY_GMAN . '_SendMessage', function(GearmanJob $job) {
	list($rcpt, $txt) = unserialize($job->workload());
	GetWhatsApp()->sendMessage($rcpt, $txt);
});
$GWorker->addFunction(WASPY_GMAN . '_Connect_Close', function(GearmanJob $job) {
	GetWhatsApp()->disconnect();
	return true;
});

while ($GWorker->work());
?>