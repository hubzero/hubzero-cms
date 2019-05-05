<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Lang;
use App;

/**
 * Text Filters form field.
 */
class Filters extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Filters';

	/**
	 * Method to get the field input markup.
	 *
	 * TODO: Add access check.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Get the available user groups.
		$groups = $this->getUserGroups();
		// Build the form control.
		$html = array();

		// Open the table.
		$html[] = '<table id="filter-config">';

		// The table heading.
		$html[] = '	<thead>';
		$html[] = '	<tr>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action">'.Lang::txt('JGLOBAL_FILTER_GROUPS_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action" title="'.Lang::txt('JGLOBAL_FILTER_TYPE_LABEL').'">'.Lang::txt('JGLOBAL_FILTER_TYPE_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action" title="'.Lang::txt('JGLOBAL_FILTER_TAGS_LABEL').'">'.Lang::txt('JGLOBAL_FILTER_TAGS_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action" title="'.Lang::txt('JGLOBAL_FILTER_ATTRIBUTES_LABEL').'">'.Lang::txt('JGLOBAL_FILTER_ATTRIBUTES_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '	</tr>';
		$html[] = '	</thead>';

		// The table body.
		$html[] = '	<tbody>';

		foreach ($groups as $group)
		{
			if (!isset($this->value[$group->value]))
			{
				$this->value[$group->value] = array('filter_type' => 'BL', 'filter_tags' => '', 'filter_attributes' => '');
			}
			$group_filter = $this->value[$group->value];

			$html[] = '	<tr>';
			$html[] = '		<th class="acl-groups left">';
			$html[] = '			'.str_repeat('<span class="gi">|&mdash;</span>', $group->level).$group->text;
			$html[] = '		</th>';
			$html[] = '		<td>';
			$html[] = '			<select name="'.$this->name.'['.$group->value.'][filter_type]" id="'.$this->id.$group->value.'_filter_type" class="hasTip" title="'.Lang::txt('JGLOBAL_FILTER_TYPE_LABEL').'::'.Lang::txt('JGLOBAL_FILTER_TYPE_DESC').'">';
			$html[] = '				<option value="BL"'.($group_filter['filter_type'] == 'BL' ? ' selected="selected"' : '').'>'.Lang::txt('COM_CONFIG_FIELD_FILTERS_DEFAULT_BLACK_LIST').'</option>';
			$html[] = '				<option value="CBL"'.($group_filter['filter_type'] == 'CBL' ? ' selected="selected"' : '').'>'.Lang::txt('COM_CONFIG_FIELD_FILTERS_CUSTOM_BLACK_LIST').'</option>';
			$html[] = '				<option value="WL"'.($group_filter['filter_type'] == 'WL' ? ' selected="selected"' : '').'>'.Lang::txt('COM_CONFIG_FIELD_FILTERS_WHITE_LIST').'</option>';
			$html[] = '				<option value="NH"'.($group_filter['filter_type'] == 'NH' ? ' selected="selected"' : '').'>'.Lang::txt('COM_CONFIG_FIELD_FILTERS_NO_HTML').'</option>';
			$html[] = '				<option value="NONE"'.($group_filter['filter_type'] == 'NONE' ? ' selected="selected"' : '').'>'.Lang::txt('COM_CONFIG_FIELD_FILTERS_NO_FILTER').'</option>';
			$html[] = '			</select>';
			$html[] = '		</td>';
			$html[] = '		<td>';
			$html[] = '			<input name="'.$this->name.'['.$group->value.'][filter_tags]" id="'.$this->id.$group->value.'_filter_tags" title="'.Lang::txt('JGLOBAL_FILTER_TAGS_LABEL').'" value="'.$group_filter['filter_tags'].'"/>';
			$html[] = '		</td>';
			$html[] = '		<td>';
			$html[] = '			<input name="'.$this->name.'['.$group->value.'][filter_attributes]" id="'.$this->id.$group->value.'_filter_attributes" title="'.Lang::txt('JGLOBAL_FILTER_ATTRIBUTES_LABEL').'" value="'.$group_filter['filter_attributes'].'"/>';
			$html[] = '		</td>';
			$html[] = '	</tr>';
		}
		$html[] = '	</tbody>';

		// Close the table.
		$html[] = '</table>';
		return implode("\n", $html);
	}

	/**
	 * A helper to get the list of user groups.
	 *
	 * @return  array
	 */
	protected function getUserGroups()
	{
		// Get a database object.
		$db = App::get('db');
		// Get the user groups from the database.
		$query = $db->getQuery();
		$query->select('a.id', 'value')
			->select('a.title', 'text')
			->select('COUNT(DISTINCT b.id)', 'level')
			->from('#__usergroups', 'a')
			->joinRaw('#__usergroups AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
			->group('a.id')
			->group('a.title')
			->group('a.lft')
			->order('a.lft', 'ASC');
		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		return $options;
	}
}
