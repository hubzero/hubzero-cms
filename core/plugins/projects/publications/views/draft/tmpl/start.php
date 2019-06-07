<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="plg-header">
<?php if ($this->project->isProvisioned()) { ?>
<h3 class="prov-header"><a href="<?php echo Route::url($this->route); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')); ?></h3>
<?php } else { ?>
<h3 class="publications c-header"><a href="<?php echo Route::url($this->route); ?>"><?php echo $this->title; ?></a> &raquo; <span class="indlist"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')); ?></span></h3>
<?php } ?>
</div>
<?php if ($this->project->isProvisioned()) { ?>
<div class="grid">
	<div class="col span9">
<?php } ?>
<div class="welcome">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEWPUB_WHAT'); ?></h3>
	<div id="suggestions" class="suggestions">
		<?php for ($i = 0; $i < count($this->choices); $i++)
		{
			$current = $this->choices[$i];
			$action = 'publication';

		?>
		<div class="s-<?php echo $current->alias; ?>"><p><a href="<?php echo Route::url($this->route . '&action=' . $action . '&base=' . $current->alias); ?>"><?php echo $current->type; ?> <span class="block"><?php echo $current->description; ?></span></a></p></div>
		<?php } ?>
		<div class="clear"></div>
	</div>
</div>
<?php if ($this->project->isProvisioned()) { ?>
	</div><!-- / .subject -->
	<div class="col span3 omega">
		<div id="start-projectnote">
			<h4><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_PROJECT'); ?></h4>
			<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTRIB_START'); ?></p>
			<p class="getstarted-links"><a href="/members/myaccount/projects"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_YOUR_PROJECTS'); ?></a> | <a href="/projects/start" class="addnew"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PROJECT'); ?></a></p>
		</div>
	</div><!-- / .aside -->
</div>
<?php }
