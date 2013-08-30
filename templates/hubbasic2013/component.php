<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$this->template = 'hubbasic2013';

$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('reset', 'fontcons', 'columns', 'notifications', 'tabs')); /* reset MUST come before all others except fontcons */ ?>" />

<?php if ($this->direction == 'rtl' && (!file_exists(JPATH_THEMES . DS . $this->template . DS . 'css/component_rtl.css') || !file_exists(JPATH_THEMES . DS . $this->template . DS . 'css/component.css'))) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template_rtl.css" type="text/css" />
<?php elseif ($this->direction == 'rtl' ) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/component.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/component_rtl.css" type="text/css" />
<?php elseif ($this->direction == 'ltr' && !file_exists(JPATH_THEMES . DS . $this->template . DS . 'css/component.css')) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template.css" type="text/css" />
<?php elseif ($this->direction == 'ltr' ) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/component.css" type="text/css" />
<?php endif; ?>

		<jdoc:include type="head" />

		<!--[if IE 9]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/browser/ie9.css" />
		<![endif]-->
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie8.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/browser/ie7.css" />
		<![endif]-->
	</head>
	<body class="contentpane" id="component-body">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>