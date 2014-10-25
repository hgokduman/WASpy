#!/bin/sh
rm nohup.out
nohup php gm.worker.spy.php & > /dev/null
nohup php gm.worker.courier.php & > /dev/null
nohup php gm.worker.presence.php & > /dev/null
nohup php gm.worker.messages.php & > /dev/null
nohup php gm.client.poller.php & > /dev/null