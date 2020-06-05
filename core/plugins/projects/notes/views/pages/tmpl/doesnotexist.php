<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes';
$pagename = Request::getString('pagename', '');

?>
<section class="main section">
	<p class="s-notes">	
		<?php if ($this->project->access('content')) { ?>
			<a href="<?php echo Route::url($url . '&action=new'); ?>">
				<?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_NOTE'); ?>
			</a>
		<?php } else { ?>
			<?php echo Lang::txt('This project has no notes.'); ?>
		<?php } ?>
	</p>
</section><!-- / .main section -->
