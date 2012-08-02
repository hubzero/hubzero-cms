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

JToolBarHelper::title(JText::_('TAGS') . ': <small><small>[ ' . JText::_('Relationships') . ' ]</small></small>', 'tags.png');
/*if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::custom('pierce', 'copy', '', JText::_('PIERCE'), false);
	JToolBarHelper::custom('merge', 'forward', '', JText::_('MERGE'), false);
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}*/

$preload = null;

$doc =& JFactory::getDocument();

$doc->addScript('/administrator/components/' . $this->option . '/assets/js/jquery.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/jquery-ui.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/d3/d3.min.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/d3/d3.layout.min.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/d3/d3.geom.min.js');
$doc->addScript('/administrator/components/' . $this->option . '/assets/js/tag_graph.js');
$doc->addScript('/plugins/hubzero/autocompleter/autocompleter.jquery.js');
//$doc->addStyleSheet('/plugins/hubzero/autocompleter/autocompleter.css');
$doc->addStyleSheet('/administrator/components/' . $this->option . '/assets/css/tag_graph.css');

//JToolBarHelper::title(JText::_('Tag Management'));
JToolBarHelper::custom('meta', 'edit', ' ', 'Focus Areas', false);
?>
<form id="tag-sel" action="" method="get">
	<fieldset class="adminform">
		<legend><span>Find tag</span></legend>
		<table class="admintable">
			<tfoot>
				<tr>
					<td colspan="3">
						<button type="submit" id="center">Lookup</button>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<th class="key"><label>Tag:</label></th>
					<td><input type="text" id="center-node" class="tag-entry" value="<?php echo $preload; ?>" /></td>
					<td>Look up a tag to view and assign its relationships to other tags.</td>
				</tr>
				<tr>
					<th class="key">Show relationships:</th>
					<td><label><input type="radio" name="relationship" id="hierarchical" checked="checked" /> labels and hierarchy</label></td>
					<td><label><input type="radio" name="relationship" id="implicit" /> implicit</label></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</form>

<fieldset class="adminform">
	<legend><span>Relationship graph</span></legend>
	<div id="graph"></div>
</fieldset>

<div id="metadata-cont">
	<div class="col width-50 fltlft">
		<form id="metadata" action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post">
			<fieldset class="adminform">
				<legend><span>Metadata</span></legend>
				<table class="admintable">
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="hidden" class="tag-id" name="tag" value="" />
								<input type="hidden" value="update" name="task" />
								<button type="submit">Update</button>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<th class="key">Description:</th>
							<td><textarea cols="100" rows="4" id="description" name="description"></textarea></td>
						</tr>
						<tr>
							<th class="key">Labeled:</th>
							<td><ul id="labeled" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key">Labels:</th>
							<td><ul id="labels" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key">Parents:</th>
							<td><ul id="parents" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key">Children:</th>
							<td><ul id="children" class="textboxlist-holder act"></ul></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="col width-50 fltrt">
		<form action="index.php?option=com_tags" method="post">
			<fieldset class="adminform">
				<legend><span>Delete or merge</span></legend>
				<table class="admintable">
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="hidden" class="tag-id" name="tag" value="" />
								<input type="hidden" value="delete" name="task" />
								<button type="submit">Delete</button>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td><label for="do_merge"><input type="checkbox" name="do_merge" id="do_merge" /> Merge <span class="tag-count"></span> tagged items</label> <label for="merge-tag">into another tag:</label></td>
							<td><input type="text" id="merge-tag" name="merge_tag" class="tag-entry" /></td>
						</tr>
						<tr>
							<td colspan="2"><label><input type="checkbox" name="really" /> I understand that this operation is irreversible and I am prepared to live with its consequences</label></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="clr"></div>
</div>

<form name="adminForm" method="get" action="index.php">
	<input type="hidden" value="<?php echo $this->option; ?>" name="option" />
	<input type="hidden" value="<?php echo $this->controller; ?>" name="controller" />
	<input type="hidden" value="" name="task" />
	<input type="hidden" value="0" name="boxchecked" />
	<input type="hidden" name="plgAutocompleterCss" id="plgAutocompleterCss" value="/plugins/hubzero/autocompleter/autocompleter.css" />
</form>
