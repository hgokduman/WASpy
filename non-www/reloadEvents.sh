#!/bin/sh
kill `cat ./.pid/worker_events.pid`
nohup php gm.worker.events.php > /dev/null 2>&1 & echo $! > ./.pid/worker_events.pid
