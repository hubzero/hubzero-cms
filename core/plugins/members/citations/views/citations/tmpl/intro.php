<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('citations.css')
	 ->js();

$base = 'index.php?option=com_members&id=' . $this->member->get('id') . '&active=citations';

if (isset($this->messages))
{
	foreach ($this->messages as $message)
	{
		echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
	}
}
?>

<div id="content-header-extra"><!-- Citation management buttons -->
	<?php if ($this->isAdmin) : ?>
		<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		</a>
	<?php endif; ?>
</div><!-- / Citations management buttons -->

<div id="intro-container">
<div id="citations-introduction">
	<div class="instructions">
	<h2 id="instructions-title"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS'); ?></h2>
	<p id="noCitations"> <?php echo Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
	<?php if ($this->isAdmin): ?>
	<p id="who"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_MEMBER_MAY'); ?></p>
	<ul>
		<li>
			<div class="instruction">
			<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<span class="description"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_MANUALLY_ENTER'); ?></span>
		</div>
	</li>
 		<li>
		<div class="instruction">
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<span class="description">  Import a list of citations.</span>
		</div>
		</li>
		<li>
		<span class="or">or</span>
		</li>
 		<li>
		<div class="instruction">
			<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		 </a>
		<span class="description"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DESCRIPTION'); ?></span>
		</div>
			</li>
 </ul>
 <?php endif; ?>
</div><!-- / .instructions -->
	<div class="questions">
	<p><strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS'); ?></strong></p>
	<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS_EXPLANATION'); ?></p>
	</div>
	</div>

</div> <!-- /#intro-container --> 
