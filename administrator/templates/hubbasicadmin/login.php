<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
	<head>
		<jdoc:include type="head" />

		<link href="templates/<?php echo $this->template; ?>/css/login.css" rel="stylesheet" type="text/css" media="screen" />

		<script type="text/javascript">
		window.onload = function() {
			document.login.username.select();
			document.login.username.focus();
		}
		</script>
	</head>
	<body>
		<div id="content-box">
			<div id="element-box">
				<h1><?php echo JText::_('Administration Login'); ?></h1>
				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<div class="clr"></div>
			</div><!-- / #element-box -->
			<noscript>
				<?php echo JText::_('WARNJAVASCRIPT'); ?>
			</noscript>
			<div class="clr"></div>
		</div><!-- / #content-box -->
	</body>
</html>