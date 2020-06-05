<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load base styles
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/component.css?v=' . filemtime(__DIR__ . '/css/component.css'));

// Load theme
$theme = $this->params->get('theme');
if ($theme == 'custom')
{
	$color = $this->params->get('color');
	$this->addStyleDeclaration(include_once __DIR__ . '/css/themes/custom.php');
}

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/component.js?v=' . filemtime(__DIR__ . '/js/component.js'));

$browser = new \Hubzero\Browser\Detector();

$cls = array(
	'nojs',
	$this->direction,
	$theme,
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo implode(' ', $cls); ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<jdoc:include type="head" />
	</head>
	<body id="component-body" class="contentpane">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>