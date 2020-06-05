<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
	<ul class="module-nav">
		<li><a class="icon-browse" href="<?php echo Route::url('index.php?option=com_publications&controller=curation'); ?>"><?php echo Lang::txt('MOD_MYCURATION_ALL_TASKS'); ?></a></li>
	</ul>

	<h4>
		<a href="<?php echo Route::url('index.php?option=com_publications&controller=curation&assigned=1'); ?>">
			<?php echo Lang::txt('MOD_MYCURATION_ASSIGNED'); ?>
			<span><?php echo Lang::txt('MOD_MYCURATION_VIEW_ALL'); ?></span>
		</a>
	</h4>
	<?php if (count($this->rows) <= 0) { ?>
		<p><em><?php echo Lang::txt('MOD_MYCURATION_NO_ITEMS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
		<?php
		foreach ($this->rows as $row)
		{
			$class = $row->state == 5 ? 'status-pending' : 'status-wip';
			?>
			<li class="curation-task <?php echo $class; ?>">
				<a href="<?php echo $row->state == 5 ? Route::url('index.php?option=com_publications&controller=curation&id=' . $row->id) : Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number); ?>"><img src="<?php echo Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb'; ?>" alt="" />
				<?php echo $row->title . ' v.' . $row->version_label; ?></a>
				<span><?php if ($row->state == 5) { ?><a href="<?php echo Route::url('index.php?option=com_publications&controller=curation&id=' . $row->id); ?>"><?php echo Lang::txt('MOD_MYCURATION_REVIEW'); ?></a><?php } ?><?php if ($row->state == 7) { echo Lang::txt('MOD_MYCURATION_PENDING_CHANGES');  } ?></span>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } ?>
</div>