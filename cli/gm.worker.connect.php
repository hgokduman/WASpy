<?php
require_once('../config.inc.php');
require_once(WASPY_DIR . '/libs/functions.inc.php');
require_once(WASPY_DIR . '/libs/WhatsAPIPlus.class.php');
require_once(WASPY_DIR . '/libs/WhatsAPIPlusEventListener.class.php');

$GWorker= new GearmanWorker();
$GWorker->addServer();
$GWorker->setId(WASPY_GMAN . '_Connect_' . uniqid(true));

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

while ($GWorker->work());
?>