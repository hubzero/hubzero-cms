<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

ini_set('display_errors', 1);
require_once JPATH_ROOT.'/components/com_ysearch/include.php';

JPluginHelper::importPlugin('ysearch');

$context = array();
if (array_key_exists('ysearch-task', $_POST))
	foreach (JApplication::triggerEvent('onYSearchTask'.$_POST['ysearch-task']) as $resp)
	{
		list($name, $html, $ctx) = $resp;
		echo $html;
		if (array_key_exists($name, $context))
			$context[$name] = array_merge($context[$name], $ctx);
		else
			$context[$name] = $ctx;
	}

foreach (JApplication::triggerEvent('onYSearchAdministrate', array($context)) as $plugin)
{
	list($name, $html) = $plugin;
	echo '<h2>'.$name.'</h2>';
	echo $html;
}
