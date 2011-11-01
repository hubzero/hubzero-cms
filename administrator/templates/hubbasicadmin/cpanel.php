<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
	<head>
		<jdoc:include type="head" />

		<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
		<link href="templates/<?php echo $this->template; ?>/css/template.css" rel="stylesheet" type="text/css" />

		<!--[if IE 7]>
			<link href="templates/<?php echo $this->template;?>/css/ie7.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<!--[if lte IE 6]>
			<link href="templates/<?php echo $this->template; ?>/css/ie6.css" rel="stylesheet" type="text/css" />
		<![endif]-->

<?php if (JModuleHelper::isEnabled('menu')) : ?>
		<script type="text/javascript" src="templates/<?php echo $this->template; ?>/js/index.js"></script>
<?php endif; ?>
	</head>
	<body id="cpanel">
		<div id="masthead">
			<h1><?php echo $mainframe->getCfg('sitename'); ?></h1>
			<p class="version"><?php echo  JText::_('Version') ?> <?php echo JVERSION; ?></p>
			<div id="module-status">
				<jdoc:include type="modules" name="status"  />
			</div>
			<div class="clr"></div>
		</div><!-- / #masthead -->
		<div id="navigation">
			<div id="module-menu">
				<jdoc:include type="modules" name="menu" />
			</div>
			<div class="clr"></div>
		</div><!-- / #navigation -->
		<div id="content-box">
				<jdoc:include type="message" />
			<div class="col width-50">
				<jdoc:include type="modules" name="icon" style="box" />
			</div>
			<div class="col width-50">
				<jdoc:include type="modules" name="cpanel" style="box" />
			</div>
				<div class="clr"></div>
			<noscript>
				<?php echo JText::_('WARNJAVASCRIPT'); ?>
			</noscript>
		</div><!-- / #content-box -->
		<div class="clr"></div>
		<div id="footer">
			<p class="copyright">
				<a href="http://hubzero.org" rel="external">HUBzero&reg;</a>
			</p>
		</div><!-- / #footer -->
	</body>
</html>