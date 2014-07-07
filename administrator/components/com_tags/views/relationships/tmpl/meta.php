<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = TagsHelper::getActions();

JToolBarHelper::title(JText::_('TAGS') . ': <small><small>[ ' . JText::_('Focus Areas') . ' ]</small></small>', 'tags.png');
//JToolBarHelper::cancel();

//JToolBarHelper::custom('index', 'edit', ' ', 'Tag Relationships', false);
JToolBarHelper::spacer();
JToolBarHelper::help('focusareas.html', true);

$doc = JFactory::getDocument();

$doc->addScript('/administrator/components/' . $this->option . '/assets/js/jquery.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/jquery-ui.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/tag_graph.js');

$dbh = JFactory::getDBO();
$dbh->setQuery(
	'SELECT *, (SELECT group_concat(resource_type_id) FROM #__focus_area_resource_type_rel WHERE focus_area_id = fa.id) AS types 
	FROM #__tags t
	INNER JOIN #__focus_areas fa ON fa.tag_id = t.id
	ORDER BY raw_tag'
);
$fas = $dbh->loadAssocList();
$dbh->setQuery(
	'SELECT DISTINCT id, type FROM #__resource_types WHERE category = (SELECT id FROM #__resource_types WHERE type = \'Main Types\') AND contributable ORDER BY type'
);
$types = $dbh->loadAssocList('id');
?>
<script type="text/javascript">
window.resourceTypes = <?php echo json_encode(array_values($types)); ?>;
</script>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post">
	<div class="col width-70 fltlft">
		<div id="fas">
<?php 
foreach ($fas as $fa): 
	$type_ids = array_flip(explode(',', $fa['types']));
?>
			<fieldset class="adminform" id="group-<?php echo $fa['id']; ?>">
				<legend><span>Group</span></legend>
				<table class="admintable">
					<tfoot>
						<tr>
							<td colspan="3">
								<button class="delete-group" id="delete-<?php echo $fa['id']; ?>" rel="group-<?php echo $fa['id']; ?>">Delete group</button>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<th class="key"><label>Group name:</label></th>
							<td colspan="2"><input type="text" name="name-<?php echo $fa['id']; ?>" value="<?php echo str_replace('"', '&quot;', $fa['raw_tag']); ?>" /></td>
						</tr>
						<tr>
							<th rowspan="4" class="key"><label for="types-<?php echo $fa['id']; ?>[]">Show for resource types:</label></th>
							<td colspan="2">
								<select id="types-<?php echo $fa['id']; ?>" name="types-<?php echo $fa['id']; ?>[]" multiple="multiple" size="<?php echo count($types); ?>">
									<?php foreach ($types as $type): ?>
										<option value="<?php echo $type['id']; ?>" <?php if (isset($type_ids[$type['id']])) echo 'selected="selected" '; ?>><?php echo $type['type']; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="optional" <?php if (is_null($fa['mandatory_depth'])) echo 'checked="checked" '; ?>/> optional</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="mandatory" <?php if (!is_null($fa['mandatory_depth']) && $fa['mandatory_depth'] < 2) echo 'checked="checked" '; ?>/> mandatory</label>
							</td>
						</tr>
						<tr>
							<td>
								<label><input type="radio" name="mandatory-<?php echo $fa['id']; ?>" value="depth" <?php if ($fa['mandatory_depth'] > 1) echo 'checked="checked" '; ?>/> mandatory</label> <label>until depth:</label>
							</td>
							<td>
								 <input type="text" name="mandatory-depth-<?php echo $fa['id']; ?>" value="<?php if ($fa['mandatory_depth'] > 1) echo $fa['mandatory_depth']; ?>" />
							</td>
						</tr>
						<tr>
							<th rowspan="3" class="key"><label for="types-<?php echo $fa['id']; ?>[]">Selection type:</label></th>
							<td colspan="2">
								<label><input type="radio" name="multiple-<?php echo $fa['id']; ?>" value="multiple" <?php if (!is_null($fa['multiple_depth']) && $fa['multiple_depth'] < 2) echo 'checked="checked" '; ?>/> multiple-select (checkbox)</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label><input type="radio" name="multiple-<?php echo $fa['id']; ?>" value="single" <?php if (is_null($fa['multiple_depth'])) echo 'checked="checked" '; ?>/> single-select (radio) </label>
							</td>
						</tr>
						<tr>
							<td>
								<label><input type="radio" name="multiple-<?php echo $fa['id']; ?>" value="depth" <?php if ($fa['multiple_depth'] > 1) echo 'checked="checked" '; ?>/> single-select</label> <label>until depth: </label>
							</td>
							<td>
								 <input type="text" name="multiple-depth-<?php echo $fa['id']; ?>" value="<?php if ($fa['multiple_depth'] > 1) echo $fa['multiple_depth']; ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
<?php 
endforeach;
$fill_new = !isset($added_new_focus_area);
$type_ids = $fill_new && isset($_POST['types-new']) ? array_flip($_POST['types-new']) : array();
?>
		</div><!-- / #fas -->
		<p>
			<button id="add_group">Add group</button>
		</p>
		<p>
			<input type="hidden" value="<?php echo $this->option; ?>" name="option" />
			<input type="hidden" value="<?php echo $this->controller; ?>" name="controller" />
			<input type="hidden" name="task" value="updatefocusareas" />
			<button type="submit">Save</button>
		</p>
	</div>
	<div class="col width-30 fltrt">
		<p>By default, there is one focus area group, named "focus area", that is shown for every resource on the site during the contribution process, prompting the user to select from the available areas to categorize their submission.</p>
		<p>Here, you can change the properties of this group or add additional groups of focus areas.</p>
		<p>For each group, you may choose which types of resources are valid for the group, and at what depth to consider the group mandatory. (Tags that are nested more deeply than the mandatory level will be presented as a box of optional, multiple-selection choices).</p>
	</div>
	<div class="clr"></div>
</form>

