<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'libraries' . DS . 'Hubzero' . DS . 'Browser.php');

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
		<jdoc:include type="head" />

		<link href="../media/system/css/fontcons.css" rel="stylesheet" type="text/css" />
		<link href="template/css/template.css" rel="stylesheet" type="text/css" />
		<?php if ($this->direction == 'rtl') : ?>
		<link href="template/css/template_rtl.css" rel="stylesheet" type="text/css" />
		<?php endif; ?>

		<script type="text/javascript" src="../media/system/js/mootools.js"></script>
		<script type="text/javascript" src="includes/js/installation.js"></script>
		<script type="text/javascript" src="template/js/validation.js"></script>

		<script type="text/javascript">
			window.onDomReady(function(){ new Accordion($$('h3.moofx-toggler'), $$('div.moofx-slider'), {onActive: function(toggler, i) { toggler.addClass('moofx-toggler-down'); },onBackground: function(toggler, i) { toggler.removeClass('moofx-toggler-down'); },duration: 300,opacity: false, alwaysHide:true, show: 1}); });
		</script>
	</head>
	<body>
		<div id="wrap">
			<div id="header">
				<h1>
					<span><?php echo JText::_('Installation') ?></span>
				</h1>
				<span id="version">1.2</span>
			</div>
			<div id="content-box">
				<div id="content-pad">
					<jdoc:include type="installation" />
				</div>
			</div>
		</div>
		<div id="copyright">
			<a href="http://hubzer.org" rel="external">HUBzero&reg;</a>
			<?php echo JText::_('ISFREESOFTWARE') ?>
		</div>
	</body>
</html>
