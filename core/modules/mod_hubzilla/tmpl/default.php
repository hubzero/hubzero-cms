<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$this->css('
#hubzilla {
	top: ' . $this->params->get('posTop', 'auto') . ';
	right: ' . $this->params->get('posRight', '5px') . ';
	bottom: ' . $this->params->get('posBottom', '5px') . ';
	left: ' . $this->params->get('posLeft', 'auto') . ';
}
');

$reveal = strtolower(Request::getWord('reveal', ''));

$base = rtrim(Request::base(true), '/');
?>
<div id="hubzilla"<?php if ($reveal == 'eastereggs') { echo ' class="revealed"'; } ?>>
	<audio preload="auto" id="hubzilla-roar">
		<source src="<?php echo $base; ?>/core/modules/mod_hubzilla/assets/sounds/roar.ogg" type="audio/ogg" />
		<source src="<?php echo $base; ?>/core/modules/mod_hubzilla/assets/sounds/roar.mp3" type="audio/mp3" />
	</audio>
</div>