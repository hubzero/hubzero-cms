<?php 
defined('_JEXEC') or die( 'Restricted access' );
$juser =& JFactory::getUser();
?>

<div id="content-header">
	<h2><?php echo stripslashes($this->forum->topic); ?></h2>
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
<div class="main section">
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=topic&topic='.$this->forum->id); ?>" method="post">
	<!--<h3><?php echo stripslashes($this->forum->topic); ?></h3>-->
	
	<div class="aside">
		<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=addtopic'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?></a></p>
	</div><!-- / .aside -->
	
	<div class="subject">
		<ol class="comments">
			<?php
			if ($this->rows) {
				ximport('Hubzero_User_Profile');
				ximport('Hubzero_Wiki_Parser');

				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => 'forum',
					'pagename' => 'forum',
					'pageid'   => $this->forum->id,
					'filepath' => '',
					'domain'   => $this->forum->id
				);

				$p =& Hubzero_Wiki_Parser::getInstance();

				foreach ($this->rows as $row)
				{
					$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
					$huser = '';
					if (!$row->anonymous) {
						$huser = new Hubzero_User_Profile();
						$huser->load( $row->created_by );
						if (is_object($huser) && $huser->get('name')) {
							$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by).'">'.stripslashes($huser->get('name')).'</a>';
						}
					}

					$comment = $p->parse( "\n".stripslashes($row->comment), $wikiconfig );
			?>
				<li class="comment" id="c<?php echo $row->id; ?>">
					<a name="c<?php echo $row->id; ?>"></a>
					<p class="comment-member-photo">
						<img src="<?php echo ForumHelper::getMemberPhoto($huser, $row->anonymous); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=topic&topic='.$this->forum->id.'#c'.$row->id); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_PERMALINK'); ?>">@
								<span class="time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on 
								<span class="date"><?php echo JHTML::_('date',$row->created, '%d %b, %Y', 0); ?></span>
								<?php if ($row->modified && $row->modified != '0000-00-00 00:00:00') { ?>
									&mdash; <?php echo JText::_('Edited @'); ?>
									<span class="time"><?php echo JHTML::_('date',$row->modified, '%I:%M %p', 0); ?></span> on 
									<span class="date"><?php echo JHTML::_('date',$row->modified, '%d %b, %Y', 0); ?></span>
								<?php } ?>
							</a>
						</p>
						<?php echo $comment; ?>
						
						<?php if ($this->authorized == 'admin' || $juser->get('id') == $row->created_by) { ?>
							<p class="comment-options">
								<?php if ($this->authorized == 'admin') { ?>
									<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=deletetopic&topic='.$row->id); ?>">Delete</a> | 
								<?php } ?>
								<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=edittopic&topic='.$row->id); ?>">Edit</a>
							</p>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		<?php } else { ?>
			<li><p><?php echo JText::_('PLG_GROUPS_FORUM_NO_REPLIES_FOUND'); ?></p></li>
		<?php } ?>
		
		</ol>
	    <?php 
            // @FIXME: Nick's Fix Based on Resources View
            //echo '<input type="hidden" name="topic" value="' . $this->forum->id . '" />';
            //$pf = $this->pageNav->getListFooter();
            //$nm = str_replace('com_','',$this->option);
            //$pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->_element.'/?topic='. $this->forum->id . '&',$pf);
            //echo $pf;
            //echo $this->pageNav->getListFooter(); 
            // @FIXME: End Nick's Fix
        ?>
	</div><!-- / .subject -->
</form>
<div class="clear"></div>	

<h3><a name="commentform"></a><?php echo JText::_('PLG_GROUPS_FORUM_ADD_COMMENT'); ?></h3>		
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
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_FORM_COMMENTS'); ?>
					<textarea name="topic[comment]" id="forum_comments" rows="15" cols="35"></textarea>
				</label>
		
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="topic[anonymous]" id="forum_anonymous" value="1" /> 
					<?php echo JText::_('PLG_GROUPS_FORUM_FORM_ANONYMOUS'); ?>
				</label>
		
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } ?>
		
			<div class="sidenote">
				<p>
					<strong>Please keep comments polite and on topic. Offensive posts may be removed.</strong>
				</p>
				<p>
					Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.
				</p>
			</div>
		</fieldset>
		<input type="hidden" name="topic[parent]" value="<?php echo $this->forum->id; ?>" />
		<input type="hidden" name="topic[id]" value="" />
	
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="savetopic" />
	</form>
</div><!-- / .subject -->
</div>