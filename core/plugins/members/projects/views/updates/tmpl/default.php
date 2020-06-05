<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_PROJECTS'); ?></h3>

<ul id="page_options" class="pluginOptions">
	<li>
		<a class="icon-add add btn showinbox"  href="<?php echo Route::url('index.php?option=com_projects&task=start'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_ADD'); ?>
		</a>
	</li>
</ul>

<ul class="sub-menu">
	<li>
		<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->uid . '&active=projects&action=all'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_LIST') . ' (' . $this->projectcount . ')'; ?>
		</a>
	</li>
	<li class="active">
		<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->uid . '&active=projects&action=updates'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED'); ?> <?php if ($this->newcount) { echo '<span class="s-new">' . $this->newcount . '</span>'; } ?>
		</a>
	</li>
</ul>

<div id="s-projects">
	<div id="project-updates">
		<?php
		echo $this->content;
		?>
	</div>
</div>
