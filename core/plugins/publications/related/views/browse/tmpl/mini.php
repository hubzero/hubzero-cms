<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Add stylesheet
$this->css('assets/css/related.css');

?>
<div id="whatsrelated">
	<h3><?php echo Lang::txt('PLG_PUBLICATION_RELATED_HEADER'); ?></h3>
<?php if ($this->related) { ?>
	<ul>
<?php
	foreach ($this->related as $line)
	{
		if ($line->section != 'Topic') {
			// Get the SEF for the resource
			if ($line->alias) {
				$sef = Route::url('index.php?option=' . $this->option . '&alias='. $line->alias);
			} else {
				$sef = Route::url('index.php?option=' . $this->option . '&id='. $line->id);
			}
		} else {
			if ($line->group != '' && $line->scope != '') {
				$sef = Route::url('index.php?option=com_groups&scope=' . $line->scope . '&pagename=' . $line->alias);
			} else {
				$sef = Route::url('index.php?option=com_topics&scope=' . $line->scope . '&pagename=' . $line->alias);
			}
		}
?>
		<li class="<?php echo $line->class; ?>">
			<a href="<?php echo $sef; ?>"><?php echo ($line->section == 'Series') ? Lang::txt('PLG_PUBLICATION_RELATED_PART_OF').' ' : ''; ?><?php echo stripslashes($line->title); ?></a>
		</li>
<?php } ?>
	</ul>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_PUBLICATION_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
</div>