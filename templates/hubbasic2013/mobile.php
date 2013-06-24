<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

//import need HUBzero libraries
ximport('Hubzero_Document');
ximport('Hubzero_Device');

$config =& JFactory::getConfig();

//define tempate
$this->template = 'hubbasic2012';

//get device info
$hd = new Hubzero_Device();
?>
<!DOCTYPE html>
<html class="<?php echo strtolower($hd->getDeviceFamily() . ' ' . $hd->getDeviceOS() . ' ' . $hd->getDeviceOSVersion()); ?>">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('reset', 'fontcons', 'columns', 'notifications')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/mobile.css" />
		<script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/mobile.js"></script>
	</head>
	<body>
		<div id="mobile-header">
			<h1>
				<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>">
					<span><?php echo $config->getValue('config.sitename'); ?></span>
					<span class="tagline"><?php echo JText::_('TPL_HUBBASIC_TAGLINE'); ?></span>
				</a>
			</h1>
			
			<div id="true-menu">
				<jdoc:include type="modules" name="user3" />
			</div>
			<select name="menu" id="menu">
			</select>
		</div>
		<jdoc:include type="message" />
		<jdoc:include type="component" />
		
		<div id="mobile-footer">
			<a href="?tmpl=fullsite">View Full Site</a>
		</div>
	</body>
</html>
	