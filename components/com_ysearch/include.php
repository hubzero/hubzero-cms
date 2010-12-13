<?php

foreach (array('plugin', 'request', 'result_set', 'result_types', 'terms', 'authorization') as $mdl)
	require_once dirname(__FILE__).'/models/'.$mdl.'.php';

JPluginHelper::importPlugin('ysearch');

