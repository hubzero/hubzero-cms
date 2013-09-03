<?php 
defined('_JEXEC') or die('Restricted access');

	ximport('Hubzero_User_Profile');
	
	$juser = JFactory::getUser();
	
	ximport('Hubzero_User_Profile_Helper');

	$dateFormat = '%d %b %Y';
	$timeFormat = '%I:%M %p';
	$tz = 0;
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateFormat = 'd M Y';
		$timeFormat = 'H:i p';
		$tz = true;
	}

	$name = JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
	$huser = '';
	if (!$this->comment->anonymous) 
	{
		$huser = Hubzero_User_Profile::getInstance($this->comment->created_by);
		if (is_object($huser) && $huser->get('name')) 
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $this->comment->created_by) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
		}
	}

	$cls = isset($this->cls) ? $this->cls : 'odd';
	if (!$this->comment->anonymous && $this->course->offering()->member($this->comment->created_by)->get('id'))
	{
		if (!$this->course->offering()->member($this->comment->created_by)->get('student')) 
		{
			$cls .= ' ' . strtolower($this->course->offering()->member($this->comment->created_by)->get('role_alias'));
		} 
		else if (!$this->course->offering()->access('manage') && $this->course->offering()->access('manage', 'section')) 
		{
			$cls .= ' ' . strtolower($this->course->offering()->member($this->comment->created_by)->get('role_alias'));
		}
	}
	if (isset($this->comment->treename))
	{
		$cls .= ' ' . $this->comment->treename;
	}

	if ($this->comment->reports)
	{
		$comment = '<p class="warning">' . JText::_('This comment has been reported as abusive and/or containing inappropriate content.') . '</p>';
	}
	else
	{
		$base = preg_replace('/search=(.*?)($|&)/', '', $this->base);
		$comment  = $this->parser->parse(stripslashes($this->comment->comment), $this->wikiconfig, false);
		$comment .= $this->attach->getAttachment(
			$this->comment->id, 
			str_replace('outline', 'discussions', $base) . '&unit=download&b=' . $this->comment->thread . '&file=', 
			$this->config
		);
		if ($this->search)
		{
			$comment = preg_replace('#' . $this->search . '#i', "<span class=\"highlight\">\\0</span>", $comment);
		}
	}

	if ($this->unit)
	{
		$this->base .= '&unit=' . $this->unit;
	}
	if ($this->lecture)
	{
		$this->base .= '&b=' . $this->lecture;
	}
?>
	<li class="comment <?php echo $cls; ?><?php if (!$this->comment->parent) { echo ' start'; } ?>" id="c<?php echo $this->comment->id; ?>">
		<p class="comment-member-photo">
			<a class="comment-anchor" name="c<?php echo $this->comment->id; ?>"></a>
			<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, $this->comment->anonymous); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong> 
				<a class="permalink" href="<?php echo JRoute::_($this->base . '#c' . $this->comment->id); ?>" title="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_PERMALINK'); ?>"><span class="comment-date-at">@</span> 
					<span class="time"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, $dateFormat, $tz); ?></time></span>
					<?php if ($this->comment->modified && $this->comment->modified != '0000-00-00 00:00:00') { ?>
						&mdash; <?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDITED'); ?>
						<span class="time"><time datetime="<?php echo $this->comment->modified; ?>"><?php echo JHTML::_('date', $this->comment->modified, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $this->comment->modified; ?>"><?php echo JHTML::_('date', $this->comment->modified, $dateFormat, $tz); ?></time></span>
					<?php } ?>
				</a>
			<?php /*if (!$this->comment->anonymous && $this->course->offering()->member($this->comment->created_by)->get('id') && !$this->course->offering()->member($this->comment->created_by)->get('student')) { ?>
				<span class="role <?php echo strtolower($this->course->offering()->member($this->comment->created_by)->get('role_alias')); ?>">
					<?php echo $this->escape(stripslashes($this->course->offering()->member($this->comment->created_by)->get('role_title'))); ?>
				</span>
			<?php }*/ ?>
			<?php if (!$this->comment->anonymous && $this->course->offering()->member($this->comment->created_by)->get('id')) { ?>
				<?php if (!$this->course->offering()->member($this->comment->created_by)->get('student')) { ?>
				<span class="role <?php echo strtolower($this->course->offering()->member($this->comment->created_by)->get('role_alias')); ?>">
					<?php echo $this->escape(stripslashes($this->course->offering()->member($this->comment->created_by)->get('role_title'))); ?>
				</span>
				<?php } else if (!$this->course->offering()->access('manage') && $this->course->offering()->access('manage', 'section')) { ?>
					<span class="role <?php echo strtolower($this->course->offering()->member($this->comment->created_by)->get('role_alias')); ?>">
						<?php echo $this->escape(stripslashes($this->course->offering()->member($this->comment->created_by)->get('role_title'))); ?>
					</span>
				<?php } ?>
			<?php } ?>
			</p>
			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
			<?php if ($this->config->get('access-edit-thread')) { // || $juser->get('id') == $this->comment->created_by ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($this->base . '&action=delete&post=' . $this->comment->id . '&thread=' . $this->comment->thread); ?>"><!-- 
						--><?php echo JText::_('PLG_COURSES_DISCUSSIONS_DELETE'); ?><!-- 
					--></a>
				<?php } ?>
				<?php /*if ($this->config->get('access-edit-thread')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->base . '&action=edit&post=' . $this->comment->id . '&thread=' . $this->comment->thread); ?>"><!-- 
						--><?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDIT'); ?><!-- 
					--></a>
				<?php }*/ ?>
			<?php } ?>
			<?php if (!$this->comment->reports) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (JRequest::getInt('reply', 0) == $this->comment->id) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_REPLY'); ?>" href="<?php echo JRoute::_($this->base); ?>" rel="comment-form<?php echo $this->comment->id; ?>"><!-- 
					--><?php echo JText::_('PLG_COURSES_DISCUSSIONS_CANCEL'); ?><!-- 
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_REPLY'); ?>" href="<?php echo JRoute::_($this->base . '&reply=' . $this->comment->id); ?>" rel="comment-form<?php echo $this->comment->id; ?>"><!-- 
					--><?php echo JText::_('PLG_COURSES_DISCUSSIONS_REPLY'); ?><!-- 
				--></a>
					<?php } ?>
				<?php } ?>
				<a class="icon-abuse abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=forum&id=' . $this->comment->id . '&parent=' . $this->comment->parent); ?>" rel="comment-form<?php echo $this->comment->id; ?>"><!-- 
					--><?php echo JText::_('PLG_COURSES_DISCUSSIONS_REPORT_ABUSE'); ?><!-- 
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="comment-add<?php if (JRequest::getInt('reply', 0) != $this->comment->id) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->id; ?>">
				<form id="cform<?php echo $this->comment->id; ?>" action="<?php echo JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture); ?>" method="post" enctype="multipart/form-data">
					<a name="commentform<?php echo $this->comment->id; ?>"></a>
					<fieldset>
						<legend><span><?php echo JText::sprintf('PLG_COURSES_DISCUSSIONS_REPLYING_TO', (!$this->comment->anonymous ? $name : JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="fields[id]" value="0" />
						<input type="hidden" name="fields[state]" value="1" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->post->get('scope'); ?>" />
						<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->get('category_id'); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->post->get('scope_id'); ?>" />
						<input type="hidden" name="fields[scope_sub_id]" value="<?php echo $this->post->get('scope_sub_id'); ?>" />
						<input type="hidden" name="fields[object_id]" value="<?php echo $this->post->get('object_id'); ?>" />
						<input type="hidden" name="fields[parent]" value="<?php echo $this->comment->id; ?>" />
						<input type="hidden" name="fields[thread]" value="<?php echo $this->comment->thread; ?>" />
						<input type="hidden" name="fields[created]" value="" />
						<input type="hidden" name="fields[created_by]" value="<?php echo $juser->get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
						<input type="hidden" name="active" value="discussions" />
						<input type="hidden" name="action" value="savethread" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_($this->base)); ?>" />

						<label for="comment-<?php echo $this->comment->id; ?>-content">
							<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
							<?php
							ximport('Hubzero_Wiki_Editor');
							$editor = Hubzero_Wiki_Editor::getInstance();
							echo $editor->display('fields[comment]', 'field_' . $this->comment->id . '_comment', '', 'minimal no-footer', '35', '4');
							?>
						</label>

						<label class="upload-label" for="comment-<?php echo $this->comment->id; ?>-file">
							<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ATTACH_FILE'); ?>:</span>
							<input type="file" name="upload" id="comment-<?php echo $this->comment->id; ?>-file" />
						</label>

						<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->id; ?>-anonymous">
					<?php if ($this->config->get('comments_anon', 1)) { ?>
							<input class="option" type="checkbox" name="fields[anonymous]" id="comment-<?php echo $this->comment->id; ?>-anonymous" value="1" /> 
							<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
					<?php } else { ?>
							&nbsp; <input class="option" type="hidden" name="fields[anonymous]" value="0" /> 
					<?php } ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" /> 
						</p>
					</fieldset>
				</form>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		//if ($this->comment->replies && $this->depth < $this->config->get('comments_depth', 3)) 
		if ($this->depth < $this->config->get('comments_depth', 3)) 
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => 'discussions',
					'name'    => 'threads',
					'layout'  => 'list'
				)
			);
			$view->parent     = $this->comment->id;
			$view->thread     = $this->comment->thread;
			$view->option     = $this->option;
			$view->comments   = $this->comment->replies;
			$view->post       = $this->post;
			$view->unit       = $this->unit;
			$view->lecture    = $this->lecture;
			$view->config     = $this->config;
			$view->depth      = $this->depth;
			$view->cls        = $cls;
			$view->base       = $this->base;
			$view->parser     = $this->parser;
			$view->wikiconfig = $this->wikiconfig;
			$view->attach     = $this->attach;
			$view->course     = $this->course;
			$view->search     = $this->search;
			$view->display();
		}
		?>
	</li>