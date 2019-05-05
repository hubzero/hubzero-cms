<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$directory = $this->directory;
?>
<li>
	<span class="item-icon">
		<span class="item-extension _<?php echo Filesystem::extension($directory['name']) == 'zip' ? 'zip' : 'dir'; ?>"></span>
	</span>
	<span class="item-title"><?php echo $this->escape($directory['name']); ?></span>
	<?php if (isset($directory['contents']) && $directory['contents']): ?>
		<ul>
			<?php
				$this->view('_bundle_contents')
					->set('contents', $directory['contents'])
					->display();
			?>
		</ul>
	<?php endif; ?>
</li>
