<?php

/**
 * CSS file server - works around php_server intercepting static files
 */

header('Content-Type: text/css; charset=utf-8');
header('Cache-Control: public, max-age=3600');
readfile(__DIR__ . '/install.css');
