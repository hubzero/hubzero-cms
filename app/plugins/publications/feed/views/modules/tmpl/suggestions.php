<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

shuffle($this->suggestions);
$i= 0;

?>
<div class="sidebox suggestions">
	<h4><?php echo Lang::txt('COM_PROJECTS_SUGGESTIONS'); ?>:</h4>
	<?php
	foreach ($this->suggestions as $suggestion)
	{
		$i++;
		if ($i <= 3)
		{
			?>
			<p class="<?php echo $suggestion['class']; ?>">
				<a href="<?php echo $suggestion['url']; ?>"><?php echo $suggestion['text']; ?></a>
			</p>
			<?php
		}
	}
	?>
</div>