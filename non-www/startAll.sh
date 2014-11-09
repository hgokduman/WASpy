#!/bin/sh
./stopAll.sh
nohup php gm.worker.connect.php > /dev/null 2>&1 & echo $! > ./.pid/worker_connect.pid
nohup php gm.worker.events.php > /dev/null 2>&1 & echo $! > ./.pid/worker_events.pid
nohup php gm.client.keepalive.php > /dev/null 2>&1 & echo $! > ./.pid/client_keepalive.pid
