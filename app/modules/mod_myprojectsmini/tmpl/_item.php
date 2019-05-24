<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<li <?php echo $class; ?>>
	<div class="prjctdisp">
		<a class="project-img" href="<?php echo Route::url('index.php?option=com_projects&task=view&' . $goto); ?>" title="<?php echo $this->escape($row->title) . ' (' . $row->alias . ')'; ?>">
			<img src="<?php echo Route::url('index.php?option=com_projects&alias=' . $row->alias . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->escape($row->title); ?>" />
		</a>
		<div class="project-name">
		<a href="<?php echo Route::url('index.php?option=com_projects&task=view&' . $goto); ?>" title="<?php echo $this->escape($row->title) . ' (' . $row->alias . ')'; ?>"><?php echo \Hubzero\Utility\Str::truncate($this->escape($row->title), 30); ?></a>
	</div>
	</div>
	<span class="sub">
		<?php if ($row->newactivity && $row->state == 1 && !$setup) { ?>
			<span class="s-new"><?php echo $row->newactivity; ?></span>
		<?php } ?>
	</span>
</li>
