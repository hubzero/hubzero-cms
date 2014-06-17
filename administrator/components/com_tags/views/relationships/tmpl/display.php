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

JToolBarHelper::title(JText::_('COM_TAGS') . ': ' . JText::_('COM_TAGS_RELATIONSHIPS'), 'tags.png');

$base = str_replace('/administrator', '', rtrim(JURI::getInstance()->base(true), '/'));

JHTML::_('behavior.tooltip');

$this->css('tag_graph.css');
?>
<form id="tag-sel" action="" method="get">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_TAGS_FIND_TAG'); ?></span></legend>
		<table class="admintable">
			<tfoot>
				<tr>
					<td colspan="3">
						<button type="submit" id="center"><?php echo JText::_('COM_TAGS_LOOKUP'); ?></button>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<th class="key"><label><?php echo JText::_('COM_TAGS_TAG'); ?>:</label></th>
					<td><input type="text" id="center-node" class="tag-entry" value="<?php echo $this->get('preload'); ?>" /></td>
					<td><?php echo JText::_('COM_TAGS_TAG_RELATIONSHIP'); ?></td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_TAGS_SHOW_RELATIONSHIPS'); ?>:</th>
					<td><label><input type="radio" name="relationship" id="hierarchical" checked="checked" /> <?php echo JText::_('COM_TAGS_RELATIONSHIP_HIERARCHICAL'); ?></label></td>
					<td><label><input type="radio" name="relationship" id="implicit" /> <?php echo JText::_('COM_TAGS_RELATIONSHIP_IMPLICIT'); ?></label></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</form>

<fieldset class="adminform">
	<legend><span><?php echo JText::_('COM_TAGS_RELATIONSHIP_GRAPH'); ?></span></legend>
	<div id="graph"></div>
</fieldset>

<div id="metadata-cont">
	<div class="col width-100">
		<form id="metadata" action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post">
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_TAGS_RELATIONSHIP_METADATA'); ?></span></legend>
				<table class="admintable">
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="hidden" class="tag-id" name="tag" value="" />
								<input type="hidden" value="update" name="task" />
								<button type="submit"><?php echo JText::_('COM_TAGS_RELATIONSHIP_UPDATE'); ?></button>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<th class="key"><?php echo JText::_('COM_TAGS_RELATIONSHIP_DESCRIPTION'); ?>:</th>
							<td><textarea cols="100" rows="4" id="description" name="description"></textarea></td>
						</tr>
						<tr>
							<th class="key"><?php echo JText::_('COM_TAGS_RELATIONSHIP_LABELED'); ?>:</th>
							<td><ul id="labeled" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key"><?php echo JText::_('COM_TAGS_RELATIONSHIP_LABELS'); ?>:</th>
							<td><ul id="labels" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key"><?php echo JText::_('COM_TAGS_RELATIONSHIP_PARENTS'); ?>:</th>
							<td><ul id="parents" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key"><?php echo JText::_('COM_TAGS_RELATIONSHIP_CHILDREN'); ?>:</th>
							<td><ul id="children" class="textboxlist-holder act"></ul></td>
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
	<input type="hidden" name="plgAutocompleterCss" id="plgAutocompleterCss" value="<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.css" />
</form>

<script src="<?php echo $base; ?>/administrator/components/<?php echo $this->option; ?>/assets/js/d3/d3.min.js"></script>
<script src="<?php echo $base; ?>/administrator/components/<?php echo $this->option; ?>/assets/js/d3/d3.layout.min.js"></script>
<script src="<?php echo $base; ?>/administrator/components/<?php echo $this->option; ?>/assets/js/d3/d3.geom.min.js"></script>
<script src="<?php echo $base; ?>/administrator/components/<?php echo $this->option; ?>/assets/js/tag_graph.js"></script>
<script src="<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.jquery.js"></script>
