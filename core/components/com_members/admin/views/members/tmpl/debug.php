<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load the tooltip behavior.
Html::behavior('tooltip');

Toolbar::title(Lang::txt('COM_MEMBERS_VIEW_DEBUG_USER_TITLE', $this->user->get('id'), $this->user->get('name')), 'user');
Toolbar::help('JHELP_USERS_DEBUG_USERS');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=debug&id=' . (int) $this->user->get('id')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="filter-search col span5">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_ASSETS'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_USERS'); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
			</div>
			<div class="filter-select col span7">
				<select name="filter_component" class="inputbox filter filter-submit">
					<option value=""><?php echo Lang::txt('COM_MEMBERS_OPTION_SELECT_COMPONENT');?></option>
					<?php
					if (!empty($this->components))
					{
						echo Html::select('options', $this->components, 'value', 'text', $this->filters['component']);
					}
					?>
				</select>

				<select name="filter_level_start" class="inputbox filter filter-submit">
					<option value=""><?php echo Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_START');?></option>
					<?php echo Html::select('options', $this->levels, 'value', 'text', $this->filters['level_start']); ?>
				</select>

				<select name="filter_level_end" class="inputbox filter filter-submit">
					<option value=""><?php echo Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_END');?></option>
					<?php echo Html::select('options', $this->levels, 'value', 'text', $this->filters['level_end']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<caption>
			<?php echo Lang::txt('COM_MEMBERS_DEBUG_LEGEND'); ?>
			<span class="swatch"><?php echo Lang::txt('COM_MEMBERS_DEBUG_NO_CHECK', '-');?></span>
			<span class="check-0 swatch"><?php echo Lang::txt('COM_MEMBERS_DEBUG_IMPLICIT_DENY', '-');?></span>
			<span class="check-a swatch"><?php echo Lang::txt('COM_MEMBERS_DEBUG_EXPLICIT_ALLOW', '&#10003;');?></span>
			<span class="check-d swatch"><?php echo Lang::txt('COM_MEMBERS_DEBUG_EXPLICIT_DENY', '&#10007;');?></span>
		</caption>
		<thead>
			<tr>
				<th>
					<?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_ASSET_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_ASSET_NAME', 'name', $listDirn, $listOrder); ?>
				</th>
				<?php foreach ($this->actions as $key => $action) : ?>
					<th>
						<span class="hasTip" title="<?php echo htmlspecialchars(Lang::txt($key).'::'.Lang::txt($action[1]), ENT_COMPAT, 'UTF-8'); ?>"><?php echo Lang::txt($key); ?></span>
					</th>
				<?php endforeach; ?>
				<th>
					<?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_LFT', 'lft', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->assets->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->assets as $item) : ?>
			<tr class="row0">
				<td>
					<?php echo $this->escape($item->get('title')); ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->get('level')); ?>
					<?php echo $this->escape($item->get('name')); ?>
				</td>
				<?php
				$checks = $item->get('checks');
				foreach ($this->actions as $action) : ?>
					<?php
					$name  = $action[0];
					$check = $checks[$name];
					if ($check === true) :
						$class = 'check-a';
						$text  = '<span class="state yes"><span>&#10003;</span></span>';
					elseif ($check === false) :
						$class = 'check-d';
						$text  = '<span class="state no"><span>&#10007;</span></span>';
					elseif ($check === null) :
						$class = 'check-0';
						$text  = '-';
					else :
						$class = '';
						$text  = '&#160;';
					endif;
					?>
					<td class="center <?php echo $class; ?>">
						<?php echo $text; ?>
					</td>
				<?php endforeach; ?>
				<td class="center">
					<?php echo (int) $item->get('lft'); ?>
					- <?php echo (int) $item->get('rgt'); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->get('id'); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
