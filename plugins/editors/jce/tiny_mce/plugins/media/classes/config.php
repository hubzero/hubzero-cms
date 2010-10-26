<?php
/**
* @version		$Id: config.php 138 2009-06-26 10:56:43Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class MediaConfig 
{
	function getConfig(&$vars)
	{
		$jce 	=& JContentEditor::getInstance();
		$params = $jce->getPluginParams('media');
		
		if ($params->get('media_use_script', '0') == '1') {
			$vars['media_use_script']	= '1';
			$vars['code_javascript'] 	= '1';
			$jce->removeKeys($vars['invalid_elements'], array('script'));
		} else {
			$jce->removeKeys($vars['invalid_elements'], array('object', 'param', 'embed'));
		}
		if ($params->get('media_html5', 0) == 1) {
			$vars['media_html5'] = 1;
		} else {
			$jce->addKeys($vars['invalid_elements'], array('video', 'audio'));
		}
		$vars['media_strict']					= $jce->getParam($params, 'media_strict', '0', '1');
		$vars['media_version_flash'] 			= $jce->getParam($params, 'media_version_flash', '10,0,32,18', '10,0,32,18');
		$vars['media_version_shockwave'] 		= $jce->getParam($params, 'media_version_shockwave', '11,0,0,458', '11,0,0,458');
		$vars['media_version_windowsmedia'] 	= $jce->getParam($params, 'media_version_windowsmedia', '5,1,52,701', '5,1,52,701');
		$vars['media_version_quicktime'] 		= $jce->getParam($params, 'media_version_quicktime', '6,0,2,0', '6,0,2,0');
		$vars['media_version_reallpayer'] 		= $jce->getParam($params, 'media_version_reallpayer', '7,0,0,0', '7,0,0,0');
	}
}
?>