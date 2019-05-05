<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="xflash-container">
	<!-- Image slideshow -->
<?php if ($this->noflash_link) { ?>
	<a href="<?php echo $this->noflash_link; ?>">
<?php } ?>
		<img src="<?php echo $this->noflash_file; ?>" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" id="noflashimg" alt="" />
<?php if ($this->noflash_link) { ?>
	</a>
<?php } ?>
</div>
