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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->version == 'dev') {
?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" id="authors-form" method="post" enctype="multipart/form-data">
	<fieldset>
	<?php if ($this->getError()) { ?>
		<p class="error">
			<?php echo implode('<br />', $this->getErrors()); ?>
		</p>
	<?php } ?>
		<div id="non-js-interface">
			<label for="acmembers">
				<?php echo JText::_('AUTHORS_ENTER_LOGINS'); ?>
				<?php 
				JPluginHelper::importPlugin('hubzero');
				$dispatcher =& JDispatcher::getInstance();
				$mc = $dispatcher->trigger('onGetMultiEntry', array(array('members', 'new_authors', 'acmembers')));
				if (count($mc) > 0) {
					echo $mc[0];
				} else { ?> <span class="hint"><?php echo JText::_('ADD_AUTHORS_INSTRUCTIONS'); ?></span>
				<input type="text" name="new_authors" id="acmembers" value="" />
				<?php } ?>
			</label>
		</div>

		<label>
			<span id="new-authors-role-label"><?php echo JText::_('Role'); ?></span>
			<select name="role" id="new-authors-role">
				<option value=""><?php echo JText::_('Author'); ?></option>
<?php 
if ($this->roles)
{
	foreach ($this->roles as $role)
	{
?>
				<option value="<?php echo $this->escape($role->alias); ?>"><?php echo $this->escape($role->title); ?></option>
<?php
	}
}
?>
			</select>
		</label>

		<p class="submit">
			<input type="submit" value="<?php echo JText::_('ADD'); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
</form>
<?php } else { ?>
<p class="warning"><?php echo JText::_('AUTHORS_CANT_CHANGE'); ?></p>
<?php } ?>
<?php
// Do we have any contributors associated with this resource?
if ($this->contributors) {
	$i = 0;
	$n = count($this->contributors);

	// loop through contributors and build HTML list
	$out  = '<table class="list">';
	$out .= ' <tbody>';
	foreach ($this->contributors as $contributor)
	{
		$out .= ' <tr>';
		// build name
		$out .= '  <td width="100%">';

		$name = stripslashes($contributor->firstname) .' ';
		if ($contributor->middlename != NULL) 
		{
			$name .= stripslashes($contributor->middlename) .' ';
		}
		$name .= stripslashes($contributor->lastname);

		$out .= $contributor->name ? $this->escape(stripslashes($contributor->name)) : $this->escape($name);
		$out .= ' <span class="caption">('.$this->escape(stripslashes($contributor->org)).')</span></td>';
		// build order-up/down icons
		if ($this->version=='dev') 
		{
			$out .= '  <td class="u">';
			if ($i > 0 || ($i+0 > 0)) {
			    $out .= '<a href="/index.php?option='.$this->option.'&controller='.$this->controller.'&tmpl=component&pid='.$this->id.'&id='.$contributor->id.'&task=reorder&move=up" class="order up" title="'.JText::_('COM_TOOLS_MOVE_UP').'"><span>'.JText::_('COM_TOOLS_MOVE_UP').'</span></a>';
	  		} else {
	  		    $out .= '&nbsp;';
			}
			$out .= '</td>';
			$out .= '  <td class="t">';
			if ($i < $n-1 || $i+0 < $n-1) {
				$out .= '<a href="/index.php?option='.$this->option.'&controller='.$this->controller.'&tmpl=component&pid='.$this->id.'&id='.$contributor->id.'&task=reorder&move=down" class="order down" title="'.JText::_('COM_TOOLS_MOVE_DOWN').'"><span>'.JText::_('COM_TOOLS_MOVE_DOWN').'</span></a>';
	  		} else {
	  		    $out .= '&nbsp;';
			}
			$out .= '</td>';
			//.ContribtoolHtml::orderUpIcon( $i, $this->id, $contributor->id, 'c' ).'</td>';
			//$out .= '  <td class="d">'.ContribtoolHtml::orderDownIcon( $i, $n, $this->id, $contributor->id, 'c' ).'</td>';
		// build trash icon
			$out .= '  <td class="t"><a href="/index.php?option='.$this->option.'&controller='.$this->controller.'&task=remove&tmpl=component&id='.$contributor->id.'&pid='.$this->id.'"><img src="/components/com_contribute/images/trash.gif" alt="'.JText::_('DELETE').'" /></a></td>';
		}
		$out .= ' </tr>';

		$i++;
	}
	$out .= ' </tbody>';
	$out .= '</table>';
} else {
	$out .= '<p>'.JText::_('AUTHORS_NONE_FOUND').'</p>';
}
echo $out;