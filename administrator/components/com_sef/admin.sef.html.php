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

class SefHtml 
{
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------

	public function browse( $rows, $lists, $pageNav, $option, $is404mode=false ) 
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
					ViewMode:
					<?php echo $lists['viewmode'];?> 
				</label>
				
				<label>
					Sort by:
					<?php echo $lists['sortby'];?>
				</label>
			</fieldset>
	
			<table class="adminlist">
				<thead>
					<tr>
						<th>#</th>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
						<th>Hits</th>
						<th><?php echo (($is404mode == true) ? 'Date Added' : '<acronym title="Search Engine Friendly">SEF</acronym> URL' ); ?></th>
						<th><?php echo (($is404mode == true) ? 'URL' : 'Real URL' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{ 
			$row =& $rows[$i];
?>
					<tr class="<?php echo 'row'. $k; ?>">
						<td><?php echo $i; ?></td>
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
						<td><?php echo $row->cpt; ?></td>
						<td><?php 
						if ($is404mode == true) {
		   					echo $row->dateadd;
						} else { 
							?><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;?>"><?php echo $row->oldurl;?></a><?php 
						} ?></td>
						<td><?php 
		   				if ($is404mode == true) {
		   					?><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;?>"><?php echo $row->oldurl;?></a><?php 
						} else {
							$row->newurl = str_replace('&','&amp;', $row->newurl);
							echo $row->newurl;
						} ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}

	//-----------
	
	public function edit( $row, $option, $error='' ) 
	{
		?>

		<script type="text/javascript">
		<!--
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.newurl.value == "") {
				alert( "You must provide a URL for the redirection." );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		
		<form action="index.php" method="post" name="adminForm">
			<?php
			if ($error) {
				echo '<p>'.JText::_('Error:').' '.$error.'</p>';
			}
			?>
			<fieldset class="adminform">
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="oldurl">New <acronym title="Search Engine Friendly">SEF</acronym> URL:</label></td>
							<td><input type="text" size="80" name="oldurl" id="oldurl" value="<?php echo $row->oldurl; ?>" /></td>
						</tr>
						<tr>
							<td class="key"><label for="newurl">Old Non-<acronym title="Search Engine Friendly">SEF</acronym> URL:</label></td>
							<td>
								<input type="text" size="80" name="newurl" id="newurl" value="<?php echo $row->newurl; ?>" />
								<p class="info">only relative redirection from the document root <em>without</em> a '/' at the begining</p>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="task" value="save" />
		</form>
		<?php
	}
}
?>
