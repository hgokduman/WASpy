<?php
function Ts2Date($ts) {
	return date('Y-m-d H:i:s', $ts);
}

function humanDate($timestamp) {
	return date('Y-m-d \@ H:i:s', $timestamp);
}

function doOutput($message) {
	$txt = humandate(time()) . ': ' . $message . PHP_EOL;
	echo $txt;
	return $txt;
}

function jid2phone($jid) {
	return substr($jid, 0, strpos($jid, '@'));;
}
?>