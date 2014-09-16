#!/bin/bash
#<?php exit('no access'); ?>

# Run me through bash to start a MySQL client without having to punch in the information from configuration.php

conf="`dirname $0`/../configuration.php"
get_var () { grep "var\ \$$1 " $conf | awk '{ print $4 }' | sed "s/^'\|[';]\+$//g"; }

mkfifo -m 600 $HOME/.my.cred
(echo "[client]"; echo -n "password=\""; get_var "password" | sed "s/$/\"/"; ) > $HOME/.my.cred &
mysql --defaults-extra-file="$HOME/.my.cred" -u `get_var 'user'` `get_var 'db'`
rm $HOME/.my.cred

