<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
	define('n',"\n");
	define('t',"\t");
	define('r',"\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class FeedbackHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function quotes( $rows, $pageNav, $option, $filters, $type ) 
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
				
		<?php 
		if ($type == 'regular') {	
			echo ('<h3>'.JText::_('FEEDBACK_SUBMITTED_QUOTES').'</h3><p class="extranav"><a href="index.php?option='.$option.'&amp;type=selected">'.JText::_('FEEDBACK_SELECTED_QUOTES').'</a>.</p>');
		} else {
			echo ('<h3>'.JText::_('FEEDBACK_SELECTED_QUOTES').'</h3><p class="extranav"><a href="index.php?option='.$option.'">'.JText::_('FEEDBACK_SUBMITTED_QUOTES').'</a>.</p>');
		}
?>
			<fieldset id="filter">
				<label>
					<?php echo JText::_('FEEDBACK_SEARCH'); ?>: 
					<input type="text" name="search" value="<?php echo $filters['search']; ?>" />
				</label>
			
				<label>
					<?php echo JText::_('FEEDBACK_SORT'); ?>: 
					<select name="sortby" id="sortby">
						<option value="date"<?php if ($filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_DATE'); ?></option>
						<option value="fullname"<?php if ($filters['sortby'] == 'fullname') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_NAME'); ?></option>
						<option value="org"<?php if ($filters['sortby'] == 'org') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_ORGANIZATION'); ?></option>
					</select>
				</label>
				
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
		
			<table class="adminlist">
				<thead>
					<tr>
						<th>#</th>
						<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('FEEDBACK_COL_SUBMITTED'); ?></th>
						<th><?php echo JText::_('FEEDBACK_COL_AUTHOR'); ?></th>
						<th><?php echo JText::_('FEEDBACK_COL_ORGANIZATION'); ?></th>
						<th><?php echo JText::_('FEEDBACK_COL_QUOTE'); ?></th>
						<th><?php echo JText::_('FEEDBACK_COL_PICTURE'); ?></th>
<?php 		
		if ($type == 'regular') {	
			echo ('<th>'.JText::_('FEEDBACK_COL_PUBLISH_CONSENT').'</th><th>'.JText::_('FEEDBACK_COL_UID').'</th>');
		} else {
			echo ('<th>'.JText::_('FEEDBACK_COL_QUOTES').'</th><th>'.JText::_('FEEDBACK_COL_ROTATION').'</th>');
		}
?>   
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="9"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];

			//cut quote at 100 characters
			$quotepreview = stripslashes($row->quote);
			$quotepreview = substr($quotepreview, 0, 100);
			if (strlen ($quotepreview)>=99) {
				$quotepreview = $quotepreview.'...';
			}
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo $i; ?></td>
						<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onClick="isChecked(this.checked);" /></td>
						<td><?php echo JHTML::_('date', $row->date, '%d %b, %Y'); ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;type=<?php echo $type ?>&amp;id=<?php echo $row->id; ?>"><?php echo stripslashes($row->fullname); ?></a></td>
						<td><?php echo ($row->org) ? stripslashes($row->org) : '&nbsp;';?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;type=<?php echo $type ?>&amp;id=<?php echo $row->id; ?>"><?php echo $quotepreview;?></a></td>
						<td><?php echo ($row->picture != NULL) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '&nbsp;'; ?></td>
						<td><?php if ($type == 'regular') {
								echo ($row->publish_ok == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
							} else {
								echo ($row->notable_quotes == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
							} ?></td>
						<td><?php if ($type == 'regular') {
								echo $row->userid;
							} else {
								echo ($row->flash_rotation == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
							} ?></td>
				  </tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
		
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="type" value="<?php echo $type ?>" />
		</form>
<?php
	}

	//-----------

	public function create( $option )
	{
		?>
		<form action="index.php" method="post" name="adminForm">
			<fieldset>
				<label>
					<?php echo JText::_('FEEDBACK_USERNAME'); ?>: 
					<input type="text" name="username" value="" />
				</label>
			
				<input type="submit" name="submit" value="<?php echo JText::_('FEEDBACK_NEXT'); ?>" />
			
				<p><?php echo JText::_('FEEDBACK_NO_USERNAME'); ?></p>
				
				<input type="hidden" name="option" value="<?php echo $option ?>" />
				<input type="hidden" name="task" value="edit" />
			</fieldset>
		</form>
		<?php
	}

	//-----------
	
	public function edit( $row, $action, $option, $type ) 
	{
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		$xhub = &XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		
		if ($type != 'regular') {
			$short_quote = stripslashes($row->short_quote);
			$miniquote = stripslashes($row->miniquote);
			if (!$short_quote) {
				$short_quote =  substr(stripslashes($row->quote), 0, 270);
			}
			if (!$miniquote) {
				$miniquote =  substr(stripslashes($short_quote), 0, 150);
			}

			if (strlen ($short_quote)>=271) {
				$short_quote = $short_quote.'...';
			}
		}
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('adminForm');
			
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.fullname.value == '') {
				alert( '<?php echo JText::_('FEEDBACK_AUTHOR_MUST_HAVE_NAME'); ?>' );
			} else if (form.org.value == '') {
				alert( '<?php echo JText::_('FEEDBACK_AUTHOR_MUST_HAVE_AFFILIATION'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		
		function getAuthorImage() 
		{
			var filew = window.filer;
			if (filew) {
				var conimg = filew.document.forms['filelist'].conimg;
				if (conimg) {
					document.forms['adminForm'].elements['picture'].value = conimg.value;
				}
			}
		}
		
		function checkState(checkboxname)
		{
	  
			if (checkboxname.checked == false) {
		 		checkboxname.checked = false;
			} 
		}
		</script>
		
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		 <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		 <input type="hidden" name="option" value="<?php echo $option; ?>" />
		 <input type="hidden" name="task" value="save" />
		 <input type="hidden" name="type" value="<?php echo $type ?>" />

		 <div class="col width-50">
			<fieldset class="adminform">
				<legend><?php echo JText::_('FEEDBACK_DETAILS'); ?></legend>
		
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="save_for"> <?php if(!$row->id) { echo JText::_('FEEDBACK_CHOOSE_WHERE_TO_SAVE'); } else { echo JText::_('FEEDBACK_SAVE_QUOTE'); } ?> <span class="required">*</span></label></label></td> 
							<td>
<?php if ($type == 'regular') { ?>
								<input type="checkbox" name="replacequote" id="replacequote" value="1" checked="checked" /> <?php if ($row->id) { echo JText::_('FEEDBACK_REPLACE_ORIGINAL_QUOTE'); } else {echo JText::_('FEEDBACK_SAVE_IN_ARCHIVE');} ?>  <br />
<?php } ?>
								<input type="checkbox" name="notable_quotes" id="notable_quotes" value="1" <?php if ($type =='selected' && $row->notable_quotes == 1)  { echo 'checked="checked"'; } ?> /> <?php echo JText::_('FEEDBACK_SELECT_FOR_QUOTES'); ?> <br />
								<input type="checkbox" name="flash_rotation" id="flash_rotation" value="1" <?php if ($type =='selected' && $row->flash_rotation == 1)  { echo 'checked="checked"'; } ?> /> <?php echo JText::_('FEEDBACK_SELECT_FOR_ROTATION'); ?>
							</td>
						</tr>
						<tr>
							<td class="key"><label for="fullname"><?php echo JText::_('FEEDBACK_FULL_NAME'); ?>: <span class="required">*</span></label></td>
							<td><input type="text" name="fullname" id="fullname" value="<?php echo stripslashes($row->fullname); ?>" size="50" /></td>
						</tr>
						<tr>
							<td class="key"><label for="org"><?php echo JText::_('FEEDBACK_ORGANIZATION'); ?>:</label></td>
							<td><input type="text" name="org" id="org" value="<?php echo stripslashes($row->org); ?>" size="50" /></td>
						</tr>	
						<tr>
							<td class="key" valign="top"><label for="userid"><?php echo JText::_('FEEDBACK_USER_ID'); ?>:</label></td>
							<td>
								<input type="text" name="userid" id="userid" value="<?php echo stripslashes($row->userid); ?>" size="50" <?php if($row->id && $row->userid!=0) { echo 'disabled="disabled"'; } ?> />
								<?php
									if (!$row->id) {
										echo '<p>'.JText::_('FEEDBACK_USER_ID_EXPLANATION').'</p>';
									}
								?>
							</td>
						</tr>
<?php if ($type == 'regular') { ?>
						<tr>
							<td class="key"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENTS'); ?>:</td>
							<td>
								<input type="checkbox" name="publish_ok" id="publish_ok" value="1" <?php if ($row->publish_ok == 1) { echo ' checked="checked"'; } if($row->id) { echo ("disabled"); } ?>  />
								<label for="publish_ok"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?></label><br />
				
								<input type="checkbox" name="contact_ok" id="contact_ok" value="1" <?php if ($row->contact_ok == 1) { echo ' checked="checked"'; } if($row->id) { echo ("disabled"); } ?> />
								<label for="contact_ok"><?php echo JText::_('FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?></label>
							</td>
						</tr>
<?php } else {  ?>
						<tr>
							<td class="key" valign="top"><label for="short_quote"><?php echo JText::_('FEEDBACK_SHORT_QUOTE'); ?>:</label></td>
							<td>
								<input type="hidden" name="publish_ok" id="publish_ok" value="1" />
								<input type="hidden" name="contact_ok" id="contact_ok" value="1" />
								<p><?php echo JText::_('FEEDBACK_SHORT_QUOTE_NOTE'); ?></p>
								<?php echo $editor->display('short_quote', $short_quote, '360px', '200px', '40', '10'); ?>
							</td>
						</tr>
                        <tr>
							<td class="key" valign="top"><label for="miniquote"><?php echo JText::_('Mini Quote'); ?>:</label></td>
							<td>
								<input type="text" name="miniquote" id="miniquote" value="<?php echo $miniquote; ?>"  size="150" />
								<p><?php echo JText::_('Mini quote is limited to 150 characters to appear on frontpage random quote module'); ?></p>
							</td>
						</tr>
<?php } ?>
						<tr>
							<td class="key" valign="top"><label for="quote"><?php echo JText::_('FEEDBACK_FULL_QUOTE'); ?>: <span class="required">*</span></label></td>
							<td><?php echo $editor->display('quote',  stripslashes($row->quote) , '350px', '200px', '50', '10' ); ?></td>
						</tr>
						<tr>
							<td class="key"><label for="date"><?php echo JText::_('FEEDBACK_QUOTE_SUBMITTED'); ?>:</label></td>
							<td><input type="text" name="date" id="date" value="<?php echo $row->date; ?>"  size="50" /></td>
						</tr>
						<tr>
							<td class="key" valign="top"><label for="notes"><?php echo JText::_('FEEDBACK_EDITOR_NOTES'); ?>:</label> <p><?php echo JText::_('FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?></p></td>
							<td><?php echo $editor->display('notes',  stripslashes($row->notes) , '350px', '200px', '50', '10' ); ?></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col width-50">
				<fieldset class="adminform">
					<legend><?php echo JText::_('FEEDBACK_PICTURE'); ?></legend>
<?php
			if ($row->id != 0) {
				$pics = stripslashes($row->picture);
				$pics = explode('/', $pics);
				$file = end($pics);
?>
			<input type="hidden" name="picture" value="<?php echo $row->picture; ?>" />
			<iframe width="100%" height="350" name="filer" id="filer" frameborder="0" src="index3.php?option=com_feedback&amp;task=img&amp;file=<?php echo $file; ?>&amp;id=<?php echo $row->userid; ?>"></iframe>
<?php
			} else {
				echo '<p class="alert">'.JText::_('FEEDBACK_MUST_BE_SAVED_BEFORE_PICTURE').'</p>';
			}
?>
				</fieldset>
			</div>
			<div class="clr"></div>
		</form>
		<?php
	}
	
	//-----------
	
	public function writeImage( $app, $option, $webpath, $defaultpic, $path, $file, $file_path, $id, $errors=array() )
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('FEEDBACK_PICTURE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
	<script type="text/javascript">
	<!--
	function passparam()
	{
		parent.document.getElementById('adminForm').picture.value = this.document.forms[0].conimg.value;
	}
	
	window.onload = passparam;
	//-->
	</script>
 </head>
 <body>
   <form action="index2.php" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
	<table class="formed">
	 <thead>
	  <tr>
	   <th><label for="image"><?php echo JText::_('UPLOAD'); ?> <?php echo JText::_('WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
	  </tr>
	 </thead>
	 <tbody>
	  <tr>
	   <td>
	    <input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="task" value="upload" />
		
		<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
		<input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
	   </td>
	  </tr>
	 </tbody>
	</table>
	<?php
		if (count($errors) > 0) {
			echo FeedbackHtml::error( implode('<br />',$errors) ).n;
		}
	?>
	<table class="formed">
	 <thead>
	  <tr>
	   <th colspan="4"><label for="image"><?php echo JText::_('FEEDBACK_PICTURE'); ?></label></th>
	  </tr>
	 </thead>
	 <tbody>
<?php
	$k = 0;

	if ($file && file_exists( $file_path.DS.$file )) {
		$this_size = filesize($file_path.DS.$file);
		list($width, $height, $type, $attr) = getimagesize($file_path.DS.$file);
?>
	  <tr>
	   <td rowspan="6">
		<img src="<?php echo '../'.$webpath.DS.$path.DS.$file; ?>" alt="<?php echo JText::_('FEEDBACK_PICTURE'); ?>" id="conimage" />
		<input type="hidden" name="conimg" value="<?php echo $webpath.DS.$path.DS.$file; ?>" />
	   </td>
	   <td><?php echo JText::_('FILE'); ?>:</td>
	   <td><?php echo $file; ?></td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('SIZE'); ?>:</td>
	   <td><?php echo FileUploadUtils::formatsize($this_size); ?></td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('WIDTH'); ?>:</td>
	   <td><?php echo $width; ?> px</td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('HEIGHT'); ?>:</td>
	   <td><?php echo $height; ?> px</td>
	  </tr>
	  <tr>
	   <td><input type="hidden" name="currentfile" value="<?php echo $file; ?>" /></td>
	   <td><a href="index3.php?option=<?php echo $option; ?>&amp;task=deleteimg&amp;file=<?php echo $file; ?>&amp;id=<?php echo $id; ?>">[ <?php echo JText::_('DELETE'); ?> ]</a></td>
	  </tr>
<?php } else { ?>
	  <tr>
	   <td colspan="4"><img src="<?php echo '..'.$defaultpic; ?>" alt="<?php echo JText::_('NO_MEMBER_PICTURE'); ?>" />
		<input type="hidden" name="currentfile" value="" /></td>
	  </tr>
<?php } ?>
	 </tbody>
	</table>
   </form>
 </body>
</html>
<?php
	}
}
?>