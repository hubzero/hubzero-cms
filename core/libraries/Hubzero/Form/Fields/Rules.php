<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Access\Access;
use Hubzero\Html\Builder\Behavior;
use Exception;
use App;

/**
 * Field for assigning permissions to groups for a given asset
 */
class Rules extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Rules';

	/**
	 * Method to get the field input markup for Access Control Lists.
	 * Optionally can be associated with a specific component and section.
	 *
	 * TODO: Add access check.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		Behavior::tooltip();

		// Initialise some field attributes.
		$section    = $this->element['section'] ? (string) $this->element['section'] : '';
		$component  = $this->element['component'] ? (string) $this->element['component'] : '';
		$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';

		// Get the actions for the asset.
		$comfile = $component ? App::get('component')->path($component) . '/config/access.xml' : '';
		$sectioned = "/access/section[@name='" . ($section ?: 'component') . "']/";

		$actions = Access::getActionsFromFile($comfile, $sectioned);

		// Iterate over the children and add to the actions.
		foreach ($this->element->children() as $el)
		{
			if ($el->getName() == 'action')
			{
				$actions[] = (object) array(
					'name' => (string) $el['name'],
					'title' => (string) $el['title'],
					'description' => (string) $el['description']
				);
			}
		}

		// Get the explicit rules for this asset.
		if ($section == 'component')
		{
			// Need to find the asset id by the name of the component.
			$db = App::get('db');

			$query = $db->getQuery()
				->select('id')
				->from('#__assets')
				->whereEquals('name', $component);
			$db->setQuery($query->toString());
			$assetId = (int) $db->loadResult();

			if ($error = $db->getErrorMsg())
			{
				throw new Exception(500, $error);
			}
		}
		else
		{
			// Find the asset id of the content.
			// Note that for global configuration, com_config injects asset_id = 1 into the form.
			$assetId = $this->form->getValue($assetField);
		}

		// Full width format.

		// Get the rules for just this asset (non-recursive).
		$assetRules = Access::getAssetRules($assetId);

		// Get the available user groups.
		$groups = $this->getUserGroups();

		// Build the form control.
		$curLevel = 0;

		$lang = App::get('language');

		// Prepare output
		$html = array();
		$html[] = '<div id="permissions-sliders" class="pane-sliders">';
		$html[] = '<p class="rule-desc">' . $lang->txt('JLIB_RULES_SETTINGS_DESC') . '</p>';
		$html[] = '<div id="permissions-rules">';
		// If AssetId is blank and section wasn't set to component, set it to the component name here for inheritance checks.
		$assetId = empty($assetId) && $section != 'component' ? $component : $assetId;

		// Start a row for each user group.
		foreach ($groups as $group)
		{
			$difLevel = $group->level - $curLevel;

			$html[] = '<h3 class="pane-toggler title"><a href="javascript:void(0);"><span>';
			$html[] = str_repeat('<span class="level">|&ndash;</span> ', $curLevel = $group->level) . $group->text;
			$html[] = '</span></a></h3>';
			$html[] = '<div class="panel">';
			$html[] = '<div class="pane-slider content pane-hide">';

			$html[] = '<table class="group-rules">';
			$html[] = '<thead>';
			$html[] = '<tr>';

			$html[] = '<th class="actions" id="actions-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . $lang->txt('JLIB_RULES_ACTION') . '</span>';
			$html[] = '</th>';

			$html[] = '<th class="settings" id="settings-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . $lang->txt('JLIB_RULES_SELECT_SETTING') . '</span>';
			$html[] = '</th>';

			// The calculated setting is not shown for the root group of global configuration.
			$canCalculateSettings = ($group->parent_id || !empty($component));
			if ($canCalculateSettings)
			{
				$html[] = '<th id="aclactionth' . $group->value . '">';
				$html[] = '<span class="acl-action">' . $lang->txt('JLIB_RULES_CALCULATED_SETTING') . '</span>';
				$html[] = '</th>';
			}

			$html[] = '</tr>';
			$html[] = '</thead>';
			$html[] = '<tbody>';

			foreach ($actions as $action)
			{
				$html[] = '<tr>';
				$html[] = '<td headers="actions-th' . $group->value . '">';
				$html[] = '<label class="hasTip" for="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . htmlspecialchars($lang->txt($action->title) . '::' . $lang->txt($action->description), ENT_COMPAT, 'UTF-8') . '">';
				$html[] = $lang->txt($action->title);
				$html[] = '</label>';
				$html[] = '</td>';

				$html[] = '<td headers="settings-th' . $group->value . '">';

				$html[] = '<select name="' . $this->name . '[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name
					. '_' . $group->value . '" title="'
					. $lang->txt('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', $lang->txt($action->title), trim($group->text)) . '">';
				$inheritedRule = Access::checkGroup($group->value, $action->name, $assetId);

				// Get the actual setting for the action for this group.
				$assetRule = $assetRules->allow($action->name, $group->value);

				// Build the dropdowns for the permissions sliders

				// The parent group has "Not Set", all children can rightly "Inherit" from that.
				$html[] = '<option value=""' . ($assetRule === null ? ' selected="selected"' : '') . '>' . $lang->txt(empty($group->parent_id) && empty($component) ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
				$html[] = '<option value="1"' . ($assetRule === true ? ' selected="selected"' : '') . '>' . $lang->txt('JLIB_RULES_ALLOWED') . '</option>';
				$html[] = '<option value="0"' . ($assetRule === false ? ' selected="selected"' : '') . '>' . $lang->txt('JLIB_RULES_DENIED') . '</option>';

				$html[] = '</select>&#160; ';

				// If this asset's rule is allowed, but the inherited rule is deny, we have a conflict.
				if (($assetRule === true) && ($inheritedRule === false))
				{
					$html[] = $lang->txt('JLIB_RULES_CONFLICT');
				}

				$html[] = '</td>';

				// Build the Calculated Settings column.
				// The inherited settings column is not displayed for the root group in global configuration.
				if ($canCalculateSettings)
				{
					$html[] = '<td headers="aclactionth' . $group->value . '">';

					// This is where we show the current effective settings considering currrent group, path and cascade.
					// Check whether this is a component or global. Change the text slightly.
					if (Access::checkGroup($group->value, 'core.admin', $assetId) !== true)
					{
						if ($inheritedRule === null)
						{
							$html[] = '<span class="icon-16-unset">' . $lang->txt('JLIB_RULES_NOT_ALLOWED') . '</span>';
						}
						elseif ($inheritedRule === true)
						{
							$html[] = '<span class="icon-16-allowed">' . $lang->txt('JLIB_RULES_ALLOWED') . '</span>';
						}
						elseif ($inheritedRule === false)
						{
							if ($assetRule === false)
							{
								$html[] = '<span class="icon-16-denied">' . $lang->txt('JLIB_RULES_NOT_ALLOWED') . '</span>';
							}
							else
							{
								$html[] = '<span class="icon-16-denied"><span class="icon-16-locked">' . $lang->txt('JLIB_RULES_NOT_ALLOWED_LOCKED') . '</span></span>';
							}
						}
					}
					elseif (!empty($component))
					{
						$html[] = '<span class="icon-16-allowed"><span class="icon-16-locked">' . $lang->txt('JLIB_RULES_ALLOWED_ADMIN') . '</span></span>';
					}
					else
					{
						// Special handling for  groups that have global admin because they can't  be denied.
						// The admin rights can be changed.
						if ($action->name === 'core.admin')
						{
							$html[] = '<span class="icon-16-allowed">' . $lang->txt('JLIB_RULES_ALLOWED') . '</span>';
						}
						elseif ($inheritedRule === false)
						{
							// Other actions cannot be changed.
							$html[] = '<span class="icon-16-denied"><span class="icon-16-locked">' . $lang->txt('JLIB_RULES_NOT_ALLOWED_ADMIN_CONFLICT') . '</span></span>';
						}
						else
						{
							$html[] = '<span class="icon-16-allowed"><span class="icon-16-locked">' . $lang->txt('JLIB_RULES_ALLOWED_ADMIN') . '</span></span>';
						}
					}

					$html[] = '</td>';
				}

				$html[] = '</tr>';
			}

			$html[] = '</tbody>';
			$html[] = '</table>';

			$html[] = '</div></div>';
		}

		$html[] = '</div><div class="rule-notes">';
		if ($section == 'component' || $section == null)
		{
			$html[] = $lang->txt('JLIB_RULES_SETTING_NOTES');
		}
		else
		{
			$html[] = $lang->txt('JLIB_RULES_SETTING_NOTES_ITEM');
		}
		$html[] = '</div>';
		$html[] = '</div>';

		$js = "jQuery(document).ready(function($){
				$('div#permissions-rules').accordion({
					heightStyle: 'content'
				});
			});";

		App::get('document')->addScriptDeclaration($js);

		return implode("\n", $html);
	}

	/**
	 * Get a list of the user groups.
	 *
	 * @return  array
	 */
	protected function getUserGroups()
	{
		// Initialise variables.
		$db = App::get('db');
		$query = $db->getQuery()
			->select('a.id', 'value')
			->select('a.title', 'text')
			->select('COUNT(DISTINCT b.id)', 'level')
			->select('a.parent_id')
			->from('#__usergroups', 'a')
			->joinRaw('#__usergroups AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
			->group('a.id')
			->group('a.title')
			->group('a.lft')
			->group('a.rgt')
			->group('a.parent_id')
			->order('a.lft', 'ASC');
		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		return $options;
	}
}
