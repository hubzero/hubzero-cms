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

class AnswersHtml 
{
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function shortenText($text, $chars=300) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
		}

		return $text;
	}

	//-----------
	
	public function questions( &$database, &$rows, &$pageNav, $option, $filters ) 
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
					Filter by:
					<select name="filterby" onchange="document.adminForm.submit( );">
						<option value="open"<? if($filters['filterby'] == 'open') { echo ' selected="selected"'; } ?>>Open Questions</option>
						<option value="closed"<? if($filters['filterby'] == 'closed') { echo ' selected="selected"'; } ?>>Closed Questions</option>
						<option value="all"<? if($filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>>All Questions</option>
					</select>
				</label> 

				<label>
					Sort by:
					<select name="sortby" onchange="document.adminForm.submit( );">
						<option value="rewards"<? if($filters['sortby'] == 'rewards') { echo ' selected="selected"'; } ?>>Rewards</option>
						<option value="votes"<? if($filters['sortby'] == 'votes') { echo ' selected="selected"'; } ?>>Recommendations</option>
                        <option value="date"<? if($filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>>Date</option>
					</select>
				</label> 
			</fieldset>
		
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th>ID</th>
                        <th>Subject</th>
						<th>State</th>
						<th>Created</th>
						<th>Created by</th>
						<th>Answers</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			switch($row->state) 
			{
				case '1':
					$task = 'open';
					$img = 'publish_x.png';
					$alt = JText::_( 'Closed' );
					break;
				case '0':
					$task = 'close';
					$img = 'publish_g.png';
					$alt = JText::_( 'Open' );
					break;
			}
?>
					<tr class="<?php echo "row$k"; ?>">
                    	<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><?php echo $row->id ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editq&amp;id[]=<?php echo $row->id; ?>" title="Edit this question"><?php echo stripslashes($row->subject); ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>" title="Set this to <?php echo $task;?>"><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span></a></td>
						<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y') ?></td>
						<td><?php echo $row->created_by; if ($row->anonymous) { echo ' (anon)'; } ?></td>
<?php if ($row->answers > 0) { ?>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=answers&amp;qid=<? echo $row->id; ?>" title="View the answers for this Question"><?php echo $row->answers; ?> response<?php if($row->answers != 1) { echo 's'; } ?></a></td>
<?php } else { ?>
						<td><?php echo $row->answers; ?></td>
<?php } ?>
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
		</form>

		<p>State: (click icon above to toggle state)</p>
		<ul class="key">
			<li class="published"><img src="images/publish_g.png" width="16" height="16" border="0" alt="Open" /> = Open Question</li>
			<li class="unpublished"><img src="images/publish_x.png" width="16" height="16" border="0" alt="Closed" /> = Closed Question</li>
		</ul>
		<?php
	}

	//-----------
	
	public function answers( $parent, &$rows, &$pageNav, $option, $filters, $qid ) 
	{
		AnswersHTML::menutop();
		?>     
			<p id="submenu">
			 	<a href="index2.php?option=<?php echo $option; ?>" class="active" style="border-right:none;">Questions</a>:
                <a href="index2.php?option=<?php echo $option; ?>&amp;task=editq&amp;id[]=<?php echo $qid; ?>" title="Edit this question"><?php echo stripslashes($parent); ?></a>
			</p>
       <?php   AnswersHTML::menubottom(); ?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
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
					Filter by:
					<select name="filterby" onchange="document.adminForm.submit( );">
						<option value="all"<? if($filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>>All Responses</option>
						<option value="accepted"<? if($filters['filterby'] == 'accepted') { echo ' selected="selected"'; } ?>>Accepted Response</option>
						<option value="rejected"<? if($filters['filterby'] == 'rejected') { echo ' selected="selected"'; } ?>>Unaccepted Responses</option>
					</select>
				</label> 

				<label>
					Sort by:
					<select name="sortby" onchange="document.adminForm.submit( );">
						<option value="m.title"<? if($filters['sortby'] == 'm.title') { echo ' selected="selected"'; } ?>>Subject</option>
						<option value="m.id DESC"<? if($filters['sortby'] == 'm.id DESC') { echo ' selected="selected"'; } ?>>ID number</option>
						<option value="m.created_by"<? if($filters['sortby'] == 'm.created_by') { echo ' selected="selected"'; } ?>>Creator</option>
					</select>
				</label> 
			</fieldset>
		
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th>Answer</th>
						<th>State</th>
						<th>Created</th>
						<th>Created by</th>
						<th>Helpful</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];

			switch($row->state) 
			{
				case '1':
					$task = 'reject';
					$img = 'publish_g.png';
					$alt = JText::_( 'Accepted' );
					break;
				case '0':
					$task = 'accept';
					$img = 'publish_x.png';
					$alt = JText::_( 'Unaccepted' );
					break;
					
				
			}
			
			$row->answer = stripslashes($row->answer);
			$row->answer = AnswersHtml::shortenText($row->answer, 75);
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edita&amp;id[]=<?php echo $row->id; ?>" title="Edit this Answer"><?php echo $row->answer; ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;qid=<?php echo $qid; ?>" title="Set this to <?php echo $task;?>"><span><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span></a></td>
						<td><?php echo $row->created; ?></td>
						<td><?php echo $row->created_by; if($row->anonymous) { echo ' (anon)'; } ?></td>
						<td>+<?php echo $row->helpful; ?> -<?php echo $row->nothelpful; ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>

			<input type="hidden" name="qid" value="<?php echo $qid ?>" />
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="answers" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>

		<p>State: (click icon above to toggle state)</p>
		<ul class="key">
			<li class="published"><img src="images/publish_g.png" width="16" height="16" border="0" alt="Accepted" /> = Accepted Answer</li>
			<li class="unpublished"><img src="images/publish_x.png" width="16" height="16" border="0" alt="Unaccepted" /> = Unaccepted Answer</li>
		</ul>
		<?php
	}

	//-----------
	
	public function editQuestion( &$row, $option, $tags ) 
	{
		$create_date = NULL;
		if (intval( $row->created ) <> 0) {
			$create_date = JHTML::_('date', $row->created );
		}
		
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		?>
		<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-mos.css" title="green" />
		<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
		<script type="text/javascript" src="../includes/js/calendar/lang/calendar-en.js"></script>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.subject.value == ''){
				alert( 'Question must have a subject' );
			} else if (form.tags.value == ''){
				alert( 'Question must have at least one tag' );
			} else {
				
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" class="editform">
			<div class="col50">
			<fieldset class="adminform">
				<legend>Details</legend>
				<table class="admintable">
				 <tbody>
				  <tr>
				   <td class="key"><label>Anonymous:</label></td>
				   <td><input type="checkbox" name="anonymous" value="1" <?php echo ($row->anonymous) ? 'checked="checked"' : ''; ?> /> Hide your name</td>
				  </tr>
				  <tr>
				   <td class="key"><label>Notify:</label></td>
				   <td><input type="checkbox" name="email" value="1" <?php echo ($row->email) ? 'checked="checked"' : ''; ?> /> Send e-mail when someone posts a response</td>
				  </tr>
				  <tr>
				   <td class="key"><label>Subject: <span class="required">*</span></label></td>
				   <td><input type="text" name="subject" size="30" maxlength="250" value="<?php echo stripslashes($row->subject); ?>" /></td>
				  </tr>
				  <tr>
				   <td class="key" style="vertical-align:top;"><label>Question:</label></td>
				   <td><?php
					echo $editor->display('question', stripslashes($row->question), '360px', '200px', '50', '10');
					?></td>
				  </tr>
				  <tr>
				   <td class="key"><label>Tags: <span class="required">*</span></label></td>
				   <td><input type="text" name="tags" size="30" value="<?php echo $tags; ?>" /></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
			</div>
			<div class="col50">
			<fieldset class="adminform">
				<legend>Parameters</legend>

				<table class="admintable">
				 <tbody>
				  
				  <tr>
				   <td class="key"><label for="created_by">Change Creator:</label></td>
				   <td colspan="2"><input type="text" name="created_by" id="created_by" size="25" maxlength="50" value="<?php echo $row->created_by; ?>" /></td>
				  </tr>
				  <tr>
				   <td class="key"><label for="created">Created Date:</label></td>
				   <td><input type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" /></td>
				   <td><a class="icon_calendar" id="reset" title="View a calendar to select a date from" onclick="return showCalendar('created', 'y-mm-dd');">calendar</a></td>
				  </tr>
				  <tr>
				   <td class="key">State:</td>
				   <td colspan="2"><?php echo ($row->state == 1) ? 'Closed' : 'Open'; ?></td>
				  </tr>
				  <tr>
				   <td class="key">Created:</td>
				   <td colspan="2"><?php echo ($row->created != '0000-00-00 00:00:00') ? $create_date.'</td></tr><tr><td class="key">By:</td><td colspan="2">'.$row->created_by : 'New question'; ?></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
			</div>
			<div class="clr"></div>
            <input type="hidden" name="state" value="<?php echo $row->state; ?>" />
			<input type="hidden" name="qid" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="saveq" />
		</form>
		<?php
	}

	//-----------
	
	public function editAnswer( &$row, $question, $option, $qid ) 
	{
		$create_date = NULL;
		if (intval( $row->created ) <> 0) {
			$create_date = JHTML::_('date', $row->created );
		}
		
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		?>
		<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-mos.css" title="green" />
		<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
		<script type="text/javascript" src="../includes/js/calendar/lang/calendar-en.js"></script>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;

			if (pressbutton =='resethelpful') {
				if (confirm('Are you sure you want to reset the Helpful counts to zero? \nAny unsaved changes to this content will be lost.')){
					submitform( pressbutton );
					return;
				} else {
					return;
				}
			}

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.answer.value == ''){
				alert( 'Answer must have a response' );
			} else {
				<?php //getEditorContents( 'editor1', 'response' ) ; ?>
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" class="editform">
			<div class="col50">
			<fieldset class="adminform">
				<legend>Details</legend>
				<table class="admintable">
				 <tbody>
				  <tr>
				   <td class="key"><label>Anonymous:</label></td>
				   <td><input type="checkbox" name="anonymous" value="1" <?php echo ($row->anonymous) ? 'checked="checked"' : ''; ?> /></td>
				  </tr>
				  <tr>
				   <td class="key"><label>Question:</label></td>
				   <td><?php echo $question; ?></td>
				  </tr>
				  <tr>
				   <td class="key"><label>Answer</label></td>
				   <td><?php
					echo $editor->display('answer', stripslashes($row->answer), '360px', '200px', '50', '10');
					?></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
		</div>
		<div class="col50">
			<fieldset class="adminform">
				<legend>Parameters</legend>
				<table class="admintable">
				 <tbody>
				  <tr>
				   <td class="key"><label for="state">Accept:</label></td>
				   <td><input type="checkbox" name="state" id="state" value="1" <?php echo $row->state ? 'checked="checked"' : ''; ?> /></td>
				  </tr>
				  <tr>
				   <td class="key"><label for="created_by">Change Creator:</label></td>
				   <td colspan="2"><input type="text" name="created_by" id="created_by" size="25" maxlength="50" value="<?php echo $row->created_by; ?>" /></td>
				  </tr>
				  <tr>
				   <td class="key"><label for="created">Created Date:</label></td>
				   <td><input type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $row->created; ?>" /></td>
				   <td><a class="icon_calendar" id="reset" title="View a calendar to select a date from" onclick="return showCalendar('created', 'y-mm-dd');">calendar</a></td>
				  </tr>
				  <tr>
				   <td class="key"><label>State:</label></td>
				   <td><?php echo ($row->state == 1) ? 'Accepted answer' : 'Unaccepted'; ?></td>
				  </tr>
				  <tr>
				   <td class="key">Helpful:</td>
				   <td colspan="2">+<?php echo $row->helpful; ?> -<?php echo $row->nothelpful; ?>
					<?php if ( $row->helpful > 0 || $row->nothelpful > 0 ) { ?>
					<input type="button" name="reset_helpful" value="Reset Helpful" onclick="submitbutton('resethelpful');" />
					<?php } ?>
				   </td>
				  </tr>
				  <tr>
				   <td class="key">Created:</td>
				   <td colspan="2"><?php echo ($row->created != '0000-00-00 00:00:00') ? $create_date.'</td></tr><tr><td class="key">By:</td><td colspan="2">'.$row->created_by : 'New question'; ?></td>
				  </tr>
				 </tbody>
				</table>
			</fieldset>
			</div>
			<div class="clr"></div>
			
			<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savea" />
		</form>
		<?php
	}

	//-----------

	public function autop($pee, $br = 1) 
	{
		// converts paragraphs of text into xhtml
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		$pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little
		$pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little
		$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines 
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace 
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee); 
		if ($br) $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
		$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
		
		return $pee; 
	}

	//-----------
	
	public function unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', '', $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee; 
	}
	//-----------
	
	function menutop( ) 
	{
	?>
     <div id="submenu-box">
			<div class="t">
				<div class="t">
					<div class="t"></div>
		 		</div>
	 		</div>
			<div class="m">
	<?php
	}
	
	//-----------
	
	function menubottom( ) 
	{
	?>
	 <div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
		 			<div class="b"></div>
				</div>
			</div>
		</div>
		
				
		<div id="element-box">
			<div class="t">
		 		<div class="t">
					<div class="t"></div>
		 		</div>
			</div>
			<div class="m">
     <?php
	}
	
	//-----------
}
?>