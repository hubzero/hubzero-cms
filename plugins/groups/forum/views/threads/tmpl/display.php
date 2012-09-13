<?php 
defined('_JEXEC') or die('Restricted access');
$juser = JFactory::getUser();

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

ximport('Hubzero_User_Profile_Helper');
?>
<div id="content-header-extra">
	<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias); ?>"><?php echo JText::_('&larr; All discussions'); ?></a></p>
</div>

<div class="main section">
	<h3 class="thread-title">
		<?php echo $this->escape(stripslashes($this->post->title)); ?>
	</h3>

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<div class="aside">
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_ALL_TAGS'); ?><span class="starter-point"></span></h4>
<?php if ($this->tags) { ?>
			<?php echo $this->tags; ?>
<?php } else { ?>
			<p><?php echo JText::_('PLG_GROUPS_FORUM_NONE'); ?></p>
<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_PARTICIPANTS'); ?><span class="starter-point"></span></h4>
<?php if ($this->participants) { ?>
			<ul>
<?php 
	$anon = false;
	foreach ($this->participants as $participant) 
	{ 
		if (!$participant->anonymous) { 
?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $participant->created_by); ?>"><?php echo $this->escape(stripslashes($participant->name)); ?></a></li>
<?php 
		} else if (!$anon) {
			$anon = true;
?>
				<li><?php echo JText::_('PLG_GROUPS_FORUM_ANONYMOUS'); ?></li>
<?php
		}
	}
?>
			</ul>
<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_ATTACHMENTS'); ?><span class="starter-point"></span></h4>
<?php if ($this->attachments) { ?>
			<ul class="attachments">
<?php 
			foreach ($this->attachments as $attachment) 
			{
				$title = ($attachment->description) ? $attachment->description : $attachment->filename;
?>
				<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $attachment->parent . '/' . $attachment->post_id . '/' . $attachment->filename); ?>"><?php echo $this->escape($title); ?></a></li>
<?php 		} ?>
			</ul>
<?php } ?>
		</div><!-- / .container -->
	</div><!-- / .aside  -->

	<div class="subject">
		<h4 class="comments-title">
			<?php echo JText::_('PLG_GROUPS_FORUM_COMMENTS'); ?>
		</h4>
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $this->post->id); ?>" method="get">
		<ol class="comments">
			<?php
			if ($this->rows) {
				ximport('Hubzero_User_Profile');
				ximport('Hubzero_Wiki_Parser');

				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => 'forum',
					'pagename' => 'forum',
					'pageid'   => $this->post->id,
					'filepath' => '',
					'domain'   => $this->post->id
				);

				$p =& Hubzero_Wiki_Parser::getInstance();

				foreach ($this->rows as $row)
				{
					$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
					$huser = '';
					if (!$row->anonymous) 
					{
						$huser = Hubzero_User_Profile::getInstance($row->created_by);
						if (is_object($huser) && $huser->get('name')) 
						{
							$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
						}
					}

					$comment  = $p->parse("\n" . stripslashes($row->comment), $wikiconfig, false);
					$comment .= $this->attach->getAttachment(
						$row->id, 
						'index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $this->post->id . '/' . $row->id . '/', 
						$this->config
					);
					
					//$tags = $this->tag->get_tags_on_object($row->id, 0, 0, $row->created_by);
			?>
				<li class="comment<?php if (!$row->parent) { echo ' start'; } ?>" id="c<?php echo $row->id; ?>">
					<p class="comment-member-photo">
						<a class="comment-anchor" name="c<?php echo $row->id; ?>"></a>
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, $row->anonymous); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $this->post->id . '#c' . $row->id); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_PERMALINK'); ?>"><span class="comment-date-at">@</span> 
								<span class="time"><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
								<span class="date"><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></time></span>
								<?php if ($row->modified && $row->modified != '0000-00-00 00:00:00') { ?>
									&mdash; <?php echo JText::_('PLG_GROUPS_FORUM_EDITED'); ?>
									<span class="time"><time datetime="<?php echo $row->modified; ?>"><?php echo JHTML::_('date', $row->modified, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
									<span class="date"><time datetime="<?php echo $row->modified; ?>"><?php echo JHTML::_('date', $row->modified, $dateFormat, $tz); ?></time></span>
								<?php } ?>
							</a>
						</p>
						<?php echo $comment; ?>
						<?php if ($this->config->get('access-edit-thread') || $juser->get('id') == $row->created_by) { ?>
						<p class="comment-options">
							<?php if ($this->config->get('access-delete-thread')) { ?>
							<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $row->id . '/delete'); ?>">
								<?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?>
							</a>
							<?php } ?>
							<?php if ($this->config->get('access-edit-thread')) { ?>
							<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $row->id . '/edit'); ?>">
								<?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?>
							</a>
							<?php } ?>
						</p>
						<?php } ?>
					</div>
<?php /*if (count($tags) > 0) { ?>
					<div class="comment-tags">
						<p><?php echo JText::_('PLG_GROUPS_FORUM_TAGS'); ?>:</p>
						<?php echo $this->tag->buildCloud($tags); ?>
					</div><!-- / .comment-tags -->
<?php }*/ ?>
				</li>
			<?php } ?>
		<?php } else { ?>
			<li><p><?php echo JText::_('PLG_GROUPS_FORUM_NO_REPLIES_FOUND'); ?></p></li>
		<?php } ?>
		</ol>
<?php 
		$this->pageNav->setAdditionalUrlParam('gid', $this->group->get('cn'));
		$this->pageNav->setAdditionalUrlParam('active', 'forum');
		$this->pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->category->alias . '/' . $this->post->id);

		echo $this->pageNav->getListFooter();
?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->

<div class="below section">
	<h3 class="post-comment-title">
		<?php echo JText::_('PLG_GROUPS_FORUM_ADD_COMMENT'); ?>
	</h3>
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
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->alias . '/' . $this->post->id); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
<?php
				$anon = 1;
				$jxuser = Hubzero_User_Profile::getInstance($juser->get('id'));
				if (!$juser->get('guest')) 
				{
					$anon = 0;
				}
				$now = date('Y-m-d H:i:s', time());
?>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, $anon); ?>" alt="" />
			</p>
	
			<fieldset>
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('PLG_GROUPS_FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
					</strong> 
					<span class="permalink"><span class="comment-date-at">@</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></time></span>
					</span>
				</p>
				
				<label for="field_comment">
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'field_comment', '', '', '35', '15');
					?>
				</label>
				
				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_YOUR_TAGS'); ?>:
<?php 
		$tags = $this->tModel->get_tag_string($this->post->id, 0, 0, $juser->get('id'));
		
		JPluginHelper::importPlugin('hubzero');
		$dispatcher = JDispatcher::getInstance();
		$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $tags)) );
		if (count($tf) > 0) {
			echo $tf[0];
		} else {
			echo '<input type="text" name="tags" value="' . $tags . '" />';
		}
?>
				</label>
				
				<fieldset>
					<legend><?php echo JText::_('PLG_GROUPS_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>
		
				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_ANONYMOUS'); ?>
				</label>
		
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } ?>
		
				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('PLG_GROUPS_FORUM_KEEP_POLITE'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('PLG_GROUPS_FORUM_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->category_id; ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[group_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
			<input type="hidden" name="fields[id]" value="" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->