<?php 
defined('_JEXEC') or die('Restricted access');

	$juser = JFactory::getUser();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	if ($this->question->get('created_by') == $this->comment->get('created_by')) 
	{
		$cls .= ' author';
	}
	$cls .= ($this->comment->isReported()) ? ' abusive' : '';
	if ($this->comment->get('state') == 1)
	{
		$cls .= ' chosen';
	}

	$name = JText::_('COM_ANSWERS_ANONYMOUS');
	$huser = new Hubzero_User_Profile;
	if (!$this->comment->get('anonymous')) 
	{
		$huser = $this->comment->creator(); //Hubzero_User_Profile::getInstance($this->comment->get('created_by'));
		if (is_object($huser) && $huser->get('name')) 
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $huser->get('uidNumber')) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
		}
	}

	if ($this->comment->isReported())
	{
		$comment = '<p class="warning">' . JText::_('COM_ANSWERS_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content('parsed');
	}

	$this->comment->set('category', 'answercomment');
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<a class="comment-anchor" name="c<?php echo $this->comment->get('id'); ?>"></a>
			<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, $this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
		<?php if (!$this->comment->isReported() && $this->comment->get('qid')) { ?>
			<p class="comment-voting voting" id="answers_<?php echo $this->comment->get('id'); ?>">
				<?php
				$view = new JView(array(
					'name'   => 'questions', 
					'layout' => 'rateitem'
				));
				$view->option = $this->option;
				$view->item   = $this->comment;
				$view->type   = 'question';
				$view->vote   = '';
				$view->id     = '';
				if (!$juser->get('guest')) 
				{
					if ($this->comment->get('created_by') == $juser->get('username')) 
					{
						$view->vote = $this->comment->get('vote');
						$view->id   = $this->comment->get('id');
					}
				}
				$view->display();
				?>
			</p><!-- / .comment-voting -->
		<?php } ?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong> 
				<a class="permalink" href="<?php echo JRoute::_($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo JText::_('COM_ANSWERS_PERMALINK'); ?>">
					<span class="comment-date-at">@</span> 
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span> 
					<span class="comment-date-on"><?php echo JText::_('COM_ANSWERS_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<?php echo $comment; ?>

			<p class="comment-options">
			<?php /*if ($this->config->get('access-edit-thread')) { // || $juser->get('id') == $this->comment->created_by ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($this->base . '&action=delete&comment=' . $this->comment->get('id')); ?>"><!-- 
						--><?php echo JText::_('COM_ANSWERS_DELETE'); ?><!-- 
					--></a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->base . '&action=edit&comment=' . $this->comment->get('id')); ?>"><!-- 
						--><?php echo JText::_('COM_ANSWERS_EDIT'); ?><!-- 
					--></a>
				<?php } ?>
			<?php }*/ ?>
			<?php if (!$this->comment->get('reports')) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (JRequest::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo JText::_('COM_ANSWERS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('COM_ANSWERS_REPLY'); ?>" href="<?php echo JRoute::_($this->comment->link()); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!-- 
					--><?php echo JText::_('COM_ANSWERS_CANCEL'); ?><!-- 
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo JText::_('COM_ANSWERS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('COM_ANSWERS_REPLY'); ?>" href="<?php echo JRoute::_($this->comment->link('reply')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!-- 
					--><?php echo JText::_('COM_ANSWERS_REPLY'); ?><!-- 
				--></a>
					<?php } ?>
				<?php } ?>
				<a class="icon-abuse abuse" href="<?php echo JRoute::_($this->comment->link('report')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!-- 
					--><?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?><!-- 
				--></a>
				<?php if ($juser->get('username') == $this->question->get('created_by') && $this->question->isOpen() && $this->comment->get('qid')) { ?>
					<a class="accept" href="<?php echo JRoute::_($this->comment->link('accept')); ?>"><?php echo JText::_('COM_ANSWERS_ACCEPT_ANSWER'); ?></a>
				<?php } ?>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (JRequest::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if ($juser->get('guest')) { ?>
				<p class="warning">
					<?php echo JText::sprintf('COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_($this->base, false, true))) . '">' . JText::_('COM_ANSWERS_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo JRoute::_($this->base); ?>" method="post" enctype="multipart/form-data">
					<a name="commentform<?php echo $this->comment->get('id'); ?>"></a>
					<fieldset>
						<legend><span><?php echo JText::sprintf('COM_ANSWERS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : JText::_('COM_ANSWERS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[category]" value="<?php echo $this->comment->get('category'); ?>" />
						<input type="hidden" name="comment[referenceid]" value="<?php echo $this->comment->get('id'); ?>" />
						<input type="hidden" name="comment[added]" value="" />
						<input type="hidden" name="comment[added_by]" value="<?php echo $juser->get('id'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="questions" />
						<input type="hidden" name="rid" value="<?php echo $this->question->get('id'); ?>" />
						<input type="hidden" name="task" value="savereply" />

						<?php echo JHTML::_('form.token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo JText::_('COM_ANSWERS_ENTER_COMMENTS'); ?></span>
							<?php
							ximport('Hubzero_Wiki_Editor');
							$editor = Hubzero_Wiki_Editor::getInstance();
							echo $editor->display('comment[comment]', 'comment_' . $this->comment->get('id') . '_content', '', 'minimal no-footer', '35', '4');
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo JText::_('COM_ANSWERS_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" /> 
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3)) 
		{
			$view = new JView(
				array(
					'name'    => 'questions',
					'layout'  => '_list'
				)
			);
			$view->parent     = $this->comment->get('id');
			$view->question   = $this->question;
			$view->option     = $this->option;
			$view->comments   = $this->comment->replies();
			$view->config     = $this->config;
			$view->depth      = $this->depth;
			$view->cls        = $cls;
			$view->base       = $this->base;
			$view->display();
		}
		?>
	</li>