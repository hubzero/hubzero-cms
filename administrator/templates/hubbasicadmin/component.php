<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
	<head>
		<jdoc:include type="head" />

		<link href="templates/<?php echo  $this->template ?>/css/general.css" rel="stylesheet" type="text/css" />
		<link href="templates/<?php echo  $this->template ?>/css/component.css" rel="stylesheet" type="text/css" />
		
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" href="templates/<?php echo  $this->template ?>/css/ie7.css" />
			<script type="text/javascript" src="templates/<?php echo  $this->template ?>/js/stripe.js"></script>
		<![endif]-->
		<!--[if lte IE 6]>
			<link rel="stylesheet" type="text/css" href="templates/<?php echo  $this->template ?>/css/ie6.css" />
		<![endif]-->
	</head>
	<body class="component">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>