#!/bin/sh
CONNECT_PIDFILE="./.pid/worker_connect.pid"
if [ -e "$CONNECT_PIDFILE" ]
then
    CONNECT_PID=`cat $CONNECT_PIDFILE`
    CONNECT_CLOSE=`php -r "require_once('../config.inc.php'); echo sprintf('WASPY_%s_Connect_Close', WASPY_ENV);"`
    if ps -p $CONNECT_PID > /dev/null
    then
        echo "$CONNECT_PID is running"
        gearman -f $CONNECT_CLOSE 1
    fi
fi

kill `cat ./.pid/worker_connect.pid`
kill `cat ./.pid/client_keepalive.pid`
kill `cat ./.pid/worker_events.pid`
kill `cat ./.pid/worker_pushover.pid`
rm ./.pid/*.pid
