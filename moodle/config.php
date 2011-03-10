<?php  /// Moodle Configuration File 

unset($CFG);

$CFG = new stdClass();
$CFG->dbtype    = 'mysql';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodleuser';
$CFG->dbpass    = '83%85bUi@EOT';
$CFG->dbpersist =  false;
$CFG->prefix    = 'mdl_';

$CFG->wwwroot   = 'https://stage.neeshub.org/moodle';
$CFG->dirroot   = '/www/neeshub/moodle';
$CFG->dataroot  = '/www/neeshub_p/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

$CFG->passwordsaltmain = '04BXWK5GhBB<%^AHI%.yt)sCz0C';

require_once("$CFG->dirroot/lib/setup.php");
// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
?>
