<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="container" id="whatsrelated">
	<h3><?php echo Lang::txt('PLG_RESOURCES_RELATED_HEADER'); ?></h3>

	<?php if ($this->related) { ?>
		<ul>
		<?php foreach ($this->related as $line) { ?>
			<?php
			if ($line->section != 'Topic')
			{
				// Get the SEF for the resource
				if ($line->alias)
				{
					$sef = Route::url('index.php?option=' . $this->option . '&alias=' . $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=' . $this->option . '&id=' . $line->id);
				}
				$class = 'series';
			}
			else
			{
				if ($line->scope == 'group' && !!$line->scope_id)
				{
					$query = "SELECT cn from `#__xgroups` where gidNumber =	$line->scope_id";
					$groupAlias =	App::get('db')->setQuery($query)->loadObjectList()[0]->cn;
					$sef = Route::url("/groups/$groupAlias/wiki/$line->alias");
				}
				else
				{
					$sef = Route::url('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				$class = 'wiki';
			}
			?>
			<li class="<?php echo $class; ?>">
				<a href="<?php echo $sef; ?>">
					<?php echo ($line->section == 'Series') ? '<span>' . Lang::txt('PLG_RESOURCES_RELATED_PART_OF') . '</span> ' : ''; ?>
					<?php echo $this->escape(stripslashes($line->title)); ?>
				</a>
			</li>
		<?php } ?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND'); ?></p>
	<?php } ?>
</div>
