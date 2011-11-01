<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
	<head>
		<jdoc:include type="head" />

		<link rel="stylesheet" type="text/css" href="templates/system/css/system.css" />
		<link rel="stylesheet" type="text/css" href="templates/<?php echo  $this->template ?>/css/template.css" />
		
		<script type="text/javascript" src="templates/<?php echo  $this->template ?>/js/index.js"></script>
		
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" href="templates/<?php echo  $this->template ?>/css/ie7.css" />
			<script type="text/javascript" src="templates/<?php echo  $this->template ?>/js/stripe.js"></script>
		<![endif]-->
		<!--[if lte IE 6]>
			<link rel="stylesheet" type="text/css" href="templates/<?php echo  $this->template ?>/css/ie6.css" />
		<![endif]-->
	</head>
	<body>
		<div id="masthead">
			<h1><?php echo $mainframe->getCfg('sitename'); ?></h1>
			<p class="version"><?php echo  JText::_('Version') ?> <?php echo JVERSION; ?></p>
			<div id="module-status">
				<jdoc:include type="modules" name="status"  />
			</div><!-- / #module-status -->
			<div class="clr"></div>
		</div><!-- / #masthead -->
		<div id="navigation">
			<div id="module-menu">
				<jdoc:include type="modules" name="menu" />
			</div>
			<div class="clr"></div>
		</div><!-- / #navigation -->
<?php if (!JRequest::getInt('hidemainmenu')): ?>
		<jdoc:include type="modules" name="submenu" style="rounded" id="submenu-box" />
		<div class="clr"></div>
<?php endif; ?>
		<div id="wrap">
			<div id="content-wrap">
			<div id="toolbar-box">
				<jdoc:include type="modules" name="toolbar" />
				<jdoc:include type="modules" name="title" />
				<div class="clr"></div>
  			</div><!-- / #toolbar-box -->
			<div class="clr"></div>
			<div id="content-box">
				<jdoc:include type="message" />
				<div id="element-box">
					<jdoc:include type="component" />
					<div class="clr"></div>
				</div><!-- / #element-box -->
				<noscript>
					<?php echo JText::_('WARNJAVASCRIPT') ?>
				</noscript>
				<div class="clr"></div>
			</div><!-- / #content-box -->
			<div class="clr"></div>
			</div><!-- / #content-wrap -->
		</div><!-- / #wrap -->
		<div class="clr"></div>
		<div id="footer">
			<p class="copyright">
				<a href="http://hubzero.org" rel="external">HUBzero&reg;</a>
			</p>
		</div><!-- / #footer -->
	</body>
</html>