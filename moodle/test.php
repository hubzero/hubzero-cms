<?php

$sessionid = $_COOKIE['site'];

include('/www/neeshub/configuration.php');

$jconfig = new JConfig();

$db = mysql_connect($jconfig->host, $jconfig->user, $jconfig->password);
mysql_select_db($jconfig->db, $db);
$res = mysql_query("SELECT username FROM jos_session WHERE session_id='$sessionid';");
$row=mysql_fetch_object($res); 

var_dump($row);


?>

