<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

$config = JFactory::getConfig();

//do we want to include jQuery
if (JPluginHelper::isEnabled('system', 'jquery')) 
{
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.jquery.js');
} 
else 
{
	$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');
}

$template = 'hubbasic2013';
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'comments', 'voting', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/main.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/group.css" type="text/css" />

		<jdoc:include type="head" />

		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/ie8win.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/ie7win.css" />
		<![endif]-->
	</head>
	<body class="contentpane" id="group-body">
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<div id="special-bar">
			<a href="<?php echo $this->baseurl; ?>/" id="powered">powered by <span><?php echo $config->getValue('sitename'); ?></span></a>
			<p><a href="<?php echo $this->baseurl; ?>/about">Learn more about <?php echo $config->getValue('sitename'); ?></a></p>
		</div>

		<jdoc:include type="message" />
		<jdoc:include type="component" />
		<jdoc:include type="modules" name="endpage" />
	</body>
</html>