<?php
require_once('../config.inc.php');
$client= new GearmanClient();
$client->addServer();

while(true) {
	echo $client->do(WASPY_GMAN . '_PollMessages', 1);
	echo $client->do(WASPY_GMAN . '_SendPong', 1);
	sleep(KEEP_ALIVE);
}
?>
