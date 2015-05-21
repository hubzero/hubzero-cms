<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');

$function	= Request::getCmd('function', 'jSelectPosition');
$lang		= Lang::getRoot();
$ordering	= $this->escape($this->state->get('list.ordering'));
$direction	= $this->escape($this->state->get('list.direction'));
$clientId	= $this->state->get('filter.client_id');
$state		= $this->state->get('filter.state');
$template	= $this->state->get('filter.template');
$type		= $this->state->get('filter.type');
?>
<h2 class="modal-title"><?php echo Lang::txt('COM_MODULES'); ?></h2>
<form action="<?php echo Route::url('index.php?option=com_modules&view=positions&layout=modal&tmpl=component&function='.$function.'&client_id=' .$clientId);?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="filters clearfix">
		<div class="col width-40 fltlft">
			<label for="filter_search">
				<?php echo Lang::txt('JSearch_Filter_Label'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" placeholder="<?php echo Lang::txt('COM_MODULES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="col width-60 fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', JHtml::_('modules.templateStates'), 'value', 'text', $state, true);?>
			</select>

			<select name="filter_type" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_MODULES_OPTION_SELECT_TYPE');?></option>
				<?php echo Html::select('options', JHtml::_('modules.types'), 'value', 'text', $type, true);?>
			</select>

			<select name="filter_template" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_TEMPLATE');?></option>
				<?php echo Html::select('options', JHtml::_('modules.templates', $clientId), 'value', 'text', $template, true);?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="title" width="20%">
					<?php echo $this->grid('sort', 'JGLOBAL_TITLE', 'value', $direction, $ordering); ?>
				</th>
				<th>
					<?php echo $this->grid('sort', 'COM_MODULES_HEADING_TEMPLATES', 'templates', $direction, $ordering); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php $i=1; foreach ($this->items as $value=>$templates) : ?>
			<tr class="row<?php echo $i=1-$i;?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $value; ?>');"><?php echo $this->escape($value); ?></a>
				</td>
				<td>
					<?php if (!empty($templates)):?>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $value; ?>');">
						<ul>
						<?php foreach ($templates as $template => $label):?>
							<li><?php echo $lang->hasKey($label) ? Lang::txt('COM_MODULES_MODULE_TEMPLATE_POSITION', Lang::txt($template), Lang::txt($label)) : Lang::txt($template);?></li>
						<?php endforeach;?>
						</ul>
					</a>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $ordering; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $direction; ?>" />
	<?php echo Html::input('token'); ?>
</form>
