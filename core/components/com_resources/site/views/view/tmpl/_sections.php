<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

foreach ($this->sections as $section)
{
	if (!$section['html'])
	{
		continue;
	}

	$cls = '';
	if ($section['area'] == $this->active)
	{
		$cls .= ' active';
	}
	?>
	<div class="main section<?php echo $cls; ?>" id="<?php echo $section['area']; ?>-section">
		<?php echo $section['html']; ?>
	</div>
	<?php
}
