#!/bin/sh
./stopAll.sh
nohup php gm.worker.pushover.php > ./.nohup/worker_pushover.out 2>&1 & echo $! > ./.pid/worker_pushover.pid
nohup php gm.worker.events.php > ./.nohup/worker_events.out 2>&1 & echo $! > ./.pid/worker_events.pid
nohup php gm.client.keepalive.php > ./.nohup/client_keepalive.out 2>&1 & echo $! > ./.pid/client_keepalive.pid
nohup php gm.worker.connect.php > ./.nohup/worker_connect.out 2>&1 & echo $! > ./.pid/worker_connect.pid
