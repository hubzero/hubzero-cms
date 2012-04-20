<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$db = & JFactory::getDbo();
$db->setQuery("SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0");
$template = $db->loadResult();
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>"> <!--<![endif]-->
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<?php if ($this->direction == 'rtl' && (!file_exists(JPATH_THEMES . DS . $template . DS . 'css/template_rtl.css') || !file_exists(JPATH_THEMES . DS . $template . DS . 'css/template.css'))) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template_rtl.css" type="text/css" />
<?php elseif ($this->direction == 'rtl' ) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/template.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/template_rtl.css" type="text/css" />
<?php elseif ($this->direction == 'ltr' && !file_exists(JPATH_THEMES . DS . $template . DS . 'css/template.css')) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template.css" type="text/css" />
<?php elseif ($this->direction == 'ltr' ) : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/template.css" type="text/css" />
<?php endif; ?>
		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/ie8win.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/ie7win.css" />
		<![endif]-->
	</head>
	<body class="contentpane" id="component-body">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>