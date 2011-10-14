<?php 
defined('_JEXEC') or die( 'Restricted access' );

if ($this->row->parent) {
	$title = JText::_('PLG_GROUPS_FORUM_ADD_REPLY_TO_TOPIC');
} else {
	if ($this->row->id) {
		$title = JText::_('PLG_GROUPS_FORUM_EDIT_TOPIC');
		$editing = true;
	} else {
		$editing = false;
		$title = JText::_('PLG_GROUPS_FORUM_NEW_TOPIC');
	}
}

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
if (count($pathway->getPathWay()) <= 0) {
	$pathway->addItem(JText::_('Discussion Forum'), 'index.php?option='.$this->option);
	if($this->row->id) {
		$pathway->addItem(JText::_('Edit Topic'), 'index.php?option='.$this->option.'&task=edittopic&topic='.$this->row->id);
	} else {
		$pathway->addItem(JText::_('Add Topic'), 'index.php?option='.$this->option.'&task=addtopic');
	}

}
?>

<div id="content-header">
	<h2><?php echo $title; ?></h2>
</div>
<div id="content-header-extra">
	<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=topics'); ?>">Back to all discussions</a></p>
</div>
<br class="clear" />
<?php
	foreach($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

		
<div class="aside">
	<table class="wiki-reference" summary="Wiki Syntax Reference">
		<caption>Wiki Syntax Reference</caption>
		<tbody>
			<tr>
				<td>'''bold'''</td>
				<td><b>bold</b></td>
			</tr>
			<tr>
				<td>''italic''</td>
				<td><i>italic</i></td>
			</tr>
			<tr>
				<td>__underline__</td>
				<td><span style="text-decoration:underline;">underline</span></td>
			</tr>
			<tr>
				<td>{{{monospace}}}</td>
				<td><code>monospace</code></td>
			</tr>
			<tr>
				<td>~~strike-through~~</td>
				<td><del>strike-through</del></td>
			</tr>
			<tr>
				<td>^superscript^</td>
				<td><sup>superscript</sup></td>
			</tr>
			<tr>
				<td>,,subscript,,</td>
				<td><sub>subscript</sub></td>
			</tr>
		</tbody>
	</table>
</div><!-- /.aside -->

<div class="subject">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="commentform">
		<p class="comment-member-photo">
			<?php
				$juser =& JFactory::getUser();
				if (!$juser->get('guest')) {
					$jxuser = new Hubzero_User_Profile();
					$jxuser->load( $juser->get('id') );
					$thumb = ForumHelper::getMemberPhoto($jxuser, 0);
				} else {
					$config =& JComponentHelper::getParams( 'com_members' );
					$thumb = $config->get('defaultpic');
					if (substr($thumb, 0, 1) != DS) {
						$thumb = DS.$dfthumb;
					}
					$thumb = ForumHelper::thumbit($thumb);
				}
			?>
			<img src="<?php echo $thumb; ?>" alt="" />
		</p>
	
		<fieldset>
			<?php if ($this->row->parent) { ?>
				<input type="hidden" name="topic[sticky]" id="forum_sticky" value="<?php echo $this->row->sticky; ?>" />
				<input type="hidden" name="topic[topic]" id="forum_topic" value="<?php echo htmlentities(stripslashes($this->row->topic), ENT_QUOTES); ?>" />
			<?php } else { ?>
				<label>
					<input class="option" type="checkbox" name="topic[sticky]" value="1" id="forum_sticky"<?php if ($this->row->sticky == 1) { echo ' checked="checked"'; } ?> /> 
					<?php echo JText::_('PLG_GROUPS_FORUM_FORM_STICKY'); ?>
				</label>	
				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_FORM_TOPIC'); ?>
					<input type="text" name="topic[topic]" id="forum_topic" value="<?php echo htmlentities(stripslashes($this->row->topic), ENT_QUOTES); ?>" size="38" />
				</label>
			<?php } ?>
			
			<label>
				<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
				<textarea name="topic[comment]" id="forum_comments" rows="15" cols="35"><?php echo stripslashes($this->row->comment); ?></textarea>
			</label>
		
			<label id="comment-anonymous-label">
				<input class="option" type="checkbox" name="topic[anonymous]" id="forum_anonymous" value="1"<?php echo ($this->row->anonymous) ? ' checked="checked"' : ''; ?> /> 
				<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
			</label>
		
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
			</p>
		
			<div class="sidenote">
				<p>
					<strong>Please keep comments polite and on topic. Offensive posts may be removed.</strong>
				</p>
				<p>
					Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.
				</p>
			</div>
		</fieldset>
		<input type="hidden" name="topic[created]" value="<?php echo $this->row->created; ?>" />
		<input type="hidden" name="topic[created_by]" value="<?php echo $this->row->created_by; ?>" />
		<input type="hidden" name="topic[parent]" value="<?php echo $this->row->parent; ?>" />
		<input type="hidden" name="topic[id]" value="<?php echo $this->row->id; ?>" />
	
		<input type="hidden" name="editing" value="<?php echo $editing; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="savetopic" />
	</form>
</div><!-- / .subject -->