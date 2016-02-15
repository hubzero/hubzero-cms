<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::url('index.php?option=com_users&view=debuggroup&user_id='.(int) $this->state->get('filter.user_id'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_USERS_SEARCH_ASSETS'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo Lang::txt('COM_USERS_SEARCH_USERS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_component" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_USERS_OPTION_SELECT_COMPONENT');?></option>
				<?php if (!empty($this->components)) {
					echo Html::select('options', $this->components, 'value', 'text', $this->state->get('filter.component'));
				}?>
			</select>

			<select name="filter_level_start" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_USERS_OPTION_SELECT_LEVEL_START');?></option>
				<?php echo Html::select('options', $this->levels, 'value', 'text', $this->state->get('filter.level_start'));?>
			</select>

			<select name="filter_level_end" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_USERS_OPTION_SELECT_LEVEL_END');?></option>
				<?php echo Html::select('options', $this->levels, 'value', 'text', $this->state->get('filter.level_end'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<caption>
			<?php echo Lang::txt('COM_USERS_DEBUG_LEGEND'); ?>
			<span class="swatch"><?php echo Lang::txt('COM_USERS_DEBUG_NO_CHECK', '-');?></span>
			<span class="check-0 swatch"><?php echo Lang::txt('COM_USERS_DEBUG_IMPLICIT_DENY', '-');?></span>
			<span class="check-a swatch"><?php echo Lang::txt('COM_USERS_DEBUG_EXPLICIT_ALLOW', '&#10003;');?></span>
			<span class="check-d swatch"><?php echo Lang::txt('COM_USERS_DEBUG_EXPLICIT_DENY', '&#10007;');?></span>
		</caption>
		<thead>
			<tr>
				<th>
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_ASSET_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_ASSET_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<?php foreach ($this->actions as $key => $action) : ?>
					<th>
						<span class="hasTip" title="<?php echo htmlspecialchars(Lang::txt($key).'::'.Lang::txt($action[1]), ENT_COMPAT, 'UTF-8'); ?>"><?php echo Lang::txt($key); ?></span>
					</th>
				<?php endforeach; ?>
				<th>
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_LFT', 'a.lft', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row0">
				<td>
					<?php echo $this->escape($item->title); ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level); ?>
					<?php echo $this->escape($item->name); ?>
				</td>
				<?php foreach ($this->actions as $action) : ?>
					<?php
					$name	= $action[0];
					$check	= $item->checks[$name];
					if ($check === true) :
						$class	= 'check-a';
						$text	= '<span class="state yes"><span>&#10003;</span></span>';
					elseif ($check === false) :
						$class	= 'check-d';
						$text	= '<span class="state no"><span>&#10007;</span></span>';
					elseif ($check === null) :
						$class	= 'check-0';
						$text	= '-';
					else :
						$class	= '';
						$text	= '&#160;';
					endif;
					?>
				<td class="center <?php echo $class;?>">
					<?php echo $text; ?>
				</td>
				<?php endforeach; ?>
				<td class="center">
					<?php echo (int) $item->lft; ?>
					- <?php echo (int) $item->rgt; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
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
