#!/bin/sh
kill `cat ./.pid/*.pid`
rm ./.pid/*.pid
