<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class TagsHtml 
{
	public function browse( &$rows, &$pageNav, $option, $mtask, $filters ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<fieldset id="filter">
		  		<label>
					<?php echo JText::_('SEARCH'); ?>: 
					<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
				</label>

				<label>
					<?php echo JText::_('FILTER'); ?>:
					<select name="filterby" onchange="document.adminForm.submit();">
						<option value="all"<?php if ($filters['by'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('FILTER_ALL_TAGS'); ?></option>
						<option value="user"<?php if ($filters['by'] == 'user') { echo ' selected="selected"'; } ?>><?php echo JText::_('FILTER_USER_TAGS'); ?></option>
						<option value="admin"<?php if ($filters['by'] == 'admin') { echo ' selected="selected"'; } ?>><?php echo JText::_('FILTER_ADMIN_TAGS'); ?></option>
					</select>
				</label>

				<input type="submit" name="filter_submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>

			<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('RAW_TAG'); ?></th>
						<th><?php echo JText::_('TAG'); ?></th>
						<th><?php echo JText::_('ALIAS'); ?></th>
						<th><?php echo JText::_('ADMIN'); ?></th>
						<th><?php echo JText::_('NUMBER_TAGGED'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		//JPluginHelper::importPlugin('tags');
		//$dispatcher =& JDispatcher::getInstance();
		$database =& JFactory::getDBO();
		$to = new TagsObject( $database );
		
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			$now = date( "Y-m-d H:i:s" );
			$check = '';
			if ($row->admin == 1) {
				$check = '<span class="check">'.strToLower( JText::_('ADMIN') ).'</span>';
			}
			
			/*$totals = $dispatcher->trigger( 'onTagCount', array($row->id) );
			$total = 0;
			foreach ($totals as $t) 
			{
				$total = $total + $t;
			}*/
			$total = $to->getCount( $row->id );
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id=<?php echo $row->id;?>"><?php echo stripslashes($row->raw_tag); ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id=<?php echo $row->id;?>"><?php echo stripslashes($row->tag); ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id=<?php echo $row->id;?>"><?php echo stripslashes($row->alias); ?></a></td>
						<td><?php echo $check; ?></td>
						<td><?php echo $total; ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="<?php echo $mtask; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>

		<?php
	}
	
	//-----------
	
	public function edit( &$database, &$row, $option, $error=null ) 
	{
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.raw_tag.value == '') {
				alert( '<?php echo JText::_('ERROR_EMPTY_TAG'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		
		<?php
		if ($error) {
			echo '<p>ERROR: '.$error.'</p>';
		}
		?>

		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="admin"><?php echo JText::_('ADMIN'); ?>:</label></td>
								<td><input type="checkbox" name="admin" id="admin" value="1" <?php if ($row->admin == 1) { echo 'checked="checked"'; } ?> /></td>
							</tr>
							<tr>
								<td class="key"><label for="raw_tag"><?php echo JText::_('TAG'); ?>:</label></td>
								<td><input type="text" name="raw_tag" id="raw_tag" size="30" maxlength="250" value="<?php echo $row->raw_tag; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="alias"><?php echo JText::_('ALIAS'); ?>:</label></td>
								<td><input type="text" name="alias" id="alias" size="30" maxlength="250" value="<?php echo $row->alias; ?>" /></td>
							</tr>
							<tr>
								<td class="key" style="vertical-align:top;"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
								<td><?php
								echo $editor->display('description', stripslashes($row->description), '360px', '200px', '50', '10');
								?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-50">
				<p><?php echo JText::_('NORMALIZED_EXPLANATION'); ?></p>
			</div>
			<div class="clr"></div>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="tag" value="<?php echo $row->tag; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="save" />
		</form>
		<?php
	}

	//-----------
	
	public function merge( $option, $ids, $rows, $step, $tags ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm" class="editform">
			<p><?php echo JText::_('MERGED_EXPLANATION'); ?></p>
			
			<div class="col width-50">
				<fieldset class="adminform">
					<legend><?php echo JText::_('MERGING'); ?></legend>
					
					<ul>
					<?php
					foreach ($tags as $tag) 
					{
						echo '<li>'.$tag->raw_tag.' ('.$tag->tag.' - '.$tag->total.')</li>'."\n";
					}
					?>
					</ul>
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend><?php echo JText::_('MERGE_TO'); ?></legend>
					
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="existingtag"><?php echo JText::_('EXISTING_TAG'); ?>:</label></td>
								<td>
									<select name="existingtag" id="existingtag">
										<option value=""><?php echo JText::_('OPT_SELECT'); ?></option>
										<?php
										foreach ($rows as $row)
										{
											echo '<option value="'.$row->id.'">'.$row->raw_tag.'</option>'."\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2"><?php echo JText::_('OR'); ?></td>
							</tr>
							<tr>
								<td class="key"><label for="newtag"><?php echo JText::_('NEW_TAG'); ?>:</label></td>
								<td><input type="text" name="newtag" id="newtag" size="25" value="" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="clr"></div>

			<input type="hidden" name="ids" value="<?php echo $ids; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="step" value="<?php echo $step; ?>" />
			<input type="hidden" name="task" value="merge" />
		</form>
		<?php
	}
	
	//-----------
	
	public function pierce( $option, $ids, $rows, $step, $tags ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm" class="editform">
			<p><?php echo JText::_('PIERCED_EXPLANATION'); ?></p>
			
			<div class="col width-50">
				<fieldset class="adminform">
					<legend><?php echo JText::_('PIERCING'); ?></legend>
					
					<ul>
					<?php
					foreach ($tags as $tag) 
					{
						echo '<li>'.$tag->raw_tag.' ('.$tag->tag.' - '.$tag->total.')</li>'."\n";
					}
					?>
					</ul>
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend><?php echo JText::_('PIERCE_TO'); ?></legend>
					
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="existingtag"><?php echo JText::_('EXISTING_TAG'); ?>:</label></td>
								<td>
									<select name="existingtag" id="existingtag">
										<option value=""><?php echo JText::_('OPT_SELECT'); ?></option>
										<?php
										foreach ($rows as $row)
										{
											echo '<option value="'.$row->id.'">'.$row->raw_tag.'</option>'."\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2"><?php echo JText::_('OR'); ?></td>
							</tr>
							<tr>
								<td class="key"><label for="newtag"><?php echo JText::_('NEW_TAG'); ?>:</label></td>
								<td><input type="text" name="newtag" id="newtag" size="25" value="" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="clr"></div>

			<input type="hidden" name="ids" value="<?php echo $ids; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="step" value="<?php echo $step; ?>" />
			<input type="hidden" name="task" value="pierce" />
		</form>
		<?php
	}
}
?>