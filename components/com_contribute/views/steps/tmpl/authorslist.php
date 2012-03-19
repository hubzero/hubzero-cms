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

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('COM_CONTRIBUTE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
	<?php
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'contribute.css')) {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->option.DS.'contribute.css" />'."\n";
		} else {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'components'.DS.$this->option.DS.'contribute.css" />'."\n";
		}
	?>
	
    <script type="text/javascript" src="/media/system/js/mootools.js"></script>
	<script type="text/javascript" src="/components/<?php echo $this->option; ?>/contribute.js"></script>
 </head>
 <body id="small-page">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form action="index.php" id="authors-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<div id="non-js-interface">
					<label>
						<select name="authid" id="authid">
							<option value=""><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_SELECT'); ?></option>
					<?php
				if ($this->rows)
				{
					foreach ($this->rows as $row)
					{
						if ($row->lname || $row->fname) {
							$name  = stripslashes($row->lname).', ';
							$name .= stripslashes($row->fname);
							if ($row->mname != NULL) {
								$name .= ' '.stripslashes($row->mname);
							}
						} else {
							$name = stripslashes($row->name);
						}

						echo '<option value="'.$row->uidNumber.'">'.$name.'</option>'."\n";
					}
				}
					?> 
						</select>
						<?php echo JText::_('COM_CONTRIBUTE_OR'); ?>
					</label>
				
					<label>
						<input type="text" name="new_authors" value="" />
						<?php echo JText::_('COM_CONTRIBUTE_AUTHORS_ENTER_LOGINS'); ?>
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
					<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_ADD'); ?>" />
				</p>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="task" value="saveauthor" />
			</fieldset>
		</form>
<?php	
// Do we have any contributors associated with this resource?
if ($this->contributors) {
	$i = 0;
	$n = count( $this->contributors );

?>
	<form action="index.php" id="authors-form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="task" value="updateauthor" />

		<table class="list">
			<tfoot>
				<td></td>
				<td>
					<input type="submit" value="<?php echo JText::_('Save Changes'); ?>"/>
				</td>
				<td></td>
				<td></td>
				<td></td>
			</tfoot>
			<tbody>
<?php
	foreach ($this->contributors as $contributor)
	{
		if ($contributor->lastname || $contributor->firstname) {
			$name  = stripslashes($contributor->firstname) .' ';
			if ($contributor->middlename != NULL) {
				$name .= stripslashes($contributor->middlename) .' ';
			}
			$name .= stripslashes($contributor->lastname);
		} else {
			$name  = stripslashes($contributor->name);
		}
?>
				<tr>
					<td width="100%">
						<?php echo $name; ?>
						<?php echo ($contributor->org) ? ' <span class="caption">('.$contributor->org.')</span>' : ''; ?>
					</td>
					<td>
						<select name="authors[<?php echo $contributor->id; ?>]" id="role-<?php echo $contributor->id; ?>">
							<option value=""<?php if ($contributor->role == '') { echo ' selected="selected"'; }?>><?php echo JText::_('Author'); ?></option>
<?php 
					if ($this->roles)
					{
						foreach ($this->roles as $role)
						{
?>
							<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($contributor->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape($role->title); ?></option>
<?php
						}
					}
?>
						</select>
					</td>
					<td class="u"><?php
					if ($i > 0 || ($i+0 > 0)) {
					    echo '<a href="index.php?option=com_contribute&amp;no_html=1&amp;pid='.$this->id.'&amp;id='.$contributor->id.'&amp;task=orderupc" class="order up" title="'.JText::_('COM_CONTRIBUTE_MOVE_UP').'"><span>'.JText::_('COM_CONTRIBUTE_MOVE_UP').'</span></a>';
			  		} else {
			  		    echo '&nbsp;';
					}
					?></td>
					<td class="d"><?php
					if ($i < $n-1 || $i+0 < $n-1) {
						echo '<a href="index.php?option=com_contribute&amp;no_html=1&amp;pid='.$this->id.'&amp;id='.$contributor->id.'&amp;task=orderdownc" class="order down" title="'.JText::_('COM_CONTRIBUTE_MOVE_DOWN').'"><span>'.JText::_('COM_CONTRIBUTE_MOVE_DOWN').'</span></a>';
			  		} else {
			  		    echo '&nbsp;';
					}
					?></td>
					<td class="t"><a href="index.php?option=<?php echo $this->option; ?>&amp;task=removeauthor&amp;no_html=1&amp;id=<?php echo $contributor->id; ?>&amp;pid=<?php echo $this->id; ?>"><img src="/components/<?php echo $this->option; ?>/images/trash.gif" alt="<?php echo JText::_('COM_CONTRIBUTE_DELETE'); ?>" /></a></td>
				</tr>
<?php
		$i++;
	}
?>
			</tbody>
		</table>
<?php } else { ?>
		<p><?php echo JText::_('COM_CONTRIBUTE_AUTHORS_NONE_FOUND'); ?></p>
<?php } ?>
 </body>
</html>
