<?php
/**
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once(JPATH_ROOT . DS . 'libraries' . DS . 'Hubzero' . DS . 'Browser.php');

$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

$doc = JFactory::getDocument();

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Add Stylesheets
$doc->addStyleSheet('../media/system/css/system.css');
$doc->addStyleSheet('template/css/template.css');

if ($this->direction == 'rtl') {
	$doc->addStyleSheet('template/css/template_rtl.css');
}

// Load the JavaScript behaviors
JHtml::_('behavior.framework', true);
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('script', 'installation/template/js/installation.js', true, false, false, false);

// Load the JavaScript translated messages
JText::script('INSTL_PROCESS_BUSY');
JText::script('INSTL_SITE_SAMPLE_LOADED');
JText::script('INSTL_FTP_SETTINGS_CORRECT');
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<jdoc:include type="head" />

		<link href="../media/system/css/fontcons.css" rel="stylesheet" type="text/css" />
		<link href="../media/system/css/notifications.css" rel="stylesheet" type="text/css" />
		<!--[if IE 7]>
			<link href="template/css/ie7.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript">
			window.addEvent('domready', function() {
				window.Install = new Installation('rightpad', '<?php echo JURI::current(); ?>');
			});
 		</script>
	</head>
	<body>
		<div id="header">
			<span class="logo"><a href="http://hubzero.org" target="_blank"><img src="template/images/hub.png" alt="HUBzero" /></a></span>
			<h1>HUBzero <?php echo HVERSION; ?> <?php echo JText::_('INSTL_INSTALLATION') ?></h1>
		</div>
		<jdoc:include type="message" />
		<div id="content-box">
			<div id="content-pad">
				<div id="stepbar">
					<div class="container">
						<?php echo JHtml::_('installation.stepbar'); ?>
					</div>
					<div class="box"></div>
				</div>
				<div id="warning">
					<noscript>
						<div id="javascript-warning">
							<?php echo JText::_('INSTL_WARNJAVASCRIPT'); ?>
						</div>
					</noscript>
				</div>
				<div id="right">
					<div id="rightpad">
						<jdoc:include type="installation" />
					</div>
				</div>
				<div class="clr"></div>
			</div>
		</div>
		<div id="copyright">
			<?php $hubzero= '<a href="http://hubzero.org">HUBzero&#174;</a>';
			echo JText::sprintf('JGLOBAL_ISFREESOFTWARE', $hubzero) ?>
		</div>
	</body>
</html>
