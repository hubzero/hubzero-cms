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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class XPollHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------

	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function browse( &$rows, &$pageNav, $option ) 
	{
		$juser =& JFactory::getUser();
		?>
		<form action="index.php" method="post" name="adminForm">
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
						<th><?php echo JText::_('POLL_TITLE'); ?></th>
						<th><?php echo JText::_('OPTIONS'); ?></th>
						<th><?php echo JText::_('PUBLISHED'); ?></th>
						<th><?php echo JText::_('OPEN'); ?></th>
						<th colspan="2"><?php echo JText::_('VOTES'); ?></th>
						<th><?php echo JText::_('CHECKED_OUT'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row =& $rows[$i];
			$task  = $row->published ? 'unpublish' : 'publish';
			$class = $row->published ? 'published' : 'unpublished';
			$alt   = $row->published ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED');
			
			$task2  = ($row->open == 1) ? 'close' : 'open';
			$class2 = ($row->open == 1) ? 'published' : 'unpublished';
			$alt2   = ($row->open == 1) ? JText::_('OPEN') : JText::_('CLOSED');
?>
					<tr class="<?php echo "row$k"; ?>">
						<?php if ($row->checked_out && $row->checked_out != $juser->get('id')) { ?>
						<td> </td>
						<?php } else { ?>
						<td><input type="checkbox" name="cid[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<?php } ?>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;cid[]=<? echo $row->id; ?>" title="Edit this poll"><?php echo $row->title; ?></a></td>
						<td><?php echo $row->numoptions; ?></td>
						<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task; ?>&amp;cid[]=<? echo $row->id; ?>" title="Set this to <?php echo $task;?>"><span><?php echo $alt; ?></span></a></td>
						<td><a class="<?php echo $class2;?>" href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task2; ?>&amp;cid[]=<? echo $row->id; ?>" title="Set this to <?php echo $task2;?>"><span><?php echo $alt2; ?></span></a></td>
						<td><?php echo $row->voters; ?></td>
						<td><?php if ($row->voters > 0) { ?><a class="reset" href="index.php?option=<?php echo $option ?>&amp;task=reset&amp;cid[]=<? echo $row->id; ?>" title="Reset the stats on this poll"><span>reset</span></a><?php } ?></td>
						<td><?php echo $row->editor; ?></td>
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

	public function edit( &$row, &$options, &$lists, $option ) 
	{
		//mosMakeHtmlSafe( $row, ENT_QUOTES );
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			// Do field validation
			if (form.title.value == "") {
				alert( "<?php echo JText::_( 'POLL_MUST_HAVE_A_TITLE', true ); ?>" );
			} else if ( isNaN( parseInt( form.lag.value ) ) ) {
				alert( "<?php echo JText::_( 'POLL_MUST_HAVE_A_NON-ZERO_LAG_TIME', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('PARAMETERS'); ?></legend>

					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><?php echo JText::_('POLL_TITLE'); ?>:</td>
								<td><input type="text" name="title" size="60" value="<?php echo htmlspecialchars( stripslashes($row->title), ENT_QUOTES ); ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('LAG'); ?>:</td>
								<td><input type="text" name="lag" size="10" value="<?php echo $row->lag; ?>" /> <?php echo JText::_('SECONDS_BETWEEN_VOTES'); ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('OPTIONS'); ?></legend>

					<table class="admintable">
						<tbody>
					<?php
							for ($i=0, $n=count( $options ); $i < $n; $i++ ) { 
					?>
							<tr>
								<td class="key"><?php echo ($i+1); ?></td>
								<td><input type="text" name="polloption[<?php echo $options[$i]->id; ?>]" value="<?php echo htmlspecialchars( stripslashes($options[$i]->text), ENT_QUOTES ); ?>" size="60" /></td>
							</tr>
					<?php	
							}
							for (; $i < 12; $i++) { 
					?>
							<tr>
								<td class="key"><?php echo ($i+1); ?></td>
								<td><input type="text" name="polloption[]" value="" size="60" /></td>
							</tr>
					<?php	
							}
					?>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('SHOW_ON_MENU_ITEMS'); ?>:</legend>

					<?php echo $lists['select']; ?>
				</fieldset>
			</div>
			<div class="clr"></div>
			
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />
		</form>
		<?php
	}

}
?>
