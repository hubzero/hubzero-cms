#<?php exit('no access'); ?>
#!/bin/bash
conf="`dirname $0`/configuration.php"
get_var () { grep "var\ \$$1 " $conf | awk '{ print $4 }' | sed "s/^'\|[';]\+$//g"; }

umask 077
mkfifo $HOME/.my.cred
(echo "[client]"; echo -n "password="; get_var "password") > $HOME/.my.cred &
mysql --defaults-extra-file="$HOME/.my.cred" -u `get_var 'user'` `get_var 'db'`
rm $HOME/.my.cred

