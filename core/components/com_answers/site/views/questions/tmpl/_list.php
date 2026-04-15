<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<?php if (isset($this->comments) && count($this->comments)) : ?>
<ol class="comments" id="<?php echo (isset($this->thread) ? $this->thread : 't') . (isset($this->parent) ? $this->parent : '0'); ?>">
	<?php
		$cls = 'odd';
		if (isset($this->cls))
		{
			$hasComments = true;
			break;
		}
	?>
</ol>
<?php endif; ?>