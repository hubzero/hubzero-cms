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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->get('alias') . '/' . $this->thread->get('thread');

ximport('Hubzero_User_Profile_Helper');
?>
<ul id="page_options">
	<li>
		<a class="icon-comments comments btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->get('alias')); ?>">
			<?php echo JText::_('All discussions'); ?>
		</a>
	</li>
</ul>

<div class="main section">
	<h3 class="thread-title<?php echo ($this->thread->get('closed')) ? ' closed' : ''; ?>">
		<?php echo $this->escape(stripslashes($this->thread->get('title'))); ?>
	</h3>

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<div class="aside">
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_ALL_TAGS'); ?><span class="starter-point"></span></h4>
		<?php if ($this->thread->tags('cloud')) { ?>
			<?php echo $this->thread->tags('cloud'); ?>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_GROUPS_FORUM_NONE'); ?></p>
		<?php } ?>
		</div><!-- / .container -->

	<?php if ($this->thread->participants()->total() > 0) { ?>
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_PARTICIPANTS'); ?></h4>
			<ul>
			<?php 
				$anon = false;
				foreach ($this->thread->participants() as $participant) 
				{ 
					if (!$participant->anonymous) { 
			?>
				<li>
					<a class="member" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $participant->created_by); ?>">
						<?php echo $this->escape(stripslashes($participant->name)); ?>
					</a>
				</li>
			<?php 
					} else if (!$anon) {
						$anon = true;
			?>
				<li>
					<span class="member">
						<?php echo JText::_('PLG_GROUPS_FORUM_ANONYMOUS'); ?>
					</span>
				</li>
			<?php
					}
				}
			?>
			</ul>
	<?php } ?>
		</div><!-- / .container -->

	<?php if ($this->thread->attachments()->total() > 0) { ?>
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_ATTACHMENTS'); ?><span class="starter-point"></span></h4>
			<ul class="attachments">
			<?php 
			foreach ($this->thread->attachments() as $attachment) 
			{
				$cls = 'file';
				$title = $attachment->get('description', $attachment->get('filename'));
				if (preg_match("#bmp|gif|jpg|jpe|jpeg|png#i", $attachment->get('filename')))
				{
					$cls = 'img';
				}
			?>
				<li>
					<a class="<?php echo $cls; ?> attachment" href="<?php echo JRoute::_($base . '/' . $attachment->get('post_id') . '/' . $attachment->get('filename')); ?>">
						<?php echo $this->escape(stripslashes($title)); ?>
					</a>
				</li>
			<?php } ?>
			</ul>
		</div><!-- / .container -->
	<?php } ?>
	</div><!-- / .aside  -->

	<div class="subject">
		<!-- <h4 class="comments-title">
			<?php echo JText::_('PLG_GROUPS_FORUM_COMMENTS'); ?>
		</h4> -->
		<form action="<?php echo JRoute::_($base); ?>" method="get">
		<ol class="comments">
			<?php
			if ($this->thread->posts('list', $this->filters)->total() > 0) {
				ximport('Hubzero_User_Profile');

				foreach ($this->thread->posts() as $row)
				{
					$row->set('section', $this->filters['section']);
					$row->set('category', $this->category->get('alias'));

					$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
					$huser = '';
					if (!$row->get('anonymous')) 
					{
						$huser = Hubzero_User_Profile::getInstance($row->get('created_by'));
						if (is_object($huser) && $huser->get('name')) 
						{
							$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
						}
					}
			?>
				<li class="comment<?php if (!$row->get('parent')) { echo ' start'; } ?>" id="c<?php echo $row->get('id'); ?>">
					<p class="comment-member-photo">
						<a class="comment-anchor" name="c<?php echo $row->get('id'); ?>"></a>
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, $row->get('anonymous')); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_($base . '#c' . $row->get('id')); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_PERMALINK'); ?>"><span class="comment-date-at">@</span> 
								<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
								<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
								<?php if ($row->wasModified()) { ?>
									&mdash; <?php echo JText::_('PLG_GROUPS_FORUM_EDITED'); ?>
									<span class="time"><time datetime="<?php echo $row->modified(); ?>"><?php echo $row->modified('time'); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
									<span class="date"><time datetime="<?php echo $row->modified(); ?>"><?php echo $row->modified('date'); ?></time></span>
								<?php } ?>
							</a>
						</p>
						<?php echo $row->content('parsed'); ?>
						<?php if ($this->config->get('access-edit-thread') || $juser->get('id') == $row->get('created_by')) { ?>
						<p class="comment-options">
							<?php if ($this->config->get('access-delete-thread')) { ?>
							<a class="icon-delete delete" href="<?php echo JRoute::_($base . '/delete'); ?>"><!-- 
								--><?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?><!-- 
							--></a>
							<?php } ?>
							<?php if ($this->config->get('access-edit-thread')) { ?>
							<a class="icon-edit edit" href="<?php echo JRoute::_($base . '/edit'); ?>"><!-- 
								--><?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?><!-- 
							--></a>
							<?php } ?>
						</p>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		<?php } else { ?>
			<li>
				<p><?php echo JText::_('PLG_GROUPS_FORUM_NO_REPLIES_FOUND'); ?></p>
			</li>
		<?php } ?>
		</ol>
		<?php 
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				$this->thread->posts('count', $this->filters), 
				$this->filters['start'], 
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'forum');
			$pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->category->get('alias') . '/' . $this->thread->get('id'));

			echo $pageNav->getListFooter();
		?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
<?php if ($this->config->get('access-create-thread') && !$this->thread->get('closed')) { ?>
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
		<form action="<?php echo JRoute::_($base); ?>" method="post" id="commentform" enctype="multipart/form-data">
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
						$tags = $this->thread->tags('string');

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
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->thread->get('category_id'); ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->thread->get('id'); ?>" />
			<input type="hidden" name="fields[thread]" value="<?php echo $this->thread->get('id'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->model->get('scope'); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->model->get('scope_id'); ?>" />
			<input type="hidden" name="fields[id]" value="" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->
<?php } ?>